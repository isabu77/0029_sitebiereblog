<?php
namespace App\Controller;

use \Core\Controller\Controller;
use App\Controller\PaginatedQueryAppController;
use App\Model\Entity\OrdersEntity;

class BeerController extends Controller
{
    private $tva = 1.2;
    /**
     * constructeur
     */
    public function __construct()
    {
        // crée une instance de la classe BeerTable dans la propriété 
        // $this->beer est créée dynamiquement
        $this->loadModel('beer');
        $this->loadModel('orders');
    }

    /**
     * la page d'accueil du site bière
     *      
     */
    public function index()
    {
        $title = 'Bread Beer Shop';

        $this->render('beer/index', [
            'connect' => $this->userOnly(true),
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
            $this->generateUrl('home')
        );
        $bieres = $paginatedQuery->getItems();

        $title = 'Nos produits';

        $this->render('beer/all', [
            'connect' => $this->userOnly(true),
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
        if (!empty($post)) {
            // enregistremet de la commande

            // le client connecté
            $user = $this->userOnly(false);

            $beerArray = $this->beer->all();
            $beerTotal = [];
            foreach ($beerArray as $beer) {
                $beerTotal[$beer->getId()] = $beer;
            }
            $priceTTC = 0;
            foreach ($post['qty'] as $key => $valueQty) { //on boucle sur le tableau $_POST["qty"]
                if ($valueQty > 0) {
                    $price = $beerTotal[$key]->getPrice();
                    $qty[$key] = ['qty' => $valueQty, "price" => $price];
                    $priceTTC += $valueQty * $price * $this->tva;
                }
            }
            $serialCommande = serialize($qty); //On convertit le tableau $qty en String pour 												l'envoyer en bdd plus tard.

            // créer l'objet
            $orderEntity = new OrdersEntity();
            $orderEntity->setId_user($user->getId_user());
            $orderEntity->setPriceTTC($priceTTC);
            $orderEntity->setIds_product($serialCommande);

            // insérer l'objet en base
            $result = $this->orders->insert($orderEntity);
            if ($result) {
                header('Location: /purchaseconfirm/' . $result);
            } else {
                //TODO : signaler erreur
                header('Location: /purchase');
            }
            exit();
        }

        // $this->beer contient une instance de la classe PostTable
        $paginatedQuery = new PaginatedQueryAppController(
            $this->beer,
            $this->generateUrl('purchase')
        );
        $bieres = $paginatedQuery->getItems();

        $title = 'Bon de commande';

        $this->render('beer/purchase', [
            'user' => $this->userOnly(false),
            'bieres' => $bieres,
            'paginate' => $paginatedQuery->getNavHTML(),
            'title' => $title
        ]);
    }
    /**
     * confirmation de commande des produits bière
     */
    public function purchaseconfirm($post = null, int $idOrder)
    {
        // la commande
        $order = $this->orders->find($idOrder);
        // le client
        $user = $this->userOnly(false);
        //On vérifie l'id de l'utilisateur
        //Et l'existence de la commande
        if (!$order || $order->getId_user() != $user->getId_user()) {
            header('location: /profil');
            exit();
        }

        $bieres = $this->beer->all();
        $beers = [];
        foreach ($bieres as $beer) {
            $beers[$beer->getId()] = $beer;
        }

        // Rétablit le tableau à sa forme originale
        $lines = unserialize($order->getIds_product());
        $priceTTC = 0;
        foreach ($lines as $line) {
            $priceTTC += ($line["price"] * $line["qty"]) * $this->tva;
        }
        //On vérifie le prix total TTC
        if ($priceTTC !== $order->getPriceTTC()) {
            header('location: /profil');
            exit();
        }

        $title = 'Confirmation de commande';

        $this->render('beer/purchaseconfirm', [
            'connect' => $this->userOnly(true),
            'tva' => $this->tva,
            'user' => $user,
            'order' => $order,
            'bieres' => $bieres,
            'lines' => $lines,
            'title' => $title
        ]);
    }
}
