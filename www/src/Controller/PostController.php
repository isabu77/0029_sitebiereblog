<?php
namespace App\Controller;

use \Core\Controller\Controller;
use App\Controller\PaginatedQueryAppController;

class PostController extends Controller
{
    /**
     * constructeur
     */
    public function __construct()
    {
        // crée une instance de la classe PostTable dans la propriété $this->post
        // $this->post est créée dynamiquement
        $this->loadModel('post');

        // $this->category est créée dynamiquement pour accéder aux méthodes de CategoryTable
        $this->loadModel('category');
    }

    /**
     * tous les articles
     */
    public function all()
    {
        //==============================  correction AFORMAC
        // $this->post contient une instance de la classe PostTable
        $paginatedQuery = new PaginatedQueryAppController(
            $this->post,
            $this->generateUrl('posts')
        );
        $postById = $paginatedQuery->getItems();

        $title = 'Posts';

        return $this->render('post/all', [
            'posts' => $postById,
            'paginate' => $paginatedQuery->getNavHTML(),
            'title' => $title
        ]);
    }

    /**
     * un seul article by 'lire plus'
     */
    public function show($post, string $slug, int $id)
    {
        // lecture de l'article dans la base (objet Post) par son id
        $post = $this->post->find($id);

        if (!$post) {
            throw new \exception("Aucun article ne correspond à cet Id");
        }

        // vérifier si on est sur le bon article avec le bon slug dans les paramètres de l'url demandée
        if ($post->getSlug() !== $slug) {
            $url = $this->generateUrl('post', ['id' => $id, 'slug' => $post->getSlug()]);
            // code 301 : redirection permanente pour le navigateur (de son cache, plus de requete au serveur)
            http_response_code(301);
            header('Location:' . $url);
            exit();
        }

        // les catégories de l'article par CategoryTable
        $post->setCategories($this->category->allInId($post->getId()));
        $title = $post->getName();

        // affichage HTML avec post/show.twig
        return $this->render('post/show', [
            'post' => $post,
            'title' => $title
        ]);
    }

    public function comment(string $slug, int $id)
    {
        if (empty($_POST)) {
            header('location: /posts');
        }
        
        if (isset($_POST['mail']) && !empty($_POST['mail']) &&
            isset($_POST['login']) && !empty($_POST['login']) &&
            isset($_POST['content']) && !empty($_POST['content']) &&
            isset($_POST['id']) && !empty($_POST['id'])) {
            $name = htmlspecialchars($_POST['login']);
            $content = htmlspecialchars($_POST['content']);
            $id_user = $_POST['id'];
            $verif = $this->user->exist($_POST["mail"]);
            if ($verif) {
                $this->comment->post($id, $id_user, $name, $content);
                $url = $this->generateUrl('post', ['id' => $id, 'slug' => $slug]);
                header('location: '.$url);
            } else {
                $_SESSION['error'] = 'Veuillez vous enregistrer';
                unset($_SESSION['error']);
            }
        } else {
            $_SESSION['error'] = 'Erreur';
            unset($_SESSION['error']);
        }
    }
}
