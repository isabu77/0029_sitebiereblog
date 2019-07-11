<?php

namespace App\Controller\Admin;

use \Core\Controller\Controller;
use \Core\Controller\PaginatedQueryController;
use App\Controller\PaginatedQueryAppController;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->loadModel('post');
        $this->loadModel('category');
        $this->loadModel('users');
        $this->loadModel('beer');
        $this->loadModel('orders');
        $this->loadModel('status');
    }
    public function index()
    {
        $latestPost = $this->post->latestById();
        $latestCategory = $this->category->latestById();
        $latestUser = $this->users->latestById();
        $latestBeer = $this->beer->latestById();
        $latestOrder = $this->orders->latestById();
        $title = "Administration";
        return $this->render("admin/index", [
            "title" => $title,
            "post" => $latestPost,
            "category" => $latestCategory,
            "beer" => $latestBeer,
            "user" => $latestUser,
            "order" => $latestOrder
        ]);
    }
    public function posts()
    {
        $paginatedQuery = new PaginatedQueryAppController(
            $this->post,
            $this->generateUrl('admin_posts')
        );
        $postById = $paginatedQuery->getItems();
        $title = "Les articles du blog";
        return $this->render("admin/post/posts", [
            "title" => $title,
            "posts" => $postById,
            "paginate" => $paginatedQuery->getNavHtml()
        ]);
    }
    public function categories()
    {
        $paginatedQuery = new PaginatedQueryAppController(
            $this->category,
            $this->generateUrl('admin_categories')
        );
        $categories = $paginatedQuery->getItems();
        $title = "Les categories du blog";
        return $this->render("admin/category/categories", [
            "title" => $title,
            "categories" => $categories,
            "paginate" => $paginatedQuery->getNavHtml()
        ]);
    }
    public function users()
    {
        $users = $this->users->allWithoutLimit();
        $title = "Les utilisateurs";
        return $this->render("admin/user/users", [
            "title" => $title,
            "users" => $users
        ]);
    }
    public function beers()
    {
        $paginatedQuery = new PaginatedQueryAppController(
            $this->beer,
            $this->generateUrl('admin_beers')
        );
        $beers = $paginatedQuery->getItems();
        $title = "Les bières";
        return $this->render("admin/beer/beers", [
            "title" => $title,
            "beers" => $beers,
            "paginate" => $paginatedQuery->getNavHtml()
        ]);
    }
    public function orders($post = null, $idStatus = 0)
    {
        if ($idStatus) {
            $orders = $this->orders->allInIdStatus($idStatus);
        } else {
            $orders = $this->orders->allWithoutLimit();
        }

        $title = "Les commandes";

        $statusList = $this->status->all();

        return $this->render("admin/order/orders", [
            "title" => $title,
            "status" => $statusList,
            "orders" => $orders
        ]);
    }
}
