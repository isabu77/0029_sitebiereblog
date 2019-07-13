<?php
namespace App\Controller\Admin;

use Core\Controller\Controller;
use Core\Controller\PaginatedQueryController;

class CategoryEditController extends Controller
{
    public function __construct()
    {
        $this->loadModel('post');
        $this->loadModel('category');
    }
    public function categoryEdit($post, $slug, $id)
    {
        $category = $this->category->find($id);
        if (!$category) {
            throw new Exception('Aucune categorie ne correspond à cet ID');
        }
        if ($category->getSlug() !== $slug) {
            $url = $this->generateUrl('admin_category_edit', ['id' => $id, 'slug' => $category->getSlug()]);
            http_response_code(301);
            header('Location: ' . $url);
            exit();
        }
        
        $paginatedQuery = new PaginatedQueryController(
            $this->post,
            $this->generateUrl('admin_category_edit', ["id" => $category->getId(), "slug" => $category->getSlug()])
        );
 
        $postById = $paginatedQuery->getItemsInId($id);
        $title = $category->getName();
        
        return $this->render("admin/category/categoryEdit", [
            "title" => $title,
            "category" => $category,
            "posts" => $postById
        ]);
    }
    public function categoryUpdate($post, $slug, $id)
    {
        $category = $this->category->find($id);
        $url = $this->generateUrl("admin_category_edit", ["id" => $category->getId(), "slug" => $category->getSlug()]);
        if (isset($post)) {
            $id = $post['cat_id'];

            if (!empty($post['cat_name'])) {
                $attributes["name"] =  htmlspecialchars($post['cat_name']);
            }
            if (!empty($post['cat_slug'])) {
                if (preg_match("#^[a-zA-Z0-9_-]*$#", $post['cat_slug'])) {
                    $attributes["slug"] =  htmlspecialchars($post['cat_slug']);
                }
            }
            $res = $this->category->update($id, $attributes);
            if ($res) {
                $_SESSION['success'] = "La catégorie a bien été modifiée";
            } else {
                $_SESSION['error'] = "La catégorie n'a pas été modifiée";
            }
        }
    }

    public function categoryInsert($post)
    {
        if (isset($post['name']) && !empty($post['name']) &&
            isset($post['slug']) && !empty($post['slug'])) {
            $slug = $this->category->findBy('slug', $post['slug'], true);
            if (!$slug) {
                if (preg_match("#^[a-zA-Z0-9_-]*$#", $post['slug'])) {
                    $attributes =
                    [
                        "name"     => htmlspecialchars($post['name']),
                        "slug"    => htmlspecialchars($post['slug'])
                    ];
                    $res = $this->category->insert($attributes);
                    if ($res) {
                        $_SESSION['success'] = "La catégorie a bien été ajoutée";
                    } else {
                        $_SESSION['error'] = "La catégorie n'a pas été ajoutée";
                    }
                }
            } else {
                $_SESSION['error'] = 'slug déjà existant';
                return $this->render("admin/category/categoryInsert", ["title" => "Ajouter une catégorie"]);
                unset($_SESSION['error']);
            }
        }
        return $this->render("admin/category/categoryInsert", [
            "title" => "Ajouter une catégorie"
        ]);
    }

    public function categoryDelete($post, $slug, $id)
    {
        $this->category->delete($id);
        header('location: /admin/categories');
    }
}
