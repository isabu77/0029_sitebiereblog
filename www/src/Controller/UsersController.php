<?php
namespace App\Controller;

use \Core\Controller\Controller;
use \Core\Controller\Helpers\MailController;
use \Core\Controller\Helpers\TextController;
use App\Model\Entity\UsersEntity;
use App\Model\Entity\ClientEntity;
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
        $this->loadModel('client');
        $this->loadModel('orders');
    }

    /**
     * reset du password par mail
     *
     */
    public function resetpwd($post = null)
    {
        if (isset($post["mail"]) && !empty($post["mail"])) {
            // vérifier l'existence du user en base
            $user = $this->users->getUserByMail($post["mail"]);
            if ($user) {
                // générer un nouveau mot de passe à sauvegarder dans la table users
                $passwordrdn = rand();
                $password = password_hash($passwordrdn, PASSWORD_BCRYPT);

                // modification des infos du user dans la base
                $res = $this->users->update($user->getId(), ["password" => $password]);
                if ($res) {
                    // envoyer nouveau mot de passe
                    $res = MailController::sendMail(
                        $post["mail"],
                        "Réinitialisation mdp",
                        "Le nouveau mot de passe est : " .  $passwordrdn
                    );
                    if ($res) {
                        $_SESSION['success'] = "Votre nouveau mot de passe vous a été envoyé par mail";
                        // Page de connexion
                        $title = 'Connexion';

                        $this->render('users/connexion', [
                            'user' => $post,
                            'title' => $title
                        ]);

                        unset($_SESSION["success"]); //Supprime la SESSION['success']
                        unset($_SESSION["error"]); //Supprime la SESSION['error']
                        return;
                    } else {
                        $_SESSION['error'] = "Erreur d'envoi du mail, recommencez.";
                    }
                } else {
                    $_SESSION['error'] = "Erreur de modification du mot de passe en base";
                }
            } else {
                $_SESSION['error'] = "Cet utilisateur n'existe pas. Recommencez.";
            }
        }
        $title = 'Réinitialisation du mot de passe';

        $this->render('users/resetpwd', [
            'user' => $post,
            'title' => $title
        ]);

        unset($_SESSION["success"]); //Supprime la SESSION['success']
        unset($_SESSION["error"]); //Supprime la SESSION['error']
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
                if ((filter_var($post["mail"], FILTER_VALIDATE_EMAIL) &&
                        $_POST["mail"] == $post["mailVerify"]) && ($_POST["password"] == $post["passwordVerify"])
                ) {
                    // créer l'objet users
                    $userEntity = new UsersEntity($post);

                    // vérifier l'existence du user en base
                    $user = $this->users->getUserByMail($userEntity->getMail());

                    if (!$user) {
                        // il n'existe pas : insertion en base
                        $userEntity->setPassword(password_hash(htmlspecialchars($post["password"]), PASSWORD_BCRYPT));
                        $token = TextController::randpwd(24);

                        // insérer l'objet en base dans la table users
                        $attributes =
                            [
                                "mail"         => htmlspecialchars($userEntity->getMail()),
                                "password"     => $userEntity->getPassword(),
                                "token"        => $token,
                                "verify"       => 0
                            ];

                        $userId = $this->users->insert($attributes);

                        if ($userId) {
                            // insérer l'objet en base dans la table clients

                            // créer l'objet client
                            $clientEntity = new ClientEntity($post);

                            $attributes =
                            [
                                "id_user"      => $userId,
                                "lastname"     => htmlspecialchars($clientEntity->getLastname()),
                                "firstname"    => htmlspecialchars($clientEntity->getFirstname()),
                                "address"      => htmlspecialchars($clientEntity->getAddress()),
                                "zipCode"      => htmlspecialchars($clientEntity->getZipCode()),
                                "city"         => htmlspecialchars($clientEntity->getCity()),
                                "country"      => htmlspecialchars($clientEntity->getCountry()),
                                "phone"        => htmlspecialchars($clientEntity->getPhone()),
                            ];
                            $clientId = $this->client->insert($attributes);

                            $user = $this->users->find($userId);

                            // envoyer le mail de confirmation
                            $texte = ["html" => '<h1>Bienvenue sur notre site Beer Shop'
                                . '</h1><p>Pour activer votre compte, veuillez cliquer sur le lien ci-dessous'
                                . ' ou copier/coller dans votre navigateur internet:</p><br />'
                                . '<a href="http://localhost/identification/verify/'
                                . $userId
                                . "&" . $user->getToken()
                                . '">Cliquez ICI pour valider votre compte</a><hr><p>Ceci est un mail automatique,'
                                . ' Merci de ne pas y répondre.</p>'];

                            $res = MailController::sendMail(
                                $_POST["mail"],
                                "Confirmation Inscription Beer Shop",
                                $texte
                            );
                            if ($res) {
                                $_SESSION['success'] =
                                    "Veuillez confirmer votre inscription "
                                    . "en cliquant sur le lien qui vous a été envoyé par mail";
                            } else {
                                $_SESSION['error'] = "Erreur d'envoi du mail de confirmation, recommencez.";
                            }
                        } else {
                            //signaler erreur
                            $_SESSION['error'] = "Erreur d'enregistrement en base, recommencez.";
                            header('Location: /inscription');
                            exit();
                        }
                    } else {
                        // connecter l'utilisateur
                        $this->connexion($post);
                        exit();
                    }
                } else {
                    if (!filter_var($post["mail"], FILTER_VALIDATE_EMAIL)) {
                        $_SESSION['error'] = "L'adresse mail est invalide.";
                    }
                    if ($_POST["mail"] !== $post["mailVerify"]) {
                        $_SESSION['error'] = "Les deux mails ne correspondent pas.";
                    }
                    if ($_POST["password"] !== $post["passwordVerify"]) {
                        $_SESSION['error'] = "Les deux mots de passe ne correspondent pas.";
                    }
                }
            }
        } else {
            // confirmation d'inscription
            if (
                isset($idUser) && !empty($idUser) &&
                isset($token) && !empty($token)
            ) {
                $user = $this->users->find($idUser);

                if ($user) {
                    if ($user->getToken() == $token) {
                        // validation en base
                        $res = $this->users->update($user->getId(), ["verify" => 1]);
                        if ($res) {
                            $_SESSION['success'] = 'Votre inscription est validée, vous pouvez vous connecter.';
                            // Page de connexion
                            $title = 'Connexion';

                            $this->render('users/connexion', [
                                'title' => $title
                            ]);

                            unset($_SESSION["success"]); //Supprime la SESSION['success']
                            unset($_SESSION["error"]); //Supprime la SESSION['error']
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
            'user' => $post,
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

            // vérifier l'existence du user en base
            $user = $this->users->getUserByMail($userEntity->getMail());
            // vérifier le mot de passe de l'objet en base
            if (
                $user  && !empty($userEntity->getPassword())
                && password_verify(htmlspecialchars($userEntity->getPassword()), $user->getPassword())
                && $user->getVerify()
            ) {
                // connecter l'utilisateur
                $user->setPassword("");
                parent::connectSession($user);
                header('Location: /profil');
                exit();
            } else {
                $_SESSION['auth'] = false;
                //signaler erreur
                if ($user && !$user->getVerify()) {
                    $_SESSION['error'] = "Votre inscription n'est pas validée, veuillez recommencer.";
                } else {
                    $_SESSION['error'] = "Adresse mail ou mot de passe invalide";
                }
            }
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
     * lecture d'un client par son id (appelée par javascript en ajax)
     *
     */
    public function getClient()
    {
        if (!empty($_POST)) {
            if (isset($_POST["idClient"])) {
                // lecture en base des clients du user
                $client = $this->client->find($_POST["idClient"]); 
               echo json_encode($client->get_properties()); 

            }
        }

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
                isset($post["delete"]) && !empty($post["delete"])
                && isset($post["id"]) && !empty($post["id"])
            ) {
                // suppression de la commande id
                $order = $this->orders->find($post["id"]);
                if ($order) {
                    $this->orders->delete($post["id"]);
                }
            } elseif (
                isset($post["passwordOld"]) && !empty($post["passwordOld"]) &&
                isset($post["password"]) && !empty($post["password"]) &&
                isset($post["passwordVerify"]) && !empty($post["passwordVerify"])
            ) {
                // vérifier l'existence du user en base
                $user = $this->users->getUserByMail($userConnect->getMail());
                // vérifier le mot de passe de l'objet en base
                if (
                    $user  && !empty($post["passwordOld"])
                    && password_verify(htmlspecialchars($post["passwordOld"]), $user->getPassword())
                    && $user->getVerify()
                ) {
                    if ($post["password"] == $post["passwordVerify"]) {
                        // modification du mot de passe en base
                        $password = password_hash(htmlspecialchars($post["password"]), PASSWORD_BCRYPT);

                        $res = $this->users->update($userConnect->getId(), ["password" => $password]);

                        if ($res) {
                            //message modif ok
                            $_SESSION['success'] = 'Votre mot de passe a bien été modifié';
                        } else {
                            $_SESSION['error'] = "Votre mot de passe n'a pas été modifié";
                        }
                    } else {
                        //mdp correspondent pas
                        $_SESSION['error'] = 'Les deux mots de passe ne correspondent pas.';
                    }
                } else {
                    //erreur
                    $_SESSION['error'] = 'Mot de passe incorrect';
                }
            } elseif (
                isset($post["lastname"]) && !empty($post["lastname"]) &&
                isset($post["firstname"]) && !empty($post["firstname"]) &&
                isset($post["address"]) && !empty($post["address"]) &&
                isset($post["zipCode"]) && !empty($post["zipCode"]) &&
                isset($post["city"]) && !empty($post["city"]) &&
                isset($post["country"]) && !empty($post["country"]) &&
                isset($post["phone"]) && !empty($post["phone"])
            ) {
                if ($userConnect) {
                    // update des coordonnées dans la table client
                    //$clients = $this->client->getClientsByUserId($userConnect->getId());
                    $client = $this->client->find($post["id"]);
                    $res = $this->client->update($client->getId(), $post);
                    if ($res) {
                        //message modif ok
                        $_SESSION['success'] = 'Votre profil a bien été modifié';
                    } else {
                        $_SESSION['error'] = "Votre profil n'a pas été modifié";
                    }
                }
            }
        }

        // lire les clients associés à l'utilisateur (plusieurs adresses)
        $clients = $this->client->getClientsByUserId($userConnect->getId());
        
        // les commandes du premier client associé à l'utilisateur
        $orders = $this->orders->allinId($clients[0]->getId());

        $title = 'Profil';

        $this->render('users/profil', [
            'user' => $clients[0],
            'clients' => $clients,
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
                isset($post["from"]) &&
                isset($post["object"]) &&
                isset($post["message"])
            ) {
                define('MAIL_TO', parent::getenv('GMAIL_USER'));
                define('MAIL_FROM', ''); // valeur par défaut
                define('MAIL_OBJECT', 'objet du message'); // valeur par défaut
                define('MAIL_MESSAGE', 'votre message'); // valeur par défaut
                // drapeau qui aiguille l'affichage du formulaire OU du récapitulatif
                $mailSent = false;
                // tableau des erreurs de saisie
                $errors = [];
                // si le courriel fourni est vide OU égale à la valeur par défaut
                $from = filter_input(INPUT_POST, 'from', FILTER_VALIDATE_EMAIL);
                if ($from === null || $from === MAIL_FROM) {
                    $errors[] = 'Vous devez renseigner votre adresse de courrier électronique.';
                    $_SESSION['error'] = 'Vous devez renseigner votre adresse de courrier électronique.';
                } elseif ($from === false) { // si le courriel fourni n'est pas valide
                    $errors[] = 'L\'adresse de courrier électronique n\'est pas valide.';
                    $from = filter_input(INPUT_POST, 'from', FILTER_SANITIZE_EMAIL);
                }
                $object = filter_input(
                    INPUT_POST,
                    'object',
                    FILTER_SANITIZE_STRING,
                    FILTER_FLAG_ENCODE_HIGH | FILTER_FLAG_ENCODE_LOW
                );
                // si l'objet fourni est vide, invalide ou égale à la valeur par défaut
                if ($object === null or $object === false or empty($object) or $object === MAIL_OBJECT) {
                    $errors[] = 'Vous devez renseigner l\'objet.';
                }
                $message = filter_input(INPUT_POST, 'message', FILTER_UNSAFE_RAW);
                // si le message fourni est vide ou égal à la valeur par défaut
                if ($message === null or $message === false or empty($message) or $message === MAIL_MESSAGE) {
                    $errors[] = 'Vous devez écrire un message.';
                }
                if (count($errors) === 0) { // si il n'y a pas d'erreur
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
                    $_SESSION['success'] = 'Votre message a bien été envoyé. Courriel pour la réponse :'
                        . $from . '. Objet : ' . $object . '. Message : ' . nl2br(htmlspecialchars($message));
                } else {
                    // le formulaire est affiché pour la première fois
                    // ou le formulaire a été soumis mais contenait des erreurs
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
