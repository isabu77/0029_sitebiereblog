<?php

namespace App\Controller;

use \Core\Controller\Controller;
use App\Controller\PaginatedQueryAppController;
use App\Model\Entity\OrdersEntity;

class BeerController extends Controller
{
    /**
     * constructeur
     */
    public function __construct()
    {
        // crée une instance de la classe BeerTable dans la propriété
        // $this->beer est créée dynamiquement
        $this->loadModel('beer');
        $this->loadModel('orders');
        $this->loadModel('orderline');
        $this->loadModel('users');
        $this->loadModel('client');
    }

    /**
     * la page d'accueil du site bière
     *
     */
    public function index()
    {
        $title = 'Welcome !';

        return $this->render('beer/index', [
            'title' => $title
        ]);
    }

    /**
     * les mentions légales du site bière
     *
     */
    public function mentions()
    {
        $title = 'Mentions légales';

        return $this->render('beer/mentions', [
            'title' => $title
        ]);
    }
    /**
     * les condions générales de vente du site bière
     *
     */
    public function cgv()
    {
        $title = 'Condions générales de vente';

        return $this->render('beer/cgv', [
            'title' => $title
        ]);
    }

    /**
     * tous les produits bière
     */
    public function all()
    {
        // $this->beer contient une instance de la classe BeerTable
        $paginatedQuery = new PaginatedQueryAppController(
            $this->beer,
            $this->generateUrl('boutique'),
            9
        );
        $bieres = $paginatedQuery->getItems();

        $title = 'Nos produits';

        // vérifier si une commande existe en session panier
        $orderlines = [];
        if (isset($_COOKIE[PANIER])) {
            $token = $_COOKIE[PANIER];
            if (!empty($token)) {
                // lecture en base des lignes de commande du token
                $orderlines = $this->orderline->allInToken($token);
            }
        }

        return $this->render('beer/all', [
            'bieres' => $bieres,
            'orderlines' => $orderlines,
            'paginate' => $paginatedQuery->getNavHTML(),
            'title' => $title
        ]);
    }

    /**
     * Ajout ou Modification d'une ligne dans le panier par jquery $.post
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
                    $orderlines = $this->orderline->allInToken($token);
                    foreach ($orderlines as $line) {
                        if ($line->getIdProduct() == $_POST["idBeer"]) {
                            $qty = $_POST["quantity"];

                            // demande d'ajout ou de modification de la quantité ?
                            if ($_POST["addqty"] == 'true') {
                                $qty += $line->getQuantity();
                            }

                            // le prix HT de la bière en base
                            $biere = $this->beer->find($line->getIdProduct());
                            if ($biere) {
                                $priceHT = $biere->getPrice();
                            } else {
                                $priceHT = $_POST["price"];
                            }

                            $priceTTC = $priceHT * $this->getApp()->getEnv('ENV_TVA');
                            //$priceHT = $_POST["price"];
                            $attributes = [
                                "quantity"        => $qty,
                                "priceHT"         => $priceHT,
                                "priceTTC"         => $priceTTC
                            ];
                            $result = $this->orderline->update($line->getId(), $attributes);
                            if ($result) {
                                // lecture en base des lignes de commande du token
                                $orderlines = $this->orderline->allInToken($token);
                                $total = 0;
                                foreach ($orderlines as $line) {
                                    // le prix HT de la bière en base
                                    $total += $line->getQuantity();
                                }
                                setcookie(QTYPANIER, $total, time() + 3600*48);

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
                    $priceHT = $biere->getPrice();
                } else {
                    $priceHT = $_POST["price"];
                }
                // insertion en base de la ligne panier
                $priceTTC = $priceHT * $this->getApp()->getEnv('ENV_TVA');
                //$priceHT = $_POST["price"];
                if (empty($token)) {
                    $token = substr(md5(uniqid()), 0, 24);
                }
                // le client connecté
                $user = $this->connectedSession();

                $attributes = [
                    "id_user"    => $user->getId(),
                    "id_product"    => $_POST["idBeer"],
                    "quantity"        => $_POST["quantity"],
                    "token"        => $token,
                    "priceHT"         => $priceHT,
                    "priceTTC"         => $priceTTC
                ];

                $result = $this->orderline->insert($attributes);
                if ($result) {
                    setcookie(PANIER, $token, time() + 3600*48);

                    // lecture en base des lignes de commande du token
                    $orderlines = $this->orderline->allInToken($token);
                    $total = 0;
                    foreach ($orderlines as $line) {
                        // le prix HT de la bière en base
                        $total += $line->getQuantity();
                    }
                    setcookie(QTYPANIER, $total, time() + 3600*48);
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
                    $orderlines = $this->orderline->allInToken($token);
                    foreach ($orderlines as $line) {
                        if ($line->getIdProduct() == $_POST["idBeer"]) {
                            $result = $this->orderline->delete($line->getId());
                            if ($result) {
                                // succès
                                $total = $_COOKIE[QTYPANIER] - $line->getQuantity();
                                setcookie(QTYPANIER, $total, time() + 3600*48);
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
     * Ajout par la page modale d'une ligne dans le panier par jquery $.post
     * total panier dans session["qtypanier"]
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
                    $orderlines = $this->orderline->allInToken($token);
                    foreach ($orderlines as $line) {
                        if ($line->getIdProduct() == $_POST["idBeer"]) {
                            $qty = $_POST["quantity"] + $line->getQuantity();

                            // le prix HT de la bière en base
                            $biere = $this->beer->find($line->getIdProduct());
                            if ($biere) {
                                $priceHT = $biere->getPrice();
                            } else {
                                $priceHT = $_POST["price"];
                            }

                            $priceTTC = $priceHT * $this->getApp()->getEnv('ENV_TVA');
                            //$priceHT = $_POST["price"];
                            $attributes = [
                                "quantity"        => $qty,
                                "priceHT"         => $priceHT,
                                "priceTTC"         => $priceTTC
                            ];
                            $result = $this->orderline->update($line->getId(), $attributes);
                            if ($result) {
                                // succès
                                $total = $_COOKIE[QTYPANIER] + $_POST["quantity"];
                                setcookie(QTYPANIER, $total, time() + 3600*48);
                                echo $total;
                                return;
                            }
                        }
                    }
                }
                // le prix HT de la bière en base
                $biere = $this->beer->find($_POST["idBeer"]);
                if ($biere) {
                    $priceHT = $biere->getPrice();

                    // insertion en base de la ligne panier
                    $priceTTC = $priceHT * $this->getApp()->getEnv('ENV_TVA');
                    //$priceHT = $_POST["price"];
                    if (empty($token)) {
                        $token = substr(md5(uniqid()), 0, 24);
                    }
                    // le client connecté
                    $user = $this->connectedSession();

                    $attributes = [
                        "id_user"    => $user->getId(),
                        "id_product"    => $_POST["idBeer"],
                        "quantity"        => $_POST["quantity"],
                        "token"        => $token,
                        "priceHT"         => $priceHT,
                        "priceTTC"         => $priceTTC
                    ];

                    $result = $this->orderline->insert($attributes);
                    if ($result) {
                        setcookie(PANIER, $token, time() + 3600*48);
                        // succès
                        $total = $_POST["quantity"];
                        if (isset($_COOKIE[QTYPANIER])) {
                            $total += $_COOKIE[QTYPANIER];
                        }
                        setcookie(QTYPANIER, $total, time() + 3600*48);
                        
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
        //$client = $this->client->find($user->getId());
        // lire les clients associés à l'utilisateur (plusieurs adresses)
        $clients = $this->client->getClientsByUserId($user->getId());


        // $this->beer contient une instance de la classe PostTable
        $paginatedQuery = new PaginatedQueryAppController(
            $this->beer,
            $this->generateUrl('cart')
        );
        $bieres = $paginatedQuery->getItems();
        $title = 'Votre panier';

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
            if (isset($post["lastname"]) && !empty($post["lastname"]) &&
                isset($post["firstname"]) && !empty($post["firstname"]) &&
                isset($post["address"]) && !empty($post["address"]) &&
                isset($post["zipCode"]) && !empty($post["zipCode"]) &&
                isset($post["city"]) && !empty($post["city"]) &&
                isset($post["country"]) && !empty($post["country"]) &&
                isset($post["phone"]) && !empty($post["phone"])
            ) {
                if ($post["new"] || $idClient == 0) {
                    // nouvelle adresse
                    unset($post["new"]);
                    $post["id_user"] = $user->getId();
                    $res = $this->client->insert($post);
                    if ($res) {
                        //message modif ok
                        $_SESSION['success'] = "l'adresse a bien été ajoutée";
                    } else {
                        $_SESSION['error'] = "l'adresse n'a pas été ajoutée";
                    }
                    $idClient = $this->client->last();
                } else {
                    // update du client dans la table client
                    $res = $this->client->update($idClient, $post);
                    if ($res) {
                        //message modif ok
                        $_SESSION['success'] = "l'adresse a bien été modifiée";
                    } else {
                        $_SESSION['error'] = "l'adresse n'a pas été modifiée";
                    }
                }
            }

            // relire le client dans la base
            $client = $this->client->find($idClient);

            // valider le panier enregistré dans la session et dans la table orderline
            if (isset($_COOKIE[PANIER])) {
                $token = $_COOKIE[PANIER];

                if (!empty($token)) {
                    // lecture en base des lignes de commande du token
                    $orderlines = $this->orderline->allInToken($token);
                    if (count($orderlines)) {
                        $priceHT = 0;
                        $priceTTC = 0;
                        foreach ($orderlines as $line) {
                            // le prix HT de la bière en base
                            $biere = $this->beer->find($line->getIdProduct());

                            $priceHT += $biere->getPrice() * $line->getQuantity();
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
                                "id_client"    => $idClient,
                                "priceHT"      => $priceHT,
                                "id_status"    => 1,
                                "priceTTC"     => $priceTTC
                            ];

                            $result = $this->orders->insert($attributes);
                            if ($result) {
                                // vider le panier
                                setcookie(PANIER, "", time() - 3600*24);
                                setcookie(QTYPANIER, 0, time() - 3600*24);
                                return $this->purchaseconfirm(null, $this->orders->last());
                                //header('Location: /purchaseconfirm/' . $result);
                                exit();
                            } else {
                                //TODO : signaler erreur
                                $_SESSION['error'] = "Erreur d'enregistrement de la commande dans la base";
                                //header('Location: /purchase');
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
                $orderlines = $this->orderline->allInToken($token);
                $total = 0;
                foreach ($orderlines as $line) {
                    // le prix HT de la bière en base
                    $total += $line->getQuantity();
                }
                setcookie(QTYPANIER, $total, time() + 3600*48);
            }
        }
        if ($idClient) {
            $client = $this->client->find($idClient);
        } else {
            if ($clients[0]) {
                $client = $this->client->find($clients[0]->getId());
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

    /**
     * commande des produits bière
     */
    public function purchase($post, int $idClient = null)
    {
        // le client connecté
        $user = $this->connectedSession();

        // prévoir plusieurs clients par user : afficher un select des clients associés
        // et prévoir un système pour ajouter un nouveau client (nouvelle adresse)
        //$client = $this->client->find($user->getId());
        // lire les clients associés à l'utilisateur (plusieurs adresses)
        $clients = $this->client->getClientsByUserId($user->getId());


        // $this->beer contient une instance de la classe PostTable
        $paginatedQuery = new PaginatedQueryAppController(
            $this->beer,
            $this->generateUrl('purchase')
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
            if (isset($post["lastname"]) && !empty($post["lastname"]) &&
                isset($post["firstname"]) && !empty($post["firstname"]) &&
                isset($post["address"]) && !empty($post["address"]) &&
                isset($post["zipCode"]) && !empty($post["zipCode"]) &&
                isset($post["city"]) && !empty($post["city"]) &&
                isset($post["country"]) && !empty($post["country"]) &&
                isset($post["phone"]) && !empty($post["phone"])
            ) {
                if (isset($post["new"]) || $idClient == 0) {
                    // nouvelle adresse
                    unset($post["new"]);
                    $post["id_user"] = $user->getId();
                    $res = $this->client->insert($post);
                    if ($res) {
                        //message modif ok
                        $_SESSION['success'] = "l'adresse a bien été ajoutée";
                    } else {
                        $_SESSION['error'] = "l'adresse n'a pas été ajoutée";
                    }
                    $idClient = $this->client->last();
                } else {
                    // update du client dans la table client
                    $res = $this->client->update($idClient, $post);
                    if ($res) {
                        //message modif ok
                        $_SESSION['success'] = "l'adresse a bien été modifiée";
                    } else {
                        $_SESSION['error'] = "l'adresse n'a pas été modifiée";
                    }
                }
            }

            // relire le client dans la base
            $client = $this->client->find($idClient);

            // valider le panier enregistré dans la session et dans la table orderline
            if (isset($_COOKIE[PANIER])) {
                $token = $_COOKIE[PANIER];

                if (!empty($token)) {
                    // lecture en base des lignes de commande du token
                    $orderlines = $this->orderline->allInToken($token);
                    if (count($orderlines)) {
                        $priceHT = 0;
                        $priceTTC = 0;
                        foreach ($orderlines as $line) {
                            // le prix HT de la bière en base
                            $biere = $this->beer->find($line->getIdProduct());

                            $priceHT += $biere->getPrice() * $line->getQuantity();
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
                                "id_client"    => $idClient,
                                "priceHT"      => $priceHT,
                                "id_status"    => 1,
                                "priceTTC"     => $priceTTC
                            ];

                            $result = $this->orders->insert($attributes);
                            if ($result) {
                                // vider le panier
                                setcookie(PANIER, "", time() - 3600*24);
                                setcookie(QTYPANIER, 0, time() - 3600*24);

                                return $this->purchaseconfirm(null, $this->orders->last());
                                //header('Location: /purchaseconfirm/' . $result);
                                exit();
                            } else {
                                //TODO : signaler erreur
                                $_SESSION['error'] = "Erreur d'enregistrement de la commande dans la base";
                                //header('Location: /purchase');
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
                $orderlines = $this->orderline->allInToken($token);
                $total = 0;
                foreach ($orderlines as $line) {
                    // le prix HT de la bière en base
                    $total += $line->getQuantity();
                }
                setcookie(QTYPANIER, $total, time() + 3600*48);
            }
        }
        if ($idClient) {
            $client = $this->client->find($idClient);
        } else {
            if ($clients[0]) {
                $client = $this->client->find($clients[0]->getId());
            }
        }

        return $this->render('beer/purchase', [
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
    public function purchaseconfirm($post, int $idOrder)
    {
        // la commande
        $order = $this->orders->find($idOrder);

        // le user connecté
        $user = $this->connectedSession();

        // le client associé à la commande
        $client = $this->client->find($order->getIdClient());

        //On vérifie l'id de l'utilisateur
        //Et l'existence de la commande
        if (!$order || $order->getIdClient() != $client->getId()) {
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
        $lines = $this->orderline->allInToken($order->getToken());

        // Rétablit le tableau à sa forme originale
        //$lines = unserialize($order->getIdsProduct());
        $priceTTC = 0;
        if (count($lines) == 0) {
            $_SESSION['error'] = "la commande est vide";
            header('location: /purchase');
            exit();
        }

        foreach ($lines as $line) {
            //$priceTTC  += (float) (($line["price"] * $line["qty"]) * $this->getApp()->getEnv('ENV_TVA'));
            $priceTTC  += (float) (($line->getPriceHT() * $line->getQuantity()) * $this->getApp()->getEnv('ENV_TVA'));
        }

        $FraisPort = PORT;
        if ($priceTTC < 30) {
            $priceTTC += $FraisPort;
        } else {
            $FraisPort = 0.00;
        }

        //On vérifie le prix total TTC

        /*         if (number_format($priceTTC, 2, ',', '.') != number_format($order->getPriceTTC(), 2, ',', '.')) {
            $_SESSION['error'] = "prix différents";
            header('location: /purchase');
            exit();
        }

 */
        $title = 'Confirmation de commande';

        return $this->render('beer/purchaseconfirm', [
            'tva' => $this->getApp()->getEnv('ENV_TVA'),
            'user' => $client,
            'order' => $order,
            'fraisport' => $FraisPort,
            'bieres' => $beers,
            'lines' => $lines,
            'title' => $title
        ]);
    }
}
