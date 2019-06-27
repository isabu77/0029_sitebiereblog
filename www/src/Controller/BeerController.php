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
        $this->loadModel('users');
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
            $this->generateUrl('home')
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
        $user = $this->users->find($user->getId());

        if (!empty($post)) {
            // enregistremet de la commande
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
                    $priceTTC += $valueQty * $price * getenv('ENV_TVA');
                }
            }
            $FraisPort = 5.40;
            if ($priceTTC < 30) {
                $priceTTC += $FraisPort;
            } else {
                $priceTTC = 0.00;
            }

            //On convertit le tableau $qty en String pour l'envoyer en bdd plus tard.
            $serialCommande = serialize($qty);

            // créer l'objet
            $orderEntity = new OrdersEntity();
            $orderEntity->setIdUser($user->getId());
            $orderEntity->setPriceTTC($priceTTC);
            $orderEntity->setIdsProduct($serialCommande);

            // insérer l'objet en base
            $attributes = [
                "id_user"        => $orderEntity->getIdUser(),
                "ids_product"    => $orderEntity->getIdsProduct(),
                "priceTTC"        => $orderEntity->getPriceTTC()
            ];

            $result = $this->orders->insert($attributes);
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
            'user' => $user,
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
        // le client
        $user = $this->connectedSession();

        //On vérifie l'id de l'utilisateur
        //Et l'existence de la commande
        if (!$order || $order->getIdUser() != $user->getId()) {
            dd("user");
            header('location: /profil');
            exit();
        }

        $bieres = $this->beer->all();
        $beers = [];
        foreach ($bieres as $beer) {
            $beers[$beer->getId()] = $beer;
        }
        //dd($beers);

        // Rétablit le tableau à sa forme originale
        $lines = unserialize($order->getIdsProduct());
        $priceTTC = 0;
        foreach ($lines as $line) {
            $priceTTC += (float)(($line["price"] * $line["qty"]) * getenv('ENV_TVA'));
        }
        $FraisPort = 5.40;
        if ($priceTTC < 30) {
            $priceTTC += $FraisPort;
        } else {
            $FraisPort = 0.00;
        }

        //On vérifie le prix total TTC

        if (number_format($priceTTC, 2, ',', '.') != number_format($order->getPriceTTC(), 2, ',', '.')) {
            header('location: /profil');
            exit();
        }

        $title = 'Confirmation de commande';

        $this->render('beer/purchaseconfirm', [
            'tva' => getenv('ENV_TVA'),
            'user' => $user,
            'order' => $order,
            'fraisport' => $FraisPort,
            'bieres' => $beers,
            'lines' => $lines,
            'title' => $title
        ]);
    }
}
