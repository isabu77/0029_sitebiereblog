<?php

namespace App\Controller;

use \Core\Controller\Controller;
use \Core\Controller\Helpers\MailController;
use \Core\Controller\Helpers\TextController;
use App\Model\Entity\UserEntity;
use App\Model\Entity\UserInfosEntity;

class UsersController extends Controller
{
    /**
     * constructeur
     */
    public function __construct()
    {
        // crée une instance de la classe UserTable dans la propriété
        // $this->users qui est créée dynamiquement
        $this->loadModel('user');
        $this->loadModel('userInfos');
        $this->loadModel('order');
    }

    public function subscribe($post)
    {
        $form = new \Core\Controller\FormController();

        $errors = $form->hasErrors();
        if ($errors["post"] != "no-data") {
            $form->field('mail', ["require", "verify"]);
            $form->field('password', ["require", "verify", "length" => 8]);

            $datas = $form->getDatas();

            // vérifier que le mail n'existe pas en base
            //
        }
    }
    /**
     * reset du password par mail
     *
     */
    public function resetpwd($post)
    {
        if (isset($post["mail"]) && !empty($post["mail"])) {
            // vérifier l'existence du user en base
            $user = $this->user->getUserByMail($post["mail"]);
            if ($user) {
                // générer un nouveau mot de passe à sauvegarder dans la table users
                $passwordrdn = rand();
                $password = password_hash($passwordrdn, PASSWORD_BCRYPT);

                // modification des infos du user dans la base
                $res = $this->user->update($user->getId(), ["password" => $password]);
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

                        return $this->render('users/connexion', [
                            'user' => $post,
                            'title' => $title
                        ]);
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

        return $this->render('users/resetpwd', [
            'user' => $post,
            'title' => $title
        ]);
    }

    /**
     * la page d'accueil du site bière
     *
     */
    public function inscription($post, $idUser = 0, $token = "", $createdAt = "")
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
                        $post["mail"] == $post["mailVerify"]) && ($post["password"] == $post["passwordVerify"])
                ) {
                    // créer l'objet users
                    $userEntity = new UserEntity($post);

                    // vérifier l'existence du user en base
                    $user = $this->user->getUserByMail($userEntity->getMail());

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

                        $userId = $this->user->insert($attributes);

                        if ($userId) {
                            // insérer l'objet en base dans la table clients

                            // créer l'objet client
                            $clientEntity = new UserInfosEntity($post);

                            $attributes =
                                [
                                    "user_id"      => $userId,
                                    "lastname"     => htmlspecialchars($clientEntity->getLastname()),
                                    "firstname"    => htmlspecialchars($clientEntity->getFirstname()),
                                    "address"      => htmlspecialchars($clientEntity->getAddress()),
                                    "zip_code"      => htmlspecialchars($clientEntity->getZipCode()),
                                    "city"         => htmlspecialchars($clientEntity->getCity()),
                                    "country"      => htmlspecialchars($clientEntity->getCountry()),
                                    "phone"        => htmlspecialchars($clientEntity->getPhone())
                                ];
                            $clientId = $this->userInfos->insert($attributes);

                            $user = $this->user->find($userId);

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
                                $post["mail"],
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
                    if ($post["mail"] !== $post["mailVerify"]) {
                        $_SESSION['error'] = "Les deux mails ne correspondent pas.";
                    }
                    if ($post["password"] !== $post["passwordVerify"]) {
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
                $user = $this->user->find($idUser);

                if ($user) {
                    if ($user->getToken() == $token) {
                        // validation en base
                        $res = $this->user->update($user->getId(), ["verify" => 1]);
                        if ($res) {
                            $_SESSION['success'] = 'Votre inscription est validée, vous pouvez vous connecter.';
                            // Page de connexion
                            $title = 'Connexion';

                            return $this->render('users/connexion', [
                                'title' => $title
                            ]);
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

        return $this->render('users/inscription', [
            'user' => $post,
            'title' => $title
        ]);
    }

    /**
     * Connexion du site bière
     */
    public function connexion($post)
    {
        if (!empty($post)) {
            // créer l'objet users
            $userEntity = new UserEntity($post);

            // vérifier l'existence du user en base
            $user = $this->user->getUserByMail($userEntity->getMail());
            // vérifier le mot de passe de l'objet en base
            if (
                $user  && !empty($userEntity->getPassword())
                && password_verify(htmlspecialchars($userEntity->getPassword()), $user->getPassword())
                && $user->getVerify()
            ) {
                // connecter l'utilisateur
                $user->setPassword("");
                parent::connectSession($user);
                if ($user->getToken() === "ADMIN") {
                    header('Location: /admin');
                } else {
                    header('Location: /profil');
                }
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

        return $this->render('users/connexion', [
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
     * lecture d'un client par son id (appelée par javascript en ajax)
     *
     */
    public function getClient()
    {
        $post = $_POST;
        if (!empty($post)) {
            if (isset($post["idClient"])) {
                // lecture en base des clients du user
                $client = $this->userInfos->find($post["idClient"]);
                echo json_encode($client->getProperties());
            }
        }
    }

    /**
     * la page profil du site bière
     *
     */
    public function profil($post, int $idClient = null)
    {

        // le client connecté
        $userConnect = $this->userOnly(false);

        // traitement de la modification du profil
        if (!empty($post)) {
            if (
                isset($post["delete"])
                && isset($post["id"]) && !empty($post["id"])
            ) {
                // suppression du client id s'il n'a pas de commandes
                $orders = $this->order->allInId($post["id"]);
                if (!count($orders)) {
                    $this->userInfos->delete($post["id"]);
                    $_SESSION['success'] = "L'adresse a bien été supprimée.";
                    $idClient = null;
                } else {
                    $_SESSION['error'] = "Impossible de supprimer cette adresse car des commandes lui sont attachées.";
                }
            } elseif (
                isset($post["passwordOld"]) && !empty($post["passwordOld"]) &&
                isset($post["password"]) && !empty($post["password"]) &&
                isset($post["passwordVerify"]) && !empty($post["passwordVerify"])
            ) {
                // vérifier l'existence du user en base
                $user = $this->user->getUserByMail($userConnect->getMail());
                // vérifier le mot de passe de l'objet en base
                if (
                    $user  && !empty($post["passwordOld"])
                    && password_verify(htmlspecialchars($post["passwordOld"]), $user->getPassword())
                    && $user->getVerify()
                ) {
                    if ($post["password"] == $post["passwordVerify"]) {
                        // modification du mot de passe en base
                        $password = password_hash(htmlspecialchars($post["password"]), PASSWORD_BCRYPT);

                        $res = $this->user->update($userConnect->getId(), ["password" => $password]);

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
                    $idClient = 0;
                    if (isset($post["id"]) && is_numeric($post["id"])) {
                        $idClient = $post["id"];
                    }
                    unset($post["id"]);
                    $post["zip_code"] = $post["zipCode"];
                    unset($post["zipCode"]);
                    
                    if (isset($post["new"]) || $idClient == 0) {
                        // nouvelle adresse
                        unset($post["new"]);
                        $post["user_id"] = $userConnect->getId();
                        $res = $this->userInfos->insert($post);
                        if ($res) {
                            //message modif ok
                            $_SESSION['success'] = "l'adresse a bien été ajoutée";
                        } else {
                            $_SESSION['error'] = "l'adresse n'a pas été ajoutée";
                        }
                        $idClient = $this->userInfos->last();
                    } else {
                        // update du client dans la table client
                        unset($post["user_id"]);
                        $res = $this->userInfos->update($idClient, $post);
                        if ($res) {
                            //message modif ok
                            $_SESSION['success'] = "l'adresse a bien été modifiée";
                        } else {
                            $_SESSION['error'] = "l'adresse n'a pas été modifiée";
                        }
                    }
                }
            }
        }


        // lire les clients associés à l'utilisateur (plusieurs adresses)
        $clients = $this->userInfos->getUserInfosByUserId($userConnect->getId());

        // les commandes du client affiché
        $orders = [];
        if ($idClient) {
            $client = $this->userInfos->find($idClient);
        } else {
            if ($clients[0]) {
                $client = $this->userInfos->find($clients[0]->getId());
            }
        }
        if ($client) {
            $orders = $this->order->allinId($client->getId());
        }

        $title = 'Profil';

        return $this->render('users/profil', [
            'user'  => $client,
            'clients' => $clients,
            'orders' => $orders,
            'title' => $title
        ]);
    }

    /**
     * Contact
     */
    public function contact($post)
    {
        if (!empty($post)) {
            if (
                isset($post["from"]) &&
                isset($post["object"]) &&
                isset($post["message"])
            ) {
                define('MAIL_TO', $this->getApp()->getEnv('GMAIL_USER'));
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

        return $this->render('users/contact', [
            'title' => $title
        ]);
    }
}
