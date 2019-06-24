<?php
namespace App\Controller;

use \Core\Controller\Controller;
use App\Controller\PaginatedQueryAppController;
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

                // en attendant : Boutique
                header('Location: /boutique');
                exit();
            } else {
                //TODO : signaler erreur
            }
            $title = 'Profil';
        } else
            $title = 'Inscription';

        $this->render('profil/inscription', [
            'connect' => false,
            'title' => $title
        ]);
    }


    /**
     * Connexion
     */
    public function connexion($post = null)
    {
        if (!empty($post)) {
            // créer l'objet
            $userEntity = new UsersEntity($post);

            // vérifier l'objet en base
            $result = $this->users->userConnect($userEntity->getMail(), $userEntity->getPassword(), true);
            if ($result) {
                // en attendant : Boutique
                header('Location: /boutique');
                exit();
            } else {
                //TODO : signaler erreur
            }
            $title = 'Profil';
        } else
        $title = 'Connexion';

        $this->render('profil/connexion', [
            'connect' => false,
            'title' => $title
        ]);
    }

    /**
     * verifie que l'utilisateur est connecté
     * @return array|void
     */
    public function userOnly($verify = false)
    { //:array|void|boolean
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
        // est pas defini et false
        if (!$_SESSION["auth"]) {
            if ($verify) {
                return false;
                //exit();
            }
            header('location: /connexion');
            exit();
        }
        return $_SESSION["auth"];
    }
}
