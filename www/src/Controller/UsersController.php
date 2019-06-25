<?php
namespace App\Controller;

use \Core\Controller\Controller;
use App\Model\Entity\UsersEntity;
use phpDocumentor\Reflection\Types\Boolean;

class UsersController extends Controller
{
    /**
     * constructeur
     */
    public function __construct()
    {
        // crée une instance de la classe UsersTable dans la propriété 
        // $this->users qui est créée dynamiquement
        $this->loadModel('users');
        $this->loadModel('orders');
    }

    /**
     * la page d'accueil du site bière
     *      
     */
    public function inscription($post = null)
    {
        if (!empty($post)) {
            // créer l'objet
            $post["password"] = password_hash(htmlspecialchars($post["password"]), PASSWORD_BCRYPT);
            $userEntity = new UsersEntity($post);

            // insérer l'objet en base
            $result = $this->users->insert($userEntity);
            if ($result) {
                // TODO : mail de confirmation

                // en attendant : profil
                header('Location: /profil');
            } else {
                //TODO : signaler erreur
                header('Location: /inscription');
            }
            exit();
        }

        $title = 'Inscription';

        $this->render('users/inscription', [
            'title' => $title
        ]);
    }

    /**
     * Connexion du site bière
     */
    public function connexion($post = null)
    {
        if (!empty($post)) {

            // créer l'objet users
            $userEntity = new UsersEntity($post);

            // vérifier le mot de passe de l'objet en base
            $user = $this->users->userConnect($userEntity->getMail(), $userEntity->getPassword(), false);
            if ($user) {
                $user->setPassword("");
                parent::connectSession($user);
                header('Location: /profil');
            } else {
                //TODO : signaler erreur
                header('Location: /connexion');
            }
            exit();
        }

        // Page de connexion
        $title = 'Connexion';

        $this->render('users/connexion', [
            'title' => $title
        ]);
    }

    /**
     * la déconnexion du site 
     *      
     */
    public function deconnexion()
    {
        parent::deconnectSession();
        header('Location: /');
        exit();
    }

    /**
     * verifie qu'un utilisateur est connecté
     * @return array|void
     */
    public function userOnly($verify = false): ?object
    {
        $user = parent::connectedSession();
        if (!$user) {
            if ($verify) {
                return null;
            }
            header('location: /connexion');
            exit();
        }
        return $user;
    }

    /**
     * la page profil du site bière
     *      
     */
    public function profil($post = null)
    {
        // le client connecté
        $userConnect = $this->userOnly(false);

        // traitement de la modification du profil
        if (!empty($post)) {
            if (
                isset($post["passwordOld"]) && !empty($post["passwordOld"]) &&
                isset($post["password"]) && !empty($post["password"]) &&
                isset($post["passwordVerify"]) && !empty($post["passwordVerify"]) &&
                isset($post["robot"]) && empty($post["robot"]) //protection robot
            ) {
                // vérifier le mot de passe en récupérant l'objet user
                $user = $this->users->userConnect($userConnect->getMail(), $post["passwordOld"], true);
                if ($user) {
                    if ($post["password"] == $post["passwordVerify"]) {
                        // modification du mot de passe en base
                        $password = password_hash(htmlspecialchars($post["password"]), PASSWORD_BCRYPT);

                        $res = $this->users->updatePassword($user, $password);
                        if ($res) {
                            //message modif ok
                            $_SESSION['success'] = 'Votre mot de passe a bien été modifié';
                        } else {
                            $_SESSION['error'] = "Votre mot de passe n'a pas été modifié";
                        }
                    } else {
                        //mdp correspondent pas
                        $_SESSION['error'] = 'Les deux mots de passes ne correspondent pas.';
                    }
                } else {
                    //erreur 
                    $_SESSION['error'] = 'Mot de passe incorrect';
                }
            } elseif (
                isset($post["lastname"]) && !empty($post["lastname"]) &&
                isset($post["firstname"]) && !empty($post["firstname"]) &&
                isset($post["address"]) && !empty($post["address"]) 
            ) {
                if (
                    isset($post["lastname"]) && !empty($post["lastname"]) &&
                    isset($post["firstname"]) && !empty($post["firstname"]) &&
                    isset($post["address"]) && !empty($post["address"]) &&
                    isset($post["zipCode"]) && !empty($post["zipCode"]) &&
                    isset($post["city"]) && !empty($post["city"]) &&
                    isset($post["country"]) && !empty($post["country"]) &&
                    isset($post["phone"]) && !empty($post["phone"])
                ) {
                    if ($userConnect) {
                        // update du user dans la table users
                        $res = $this->users->update($userConnect->getId(), $post);
                        if ($res) {
                            //message modif ok
                            $_SESSION['success'] = 'Votre profil a bien été modifié';
                            
                        } else {
                            dd($res);
                            $_SESSION['error'] = "Votre profil n'a pas été modifié";
                        }
                    }
                }
            }
        }

        $user = $this->users->find($userConnect->getId());

        // ses commandes
        $orders = $this->orders->allinId($userConnect->getId());

        $title = 'Profil';

        $this->render('users/profil', [
            'user' => $user,
            'orders' => $orders,
            'title' => $title
        ]);

        unset($_SESSION["success"]); //Supprime la SESSION['success']
        unset($_SESSION["error"]); //Supprime la SESSION['error']

    }
}
