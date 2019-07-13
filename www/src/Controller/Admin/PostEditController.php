<?php
namespace App\Controller\Admin;

use \Core\Controller\Controller;
use \Core\Controller\PaginatedQueryController;

class PostEditController extends Controller
{
    public function __construct()
    {
        $this->loadModel('post');
        $this->loadModel('category');
        $this->loadModel('postCategory');
    }


    public function postEdit($post, $slug, $id)
    {
        $article = $this->post->find($id);
        if (!$article) {
            throw new \Exception('Aucun article ne correspond à cet ID');
        }
        if ($article->getSlug() !== $slug) {
            $url = $this->generateUrl('admin_posts_edit', ['id' => $id, 'slug' => $article->getSlug()]);
            http_response_code(301);
            header('Location: ' . $url);
            exit();
        }
        $categories = $this->category->allInId($article->getId());
        $allCategories = $this->category->allWithoutLimit();
        
        $title = $article->getName();
        
        return $this->render("admin/post/postsEdit", [
            "title" => $title,
            "categories" => $categories,
            "post" => $article,
            "allCategories" => $allCategories
        ]);
    }

    public function postUpdate($post, $slug, $id)
    {
        $article = $this->post->find($id);
        $url = $this->generateUrl('admin_posts_edit', ['id' => $id, 'slug' => $article->getSlug()]);
        if (isset($post)) {
            $attributes["name"] =  htmlspecialchars($post['post_name']);

            if (preg_match("#^[a-zA-Z0-9_-]*$#", $post['post_slug'])) {
                $attributes["slug"] =  $post['post_slug'];
            }
            $attributes["content"] =  htmlspecialchars($post['post_content']);

            $res = $this->post->update($id, $attributes);
            if ($res) {
                $_SESSION['success'] = "L'article a bien été modifié";
            } else {
                $_SESSION['error'] = "L'article n'a pas été modifié";
            }

            header('location: '.$url);
        }
    }

    public function postInsert($post)
    {
        
        if (isset($post['name']) && !empty($post['name']) &&
            isset($post['slug']) && !empty($post['slug']) &&
            isset($post['content']) && !empty($post['content'])) {
            $slug = $this->post->findBy('slug', $post['slug'], true);
            if (!$slug) {
                if (preg_match("#^[a-zA-Z0-9_-]*$#", $post['slug'])) {
                    $attributes =
                    [
                        "name"     => htmlspecialchars($post['name']),
                        "slug"    => htmlspecialchars($post['slug']),
                        "content"      => htmlspecialchars($post['content']),
                        "created_at"      => date("Y-m-d h:i:s")
                    ];
                    $res = $this->post->insert($attributes);
                    if ($res) {
                        $_SESSION['success'] = "L'article a bien été ajouté";
                    } else {
                        $_SESSION['error'] = "L'article n'a pas été ajouté";
                    }
                }
            } else {
                $_SESSION['error'] = 'slug déjà existant';
                $title = "Ajouter un article";
                $categories = $this->category->allWithoutLimit();
                return $this->render("admin/post/postInsert", ["title" => $title,"categories" => $categories]);
            }

            $categ = $this->category->allWithoutLimit();
            
            for ($i=1; $i <= count($categ); $i++) {
                if ($post[$i]) {
                    $post_id = $this->post->latestById()->getId();
                    $this->postCategory->insertPC($post_id, $i);
                }
            }
        }
        $categories = $this->category->allWithoutLimit();
        $title = "Ajouter un article";
        
        return $this->render("admin/post/postInsert", [
            "title" => $title,
            "categories" => $categories
        ]);
    }

    public function postDelete($post, $slug, $id)
    {
        $this->post->delete($id);
        header('location: /admin/posts');
    }
}
