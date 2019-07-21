<?php

namespace App\Controller;

use \Core\Controller\Controller;
use App\Controller\PaginatedQueryAppController;
use \Core\Controller\FormController;

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
        $this->loadModel('orderLine');
    }

    /**
     * la page d'accueil du site bière
     *
     */
    public function index()
    {
        $title = 'Bienvenue !';

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
                $orderlines = $this->orderLine->allInToken($token);
            }
        }

        return $this->render('beer/all', [
            'bieres' => $bieres,
            'orderlines' => $orderlines,
            'paginate' => $paginatedQuery->getNavHTML(),
            'title' => $title
        ]);
    }
}
