<?php
namespace App\Controller;

use \Core\Controller\Controller;
use \Core\Controller\Helpers\MailController;
use \Core\Controller\Helpers\TextController;
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
    public function inscription($post = null, $idUser = 0, $token = "", $createdAt = "")
    {
        if (!empty($post)) {
            if (
                isset($post["lastname"]) && !empty($post["lastname"]) &&
                isset($post["firstname"]) && !empty($post["firstname"]) &&
                isset($post["address"]) && !empty($post["address"]) &&
                isset($post["zipCode"]) && !empty($post["zipCode"]) &&
                isset($post["city"]) && !empty($post["city"]) &&
                isset($post["country"]) && !empty($post["country"]) &&
                isset($post["phone"]) && !empty($post["phone"]) &&
                isset($post["mail"]) && !empty($post["mail"]) &&
                isset($post["mailVerify"]) && !empty($post["mailVerify"]) &&
                isset($post["password"]) && !empty($post["password"]) &&
                isset($post["passwordVerify"]) && !empty($post["passwordVerify"])
            ) {
                if (
                    (filter_var($post["mail"], FILTER_VALIDATE_EMAIL) &&
                        $_POST["mail"] == $post["mailVerify"]) && ($_POST["password"] == $post["passwordVerify"])
                ) {
                    // créer l'objet users
                    $userEntity = new UsersEntity($post);

                    // vérifier l'existence du user en base
                    $user = $this->users->userConnect($userEntity->getMail(), $userEntity->getPassword(), false);

                    if (!$user) {
                        // il n'existe pas : insertion en base
                        $post["password"] = password_hash(htmlspecialchars($post["password"]), PASSWORD_BCRYPT);
                        $userEntity = new UsersEntity($post);

                        // insérer l'objet en base
                        $userId = $this->users->insert($userEntity);
                        if ($userId) {
                            // TODO : mail de confirmation
                            $user = $this->users->find($userId);
                            // envoyer le mail de confirmation
                            $texte = ["html" => '<h1>Bienvenue sur notre site Beer Shop</h1><p>Pour activer votre compte, veuillez cliquer sur le lien ci-dessous ou copier/coller dans votre navigateur internet:</p><br /><a href="http://localhost/identification/verify/'
                                . $userId
                                . "&" . $user->getToken()
                                . '">Cliquez ICI pour valider votre compte</a><hr><p>Ceci est un mail automatique, Merci de ne pas y répondre.</p>'];

                            $res = MailController::sendMail($_POST["mail"], "Confirmation Inscription Beer Shop",  $texte);
                            if ($res) {
                                $_SESSION['success'] = "Veuillez confirmer votre inscription en cliquant sur le lien qui vous a été envoyé par mail";
                            } else {
                                $_SESSION['error'] = "Erreur d'envoi du mail de confirmation, recommencez.";
                                //header('location: /inscription');
                            }
                        } else {
                            //TODO : signaler erreur
                            header('Location: /inscription');
                            exit();
                        }
                    } else {
                        $this->connexion($post);
                        exit();
                    }
                }
            }
        } else {

            if (
                isset($idUser) && !empty($idUser) &&
                isset($token) && !empty($token)
            ) {
                // confirmation d'inscription
                $user = $this->users->find($idUser);

                if ($user) {
                    if ($user->getToken() == $token) {
                        // validation en base
                        $res = $this->users->update($user->getId(), ["verify" => 1]);
                        if ($res) {
                            $_SESSION['success'] = 'Votre inscription est validée, vous pouvez vous connecter.';
                            header('location: /connexion');
                            exit();
                        } else {
                            $_SESSION['error'] = "Votre inscription n'est pas validée, veuillez recommencer.";
                        }
                    }
                } else {
                    $_SESSION['error'] = "Cet utilisateur n'existe pas, veuillez recommencer votre inscription.";
                }
            }
        }

        $title = 'Inscription';

        $this->render('users/inscription', [
            'title' => $title
        ]);
        
        unset($_SESSION["success"]); //Supprime la SESSION['success']
        unset($_SESSION["error"]); //Supprime la SESSION['error']
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

        unset($_SESSION["success"]); //Supprime la SESSION['success']
        unset($_SESSION["error"]); //Supprime la SESSION['error']
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

                        //$res = $this->users->updatePassword($user, $password);
                        $res = $this->users->update($userConnect->getId(), ["password" => $password]);
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

    /**
     * Contact
     */
    public function contact($post = null)
    {
        if (!empty($post)) {

            if (
                isset($_POST["send"]) &&
                isset($_POST["from"]) &&
                isset($_POST["object"]) &&
                isset($_POST["message"])
            ) {
                define('MAIL_TO', getenv('GMAIL_USER'));
                define('MAIL_FROM', ''); // valeur par défaut  
                define('MAIL_OBJECT', 'objet du message'); // valeur par défaut  
                define('MAIL_MESSAGE', 'votre message'); // valeur par défaut  
                // drapeau qui aiguille l'affichage du formulaire OU du récapitulatif  
                $mailSent = false;
                // tableau des erreurs de saisie  
                $errors = array();
                // si le courriel fourni est vide OU égale à la valeur par défaut  
                $from = filter_input(INPUT_POST, 'from', FILTER_VALIDATE_EMAIL);
                if ($from === NULL || $from === MAIL_FROM) {
                    $errors[] = 'Vous devez renseigner votre adresse de courrier électronique.';
                    $_SESSION['error'] = 'Vous devez renseigner votre adresse de courrier électronique.';
                } elseif ($from === false) // si le courriel fourni n'est pas valide  
                {
                    $errors[] = 'L\'adresse de courrier électronique n\'est pas valide.';
                    $from = filter_input(INPUT_POST, 'from', FILTER_SANITIZE_EMAIL);
                }
                $object = filter_input(INPUT_POST, 'object', FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_HIGH | FILTER_FLAG_ENCODE_LOW);
                // si l'objet fourni est vide, invalide ou égale à la valeur par défaut  
                if ($object === NULL or $object === false or empty($object) or $object === MAIL_OBJECT) {
                    $errors[] = 'Vous devez renseigner l\'objet.';
                }
                $message = filter_input(INPUT_POST, 'message', FILTER_UNSAFE_RAW);
                // si le message fourni est vide ou égal à la valeur par défaut  
                if ($message === NULL or $message === false or empty($message) or $message === MAIL_MESSAGE) {
                    $errors[] = 'Vous devez écrire un message.';
                }
                if (count($errors) === 0) // si il n'y a pas d'erreur  
                {
                    // tentative d'envoi du message  
                    if (MailController::sendMail(MAIL_TO, $object, $message, false, $from)) {
                        //if( mail( MAIL_TO, $object, $message, "From: $from\nReply-to: $from\n" ) ) 

                        $mailSent = true;
                    } else // échec de l'envoi  
                    {
                        $errors[] = 'Votre message n\'a pas été envoyé.';
                    }
                }
                // si le message a bien été envoyé, on affiche le récapitulatif  
                if ($mailSent === true) {
                    $_SESSION['success'] = 'Votre message a bien été envoyé. Courriel pour la réponse :' . $from . '. Objet : ' . $object . '. Message : ' . nl2br(htmlspecialchars($message));
                } else
                // le formulaire est affiché pour la première fois ou le formulaire a été soumis mais contenait des erreurs  
                {
                    if (count($errors) !== 0) {
                        $_SESSION['error'] = $errors;
                    } else {
                        $_SESSION['error'] = "Tous les champs sont obligatoires...";
                    }
                }
            }
        }

        $title = 'Contact';

        $this->render('users/contact', [
            'title' => $title
        ]);
    }
}
