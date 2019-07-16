<?php

namespace App\Controller;

use \Core\Controller\Controller;
use App\Controller\PaginatedQueryAppController;
use \App\Controller\UserInfosController;

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
    public function createOrderFromCart(int $user_Infos_id = null)
    {
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
                            "token"         => $token,
                            "user_infos_id" => $user_Infos_id,
                            "price_ht"      => $priceHT,
                            "status_id"     => 1,
                            "tva"           => TVA,
                            "port"          => $FraisPort
                        ];

                        $result = $this->order->insert($attributes);
                        if ($result) {
                            // vider le panier
                            setcookie(PANIER, "", time() - 3600 * 24);
                            setcookie(QTYPANIER, 0, time() - 3600 * 24);
                            return true;
                        } else {
                            //TODO : signaler erreur
                            $this->getFlashService()->addAlert("Erreur d'enregistrement de la commande dans la base");
                            return false;
                            //header('Location: /order');
                        }
                    }
                }
            }
        }
        return false;

    }

    /**
     * commande des produits bière
     */
    public function order($post, int $user_Infos_id = null)
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

            // enregistrement des coordonnées dans user_infos
            $userInfos = new UserInfosController();
            $user_Infos_id = $userInfos->updateProfil($post, $user->getId());

            // relire le client dans la base
            if ($user_Infos_id) {
                $client = $this->userInfos->find($user_Infos_id);

                // valider le panier enregistré dans la session et dans la table orderline
                if ($this->createOrderFromCart($user_Infos_id)){
                    return $this->orderconfirm(null, $this->order->last());
                    //header('Location: /orderconfirm/' . $result);
                    exit();
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
        if ($user_Infos_id) {
            $client = $this->userInfos->find($user_Infos_id);
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
            $this->getFlashService()->addAlert("Cette commande n'appartient pas à l'utilisateur connecté");
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
            $this->getFlashService()->addAlert("la commande est vide");
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
