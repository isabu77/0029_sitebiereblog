<?php
namespace App\Controller;

use \Core\Controller\Controller;
use App\Controller\PaginatedQueryAppController;

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
    }

    /**
     * la page d'accueil du site bière
     *      
     */
    public function index()
    {
        $title = 'Bread Beer Shop';

        $this->render('beer/index', [
            'connect' => false,
            'title' => $title
        ]);
    }

    /**
     * tous les produits bière
     */
    public function all()
    {
        // $this->beer contient une instance de la classe PostTable
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


}
