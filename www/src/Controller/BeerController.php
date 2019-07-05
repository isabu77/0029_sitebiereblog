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

        $this->render('beer/index', [
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

        $this->render('beer/mentions', [
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

        $this->render('beer/cgv', [
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
            $this->generateUrl('boutique'),3
        );
        $bieres = $paginatedQuery->getItems();

        $title = 'Nos produits';

        $this->render('beer/all', [
            'bieres' => $bieres,
            'paginate' => $paginatedQuery->getNavHTML(),
            'title' => $title
        ]);
    }
    /**
     * commande des produits bière
     */
    public function purchase($post = null)
    {
        // le client connecté
        $user = $this->connectedSession();

        // prévoir plusieurs clients par user : afficher un select des clients associés
        // et prévoir un système pour ajouter un nouveau client (nouvelle adresse)
        $client = $this->client->find($user->getId());

        // $this->beer contient une instance de la classe PostTable
        $paginatedQuery = new PaginatedQueryAppController(
            $this->beer,
            $this->generateUrl('purchase')
        );
        $bieres = $paginatedQuery->getItems();
        $title = 'Bon de commande';

        if (!empty($_POST)) {
            //dd($_POST);
            if (isset($_POST["idClient"])) {
                // enregistrer le client dans la base
                $client = $this->client->find($_POST["idClient"]);

                // valider le panier enregistré dans la session et dans la table orderline
                if (isset($_SESSION["panier"])) {
                    $token = $_SESSION["panier"];

                    if (!empty($token)) {
                        // lecture en base des lignes de commande du token
                        $orderlines = $this->orderline->allInToken($token);
                        if (count($orderlines)){
                            $priceHT=0;
                            $priceTTC=0;
                            foreach ($orderlines as $line) {
                                // le prix HT de la bière en base
                                $biere = $this->beer->find($line->getIdProduct());

                                $priceHT += $biere->getPrice() * $line->getQuantity();
                            }
                            $priceTTC = $priceHT * parent::getenv('ENV_TVA');

                            if ($priceTTC > 0) {
                                $FraisPort = 5.40;
                                if ($priceTTC < 30) {
                                    $priceTTC += $FraisPort;
                                } else {
                                    $FraisPort = 0.00;
                                }
             
                                // créer la commande dans la table orders avec totaux et token des lignes
                                // créer l'objet
/*                                 $orderEntity =   new OrdersEntity();
                                $orderEntity->setIdClient($client->getId());
                                $orderEntity->setPriceHT($priceHT);
                                $orderEntity->setPriceTTC($priceTTC);
 */
                                // insérer l'objet en base
                                $attributes = [
                                    "token"        => $token,
                                    "id_client" => $client->getId(),
                                    "priceHT"        => $priceHT,
                                    "id_status"        => 1,
                                    "priceTTC"        => $priceTTC
                                ];

                                $result = $this->orders->insert($attributes);
                                if ($result) {
                                    // vider le panier
                                    unset($_SESSION["panier"]);
                                    return $this->purchaseconfirm(null, $result);
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
        }

        // vérifier si une commande existe en session panier
        if (isset($_SESSION["panier"])) {
            $token = $_SESSION["panier"];
            $orderlines = [];
            if (!empty($token)) {
                // lecture en base des lignes de commande du token
                $orderlines = $this->orderline->allInToken($token);
            }
        }
        $this->render('beer/purchase', [
            'user' => $user,
            'orderlines' => $orderlines,
            'client' => $client,
            'bieres' => $bieres,
            'paginate' => $paginatedQuery->getNavHTML(),
            'title' => $title
        ]);

        unset($_SESSION["error"]); //Supprime la SESSION['error']
    }

    /**
     * Ajout ou Modification d'une ligne dans le panier par jquery $.post
     * ajout si $_POST['addqty'] == 'true', modification sinon
     */
    public function updatecart()
    {
        if (!empty($_POST)) {
            if (isset($_POST["idBeer"]) && isset($_POST["quantity"]) && isset($_POST["price"])) {
                // gérer la ligne de panier
                // mise à jour du panier
                $token = "";
                // vérifier si une commande existe en session panier
                if (isset($_SESSION["panier"])) {
                    $token = $_SESSION["panier"];
                }
                $orderlines = [];
                if (!empty($token)) {
                    // lecture en base des lignes de commande du token
                    $orderlines = $this->orderline->allInToken($token);
                    foreach ($orderlines as $line) {
                        if ($line->getIdProduct() == $_POST["idBeer"]) {
                            $qty = $_POST["quantity"];

                            // demande d'ajout ou de modification de la quantité ?
                            if ($_POST["addqty"] == 'true'){
                                $qty += $line->getQuantity();
                            }

                            // le prix HT de la bière en base
                            $biere = $this->beer->find($line->getIdProduct());
                            if ($biere){
                                $priceHT = $biere->getPrice();
                            }else{
                                $priceHT = $_POST["price"];
                            }

                            $priceTTC = $priceHT * parent::getenv('ENV_TVA');
                            //$priceHT = $_POST["price"];
                            $attributes = [
                                "quantity"        => $qty,
                                "priceHT"         => $priceHT,
                                "priceTTC"         => $priceTTC
                            ];
                            $result = $this->orderline->update($line->getId(), $attributes);
                            if ($result) {
                                // succès
                                echo json_encode($attributes); 
                                return;
                            }
                        }
                    }
                }
                // le prix HT de la bière en base
                $biere = $this->beer->find($_POST["idBeer"]);
                if ($biere){
                    $priceHT = $biere->getPrice();
                }else{
                    $priceHT = $_POST["price"];
                }
                 // insertion en base de la ligne panier
                $priceTTC = $priceHT * parent::getenv('ENV_TVA');
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
                    $_SESSION["panier"] = $token;
                    setcookie("panier",$token,time()+10000);
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
                if (isset($_SESSION["panier"])) {
                    $token = $_SESSION["panier"];
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
                                echo "ok";
                                return;
                            }
                        }
                    }
                }
            }
        }
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
            //$priceTTC  += (float) (($line["price"] * $line["qty"]) * parent::getenv('ENV_TVA'));
            $priceTTC  += (float) (($line->getPriceHT() * $line->getQuantity()) * parent::getenv('ENV_TVA'));
        }

        $FraisPort = 5.40;
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

        $this->render('beer/purchaseconfirm', [
            'tva' => parent::getenv('ENV_TVA'),
            'user' => $client,
            'order' => $order,
            'fraisport' => $FraisPort,
            'bieres' => $beers,
            'lines' => $lines,
            'title' => $title
        ]);
    }
}
