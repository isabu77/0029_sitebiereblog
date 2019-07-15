<?php

namespace App\Controller;

use \Core\Controller\Controller;
use App\Controller\PaginatedQueryAppController;
use \Core\Controller\FormController;

class CartController extends Controller
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
     * Ajout ou Modification d'une ligne par la page 'Bon de Commande'
     * dans le panier par jquery $.post
     * ajout si $_POST['addqty'] == 'true', modification sinon
     */
    public function updatecart()
    {
        if (!empty($_POST)) {
            if (isset($_POST["idBeer"]) && isset($_POST["quantity"])) {
                // gérer la ligne de panier
                // mise à jour du panier
                $token = "";
                // vérifier si une commande existe en session panier
                if (isset($_COOKIE[PANIER])) {
                    $token = $_COOKIE[PANIER];
                }
                $orderlines = [];
                if (!empty($token)) {
                    // lecture en base des lignes de commande du token
                    $orderlines = $this->orderLine->allInToken($token);
                    foreach ($orderlines as $line) {
                        if ($line->getBeerId() == $_POST["idBeer"]) {
                            $qty = $_POST["quantity"];

                            // demande d'ajout ou de modification de la quantité ?
                            if ($_POST["addqty"] == 'true') {
                                $qty += $line->getBeerQty();
                            }

                            // le prix HT de la bière en base
                            $biere = $this->beer->find($line->getBeerId());
                            if ($biere) {
                                $priceHT = $biere->getPriceHt();
                            } else {
                                $priceHT = $_POST["price"];
                            }

                            $attributes = [
                                "beer_qty"        => $qty,
                                "beer_price_ht"         => $priceHT
                            ];
                            $result = $this->orderLine->update($line->getId(), $attributes);
                            if ($result) {
                                // lecture en base des lignes de commande du token
                                $orderlines = $this->orderLine->allInToken($token);
                                $total = 0;
                                foreach ($orderlines as $line) {
                                    // le prix HT de la bière en base
                                    $total += $line->getBeerQty();
                                }
                                setcookie(QTYPANIER, $total, time() + 3600 * 48);

                                // succès
                                echo json_encode($attributes);
                                return;
                            }
                        }
                    }
                }
                // le prix HT de la bière en base
                $biere = $this->beer->find($_POST["idBeer"]);
                if ($biere) {
                    $priceHT = $biere->getPriceHt();
                } else {
                    $priceHT = $_POST["price"];
                }
                // insertion en base de la ligne panier
                if (empty($token)) {
                    $token = substr(md5(uniqid()), 0, 24);
                }
                // le client connecté
                $user = $this->connectedSession();

                $attributes = [
                    "user_id"    => $user->getId(),
                    "beer_id"    => $_POST["idBeer"],
                    "beer_qty"   => $_POST["quantity"],
                    "token"      => $token,
                    "beer_price_ht" => $priceHT
                ];

                $result = $this->orderLine->insert($attributes);
                if ($result) {
                    setcookie(PANIER, $token, time() + 3600 * 48);

                    // lecture en base des lignes de commande du token
                    $orderlines = $this->orderLine->allInToken($token);
                    $total = 0;
                    foreach ($orderlines as $line) {
                        // le prix HT de la bière en base
                        $total += $line->getBeerQty();
                    }
                    setcookie(QTYPANIER, $total, time() + 3600 * 48);
                    // succès
                    echo json_encode($attributes);
                    return;
                }
            }
        }
        return;
    }

    /**
     * vérification pour suppression dans le panier par jquery $.post
     */
    public function deletecart()
    {
        if (!empty($_POST)) {
            if (isset($_POST["idBeer"])) {
                // gérer la ligne de panier
                // mise à jour du panier
                $token = "";
                // vérifier si une commande existe en session panier
                if (isset($_COOKIE[PANIER])) {
                    $token = $_COOKIE[PANIER];
                }
                //var_dump($token);
                $orderlines = [];
                if (!empty($token)) {
                    // lecture en base des lignes de commande du token
                    $orderlines = $this->orderLine->allInToken($token);
                    foreach ($orderlines as $line) {
                        if ($line->getBeerId() == $_POST["idBeer"]) {
                            $result = $this->orderLine->delete($line->getId());
                            if ($result) {
                                // succès
                                $total = $_COOKIE[QTYPANIER] - $line->getBeerQty();
                                setcookie(QTYPANIER, $total, time() + 3600 * 48);
                                echo $total;
                                return;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Ajout par la page modale dans la Boutique 
     * d'une ligne dans le panier par jquery $.post
     * total panier dans $_COOKIE["qtypanier"]
     */
    public function addToCart()
    {
        if (!empty($_POST)) {
            if (isset($_POST["idBeer"]) && isset($_POST["quantity"])) {
                // gérer la ligne de panier
                // mise à jour du panier
                $token = "";
                // vérifier si une commande existe en session panier
                if (isset($_COOKIE[PANIER])) {
                    $token = $_COOKIE[PANIER];
                }
                $orderlines = [];
                if (!empty($token)) {
                    // lecture en base des lignes de commande du token
                    $orderlines = $this->orderLine->allInToken($token);
                    foreach ($orderlines as $line) {
                        if ($line->getBeerId() == $_POST["idBeer"]) {
                            $qty = $_POST["quantity"] + $line->getBeerQty();

                            // le prix HT de la bière en base
                            $biere = $this->beer->find($line->getBeerId());
                            if ($biere) {
                                $priceHT = $biere->getPriceHt();
                            } else {
                                $priceHT = $_POST["price"];
                            }

                            $attributes = [
                                "beer_qty"        => $qty,
                                "beer_price_ht"   => $priceHT
                            ];
                            $result = $this->orderLine->update($line->getId(), $attributes);
                            if ($result) {
                                // succès
                                $total = $_COOKIE[QTYPANIER] + $_POST["quantity"];
                                setcookie(QTYPANIER, $total, time() + 3600 * 48);
                                echo $total;
                                return;
                            }
                        }
                    }
                }
                // le prix HT de la bière en base
                $biere = $this->beer->find($_POST["idBeer"]);
                if ($biere) {
                    // insertion en base de la ligne panier
                    if (empty($token)) {
                        $token = substr(md5(uniqid()), 0, 24);
                    }
                    // le client connecté
                    $user = $this->connectedSession();

                    $attributes = [
                        "user_id"    => $user->getId(),
                        "beer_id"    => $biere->getId(),
                        "beer_qty"   => $_POST["quantity"],
                        "token"      => $token,
                        "beer_price_ht" => $biere->getPriceHt()
                    ];

                    $result = $this->orderLine->insert($attributes);
                    if ($result) {
                        setcookie(PANIER, $token, time() + 3600 * 48);
                        // succès
                        $total = $_POST["quantity"];
                        if (isset($_COOKIE[QTYPANIER])) {
                            $total += $_COOKIE[QTYPANIER];
                        }
                        setcookie(QTYPANIER, $total, time() + 3600 * 48);

                        echo $total;
                        return;
                    }
                }
            }
        }
        return;
    }
    
    /**
     * panier des produits bière
     */
    public function cart($post)
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
            $this->generateUrl('cart')
        );
        $bieres = $paginatedQuery->getItems();
        $title = 'Votre panier';

        if (!empty($post)) {

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

                    if ($post["new"] || $idClient == 0) {
                        // nouvelle adresse
                        unset($post["new"]);
                        $datas["user_id"] = $user->getId();
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
                                //return $this->orderconfirm(null, $this->order->last());
                                header('Location: /orderconfirm/' . $this->order->last());
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
        return $this->render('beer/cart', [
            'user' => $user,
            'orderlines' => $orderlines,
            'clients' => $clients,
            'client' => $client,
            'bieres' => $bieres,
            'paginate' => $paginatedQuery->getNavHTML(),
            'title' => $title
        ]);
    }
}