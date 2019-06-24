<?php
namespace App\Controller;

use \Core\Controller\Controller;
use App\Controller\PaginatedQueryAppController;

class ContactController extends Controller
{
    /**
     * constructeur
     */
    public function __construct()
    {
        // crée une instance de la classe BeerTable dans la propriété 
        // $this->beer est créée dynamiquement
        //$this->loadModel('beer');
    }

    /**
     * la page de contact du site bière
     *      
     */
    public function index()
    {
        $title = 'Contact';

        $this->render('contact/index', [
            'connect' => $this->userOnly(true),
            'title' => $title
        ]);
    }

    
}
