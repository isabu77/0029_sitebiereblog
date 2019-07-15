<?php

namespace App\Controller;

use \Core\Controller\Controller;
use \Core\Controller\Helpers\TextController;
use \Core\Controller\FormController;

class UserInfosController extends Controller
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
        // l'utilisateur connecté
        $userConnect = $this->connectedSession();
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
                    $_SESSION['success'] = "Les coordonnées ont bien été supprimées.";
                    $idClient = null;
                } else {
                    $_SESSION['error'] = "Impossible de supprimer ces coordonnées car des commandes leur sont attachées.";
                }
            } elseif (
                isset($post["passwordOld"]) && !empty($post["passwordOld"]) &&
                isset($post["password"]) && !empty($post["password"]) &&
                isset($post["passwordVerify"]) && !empty($post["passwordVerify"])
            ) {
                // traitement du formulaire de changement de password
                $form = new FormController();
                $errors = $form->hasErrors();
                if ($errors["post"] != "no-data") {
                    $form->field('password', ["require", "verify"]);
                    $form->field('passwordOld', ["require"]);
                    $errors = $form->hasErrors();
                    if (empty($errors)) {
                        $datas = $form->getDatas();

                        // vérifier l'existence du user en base
                        $user = $this->user->getUserByMail($userConnect->getMail());
                        if (
                            $user
                            && password_verify(htmlspecialchars($post["passwordOld"]), $user->getPassword())
                            && $user->getVerify()
                        ) {


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
                }
            } elseif (
                isset($post["id"]) && !empty($post["id"])
                && $userConnect != null
            ) {
                // traitement du formulaire
                $form = new FormController();
                $errors = $form->hasErrors();
                if ($errors["post"] != "no-data") {
                    $form->field('lastname', ["require"]);
                    $form->field('firstname', ["require"]);
                    $form->field('address', ["require"]);
                    $form->field('zipCode', ["require"]);
                    $form->field('city', ["require"]);
                    $form->field('country', ["require"]);
                    $form->field('phone', ["require"]);
                    $errors = $form->hasErrors();
                    if (empty($errors)) {
                        $datas = $form->getDatas();

                        $idClient = $post["id"];
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
                                $_SESSION['success'] = "les coordonnées ont bien été ajoutées";
                            } else {
                                $_SESSION['error'] = "les coordonnées n'ont pas été ajoutées";
                            }
                            $idClient = $this->userInfos->last();
                        } else {
                            // update du client dans la table client
                            unset($post["user_id"]);
                            $res = $this->userInfos->update($idClient, $post);
                            if ($res) {
                                //message modif ok
                                $_SESSION['success'] = "les coordonnées ont bien été modifiées";
                            } else {
                                $_SESSION['error'] = "les coordonnées n'ont pas été modifiées";
                            }
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

}