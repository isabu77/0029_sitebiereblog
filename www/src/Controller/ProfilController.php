<?php
namespace App\Controller;

use \Core\Controller\Controller;
use App\Model\Entity\UsersEntity;
use phpDocumentor\Reflection\Types\Boolean;

class ProfilController extends Controller
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

        $this->render('profil/inscription', [
            'connect' => $this->userOnly(true),
            'title' => $title
        ]);
    }


    /**
     * Connexion du site bière
     */
    public function connexion($post = null)
    {
        if (!empty($post)) {
            // créer l'objet
            $userEntity = new UsersEntity($post);

            // vérifier le mot de passe de l'objet en base
            $result = $this->users->userConnect($userEntity->getMail(), $userEntity->getPassword(), false);
            if ($result) {
                header('Location: /profil');
            } else {
                //TODO : signaler erreur
                header('Location: /connexion');
            }
            exit();
        }

        $title = 'Connexion';

        $this->render('profil/connexion', [
            'connect' => $this->userOnly(true),
            'title' => $title
        ]);
    }
    /**
     * la déconnexion du site bière
     *      
     */
    public function deconnexion($post = null)
    {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
        unset($_SESSION["auth"]);
        header('Location: /');
        exit();
    }
    
    /**
     * la page profil du site bière
     *      
     */
    public function profil($post = null)
    {
        $user = $this->userOnly(false);

        $orders = $this->orders->allinId($user->getId_user());

        if (!empty($post)) {
            // créer l'objet
            $userEntity = new UsersEntity($post);

            // modifier l'objet en base
            /*             $result = $this->users->update($userEntity);
            if ($result) {
  
               header('Location: /profil');
                exit();
            } else {
                //TODO : signaler erreur
            }
 */
        } 
            
        $title = 'Profil';

        $this->render('profil/profil', [
            'connect' => $this->userOnly(true),
            'user' => $user,
            'orders' => $orders,
            'title' => $title
        ]);
    }
}
