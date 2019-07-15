<?php

namespace App\Controller;

use \Core\Controller\Controller;
use App\Controller\PaginatedQueryAppController;
use \Core\Controller\FormController;

class OrderController extends Controller
{
    /**
     * constructeur
     */
    public function __construct()
    {
        // crée une instance de la classe BeerTable dans la propriété
        // $this->beer est créée dynamiquement
        $this->loadModel('beer');
        $this->loadModel('order');
        $this->loadModel('orderLine');
        $this->loadModel('user');
        $this->loadModel('userInfos');
        //dd($this);
    }

    /**
     * commande des produits bière
     */
    public function order($post, int $idClient = null)
    {
        // le client connecté
        $user = $this->connectedSession();

        // prévoir plusieurs clients par user : afficher un select des clients associés
        // et prévoir un système pour ajouter un nouveau client (nouvelle adresse)
        //$client = $this->userInfos->find($user->getId());
        // lire les clients associés à l'utilisateur (plusieurs adresses)
        $clients = $this->userInfos->getUserInfosByUserId($user->getId());


        // $this->beer contient une instance de la classe PostTable
        $paginatedQuery = new PaginatedQueryAppController(
            $this->beer,
            $this->generateUrl('order')
        );
        $bieres = $paginatedQuery->getItems();
        $title = 'Bon de commande';

        if (!empty($post)) {
            $price = $post["price"];
            $idBeer = $post["id"];
            $idClient = 0;
            if (isset($post["idClient"]) && is_numeric($post["idClient"])) {
                $idClient = $post["idClient"];
            }
            unset($post["idClient"]);
            unset($post["price"]);
            unset($post["id"]);
            // enregistrement de l'adresse du client
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

                    $datas["zip_code"] = $datas["zipCode"];
                    unset($datas["zipCode"]);
                    if (isset($post["new"]) || $idClient == 0) {
                        // nouvelle adresse
                        unset($post["new"]);
                        $post["user_id"] = $user->getId();
                        $res = $this->userInfos->insert($datas);
                        if ($res) {
                            //message modif ok
                            $_SESSION['success'] = "l'adresse a bien été ajoutée";
                        } else {
                            $_SESSION['error'] = "l'adresse n'a pas été ajoutée";
                        }
                        $idClient = $this->userInfos->last();
                    } else {
                        // update du client dans la table client
                        $res = $this->userInfos->update($idClient, $datas);
                        if ($res) {
                            //message modif ok
                            $_SESSION['success'] = "l'adresse a bien été modifiée";
                        } else {
                            $_SESSION['error'] = "l'adresse n'a pas été modifiée";
                        }
                    }
                }

                // relire le client dans la base
                $client = $this->userInfos->find($idClient);

                // valider le panier enregistré dans la session et dans la table orderline
                if (isset($_COOKIE[PANIER])) {
                    $token = $_COOKIE[PANIER];

                    if (!empty($token)) {
                        // lecture en base des lignes de commande du token
                        $orderlines = $this->orderLine->allInToken($token);
                        if (count($orderlines)) {
                            $priceHT = 0;
                            $priceTTC = 0;
                            foreach ($orderlines as $line) {
                                // le prix HT de la bière en base
                                $biere = $this->beer->find($line->getBeerId());

                                $priceHT += $biere->getPriceHt() * $line->getBeerQty();
                            }
                            $priceTTC = $priceHT * TVA;

                            if ($priceTTC > 0) {
                                $FraisPort = PORT;
                                if ($priceTTC < SHIPLIMIT) {
                                    $priceTTC += $FraisPort;
                                } else {
                                    $FraisPort = 0.00;
                                }
                                // créer la commande dans la table orders avec totaux et token des lignes

                                // insérer l'objet en base
                                $attributes = [
                                    "token"        => $token,
                                    "user_infos_id"    => $idClient,
                                    "price_ht"      => $priceHT,
                                    "status_id"    => 1,
                                    "tva"     => TVA,
                                    "port"  => $FraisPort
                                ];

                                $result = $this->order->insert($attributes);
                                if ($result) {
                                    // vider le panier
                                    setcookie(PANIER, "", time() - 3600 * 24);
                                    setcookie(QTYPANIER, 0, time() - 3600 * 24);

                                    return $this->orderconfirm(null, $this->order->last());
                                    //header('Location: /orderconfirm/' . $result);
                                    exit();
                                } else {
                                    //TODO : signaler erreur
                                    $_SESSION['error'] = "Erreur d'enregistrement de la commande dans la base";
                                    //header('Location: /order');
                                }
                            }
                        }
                    }
                }
            }
        }
        // vérifier si une commande existe en session panier
        $orderlines = [];
        if (isset($_COOKIE[PANIER])) {
            $token = $_COOKIE[PANIER];
            if (!empty($token)) {
                // lecture en base des lignes de commande du token
                $orderlines = $this->orderLine->allInToken($token);
                $total = 0;
                foreach ($orderlines as $line) {
                    // le prix HT de la bière en base
                    $total += $line->getBeerQty();
                }
                setcookie(QTYPANIER, $total, time() + 3600 * 48);
            }
        }
        if ($idClient) {
            $client = $this->userInfos->find($idClient);
        } else {
            if ($clients[0]) {
                $client = $this->userInfos->find($clients[0]->getId());
            }
        }

        return $this->render('beer/order', [
            'user' => $user,
            'orderlines' => $orderlines,
            'client' => $client,
            'clients' => $clients,
            'bieres' => $bieres,
            'paginate' => $paginatedQuery->getNavHTML(),
            'title' => $title
        ]);
    }

    /**
     * confirmation de commande des produits bière
     */
    public function orderconfirm($post, int $idOrder)
    {
        // la commande
        $order = $this->order->find($idOrder);

        // le user connecté
        $user = $this->connectedSession();

        // le client associé à la commande
        $client = $this->userInfos->find($order->getUserInfosId());

        //On vérifie l'id de l'utilisateur
        //Et l'existence de la commande
        if (!$order || $order->getUserInfosId() != $client->getId()) {
            $_SESSION['error'] = "Cette commande n'appartient pas à l'utilisateur connecté";
            header('location: /profil');
            exit();
        }

        $bieres = $this->beer->all();
        $beers = [];
        foreach ($bieres as $beer) {
            $beers[$beer->getId()] = $beer;
        }

        // lecture en base des lignes de commande du token
        $lines = $this->orderLine->allInToken($order->getToken());

        // Rétablit le tableau à sa forme originale
        //$lines = unserialize($order->getIdsProduct());
        $priceTTC = 0;
        if (count($lines) == 0) {
            $_SESSION['error'] = "la commande est vide";
            header('location: /order');
            exit();
        }

        foreach ($lines as $line) {
            $priceTTC  += (float) (($line->getPriceHT() * $line->getBeerQty()) * $order->getTva());
        }

        $priceTTC += $order->getPort();

        $title = 'Confirmation de commande';

        return $this->render('beer/orderconfirm', [
            'tva' => $order->getTva(),
            'user' => $client,
            'order' => $order,
            'fraisport' => $order->getPort(),
            'bieres' => $beers,
            'lines' => $lines,
            'title' => $title
        ]);
    }

}