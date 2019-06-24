<?php
namespace App\Controller;
use \Core\Controller\Controller;
use App\Controller\PaginatedQueryAppController;

class CategoryController extends Controller
{
    /**
     * constructeur par Correction AFORMAC
     */
    public function __construct()
    {
        // crée une instance de la classe PostTable dans la propriété $this->post
        // $this->category est créée dynamiquement
        $this->loadModel('category');    
        
        // $this->post est créée dynamiquement pour accéder aux méthodes de PostTable
        // via PaginatedQueryController pour afficher les posts d'une catégorie
        $this->loadModel('post');
    }

    /**
     * toutes les catégories
     */
    public function all()
    {
        //==============================  correction AFORMAC
        // $this->post contient une instance de la classe PostTable
        $paginatedQuery = new PaginatedQueryAppController(
            $this->category,
            $this->generateUrl('categories')
        );
        $categories = $paginatedQuery->getItems();

        $title = "Catégories";

        // affichage HTML avec category/all.twig
        $this->render('category/all', [
            'categories' => $categories,
            'paginate' => $paginatedQuery->getNavHTML(),
            'title' => $title
        ]);
    }

    /**
     * une seule catégorie et ses articles
     */
    public function show($post = null, string $slug, int $id)
    {
        // méthode générique de table.php
        $category = $this->category->find($id);

        if (!$category) {
            throw new \exception("Aucune catégorie ne correspond à cet Id");
        }
        if ($category->getSlug() !== $slug) {
            $url = $this->generateUrl('category', ['id' => $id, 'slug' => $category->getSlug()]);
            // code 301 : redirection permanente pour le navigateur (de son cache, plus de requete au serveur)
            http_response_code(301);
            header('Location:' . $url);
            exit();
        }

        $title = 'Catégorie : ' . $category->getName();

        // les articles de la catégorie : 
        // $this->post doit etre créé par loadModel dans le constructeur
        $paginatedQuery = new PaginatedQueryAppController(
            $this->post,
            $this->generateUrl('category', ["id" => $category->getId(), "slug" => $category->getSlug()])
        );
        $postById = $paginatedQuery->getItemsInId($id);

        $this->render(
            "category/show",
            [
                "title" => $title,
                "posts" => $postById,
                "paginate" => $paginatedQuery->getNavHTML()
            ]
        );

    }
}
