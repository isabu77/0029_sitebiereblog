<?php
// Fichier principal qui initialise l'application et définit les routes
$basepath = dirname(__dir__) . DIRECTORY_SEPARATOR; // contient /var/www/

require_once $basepath . 'vendor/autoload.php';

// App : Instance unique de l'application (Singleton)
$app = \App\App::getInstance();
$app->setStartTime(microtime(true));
$app::load();

// définition des routes
//$router = new App\Router($basepath . 'views');
$app->getRouter($basepath)
    ->get('/', 'Beer#index', 'home')
    ->get('/boutique', 'Beer#all', 'boutique')
    ->get('/purchase', 'Beer#purchase', 'purchase')
    ->get('/cart', 'Beer#cart', 'cart')
    ->get('/purchaseconfirm/[i:id]', 'Beer#purchaseconfirm', 'purchaseconfirm')
    ->get('/blog', 'Post#all', 'posts')
    ->get('/article/[*:slug]-[i:id]', 'Post#show', 'post')
    ->get('/categories', 'Category#all', 'categories')
    ->get('/category/[*:slug]-[i:id]', 'Category#show', 'category')
    ->get('/contact', 'Users#contact', 'contact')
    ->get('/about', 'about#index', 'about')
    ->get('/inscription', 'Users#inscription', 'inscription')
    ->get('/identification/verify/[i:id]&[*:token]', 'Users#inscription', 'inscription_Verify')
    ->get('/connexion', 'Users#connexion', 'connexion')
    ->get('/profil', 'Users#profil', 'profil')
    ->get('/resetpwd', 'users#resetpwd', 'resetpwd')
    ->get('/deconnexion', 'Users#deconnexion', 'deconnexion')
    ->get('/mentions', 'Beer#mentions', 'mentions')
    ->get('/cgv', 'Beer#cgv', 'cgv')
    ->get('/boutique/panier', 'Beer#panier', 'panier')
    ->post('/inscription', 'Users#inscription', 'inscription_profil')
    ->post('/connexion', 'Users#connexion', 'connexion_profil')
    ->post('/profil', 'Users#profil', 'profil_update')
    ->get('/profil/[i:idClient]', 'Users#profil', 'profil_post')
    ->get('/purchase/[i:idClient]', 'Beer#purchase', 'purchase_profil')
    ->post('/purchase/[i:idClient]', 'Beer#purchase', 'purchase_post')
    ->post('/purchase', 'Beer#purchase', 'purchase_order')
    ->post('/cart', 'Beer#cart', 'cart_post')
    ->post('/contact', 'users#contact', 'contact_send')
    ->post('/resetpwd', 'users#resetpwd', 'resetpwd_send')
    ->get('/addcart', 'Beer#addcart', 'purchase_addcart')
    ->post('/addToCart', 'Beer#addToCart', 'purchase_addToCart_post')
    ->post('/addcart', 'Beer#addcart', 'purchase_addcart_post')
    ->get('/updatecart', 'Beer#updatecart', 'purchase_updatecart')
    ->post('/updatecart', 'Beer#updatecart', 'purchase_updatecart_post')
    ->get('/deletecart', 'Beer#deletecart', 'purchase_deletecart')
    ->post('/deletecart', 'Beer#deletecart', 'purchase_deletecart_post')
    ->post('/getClient', 'Users#getClient', 'getClient')
    
    ->get('/admin', 'admin\Admin#index', 'admin')
    ->get('/admin/posts', 'admin\Admin#posts', 'admin_posts')
    ->get('/admin/beers', 'admin\Admin#beers', 'admin_beers')
    ->get('/admin/orders', 'admin\Admin#orders', 'admin_orders')
    ->get('/admin/categories', 'admin\Admin#categories', 'admin_categories')
    ->get('/admin/users', 'admin\Admin#users', 'admin_users')

    ->get('/admin/orders/[i:id]', 'admin\Admin#orders', 'admin_orders_get')
    ->post('/admin/orders/[i:id]', 'admin\Admin#orders', 'admin_orders_post')

    ->get('/admin/posts/[*:slug]-[i:id]', 'admin\PostEdit#PostEdit', 'admin_posts_edit')
    ->post('/admin/posts/[*:slug]-[i:id]', 'admin\PostEdit#postUpdate', 'admin_post_update')
    ->get('/admin/post-delete/[*:slug]-[i:id]', 'admin\PostEdit#postDelete', 'admin_post_delete')
    ->get('/admin/postInsert', 'admin\PostEdit#postInsert', 'admin_post_insert')
    ->post('/admin/postInsert', 'admin\PostEdit#postInsert', 'admin_post_insert2')
    
    ->get('/admin/category/[*:slug]-[i:id]', 'admin\CategoryEdit#categoryEdit', 'admin_category_edit')
    ->get('/admin/category-delete/[*:slug]-[i:id]', 'admin\CategoryEdit#categoryDelete', 'admin_category_delete')
    ->post('/admin/category/[*:slug]-[i:id]', 'admin\CategoryEdit#categoryUpdate', 'admin_category_update')
    ->get('/admin/categoryInsert', 'admin\CategoryEdit#categoryInsert', 'admin_category_insert')
    ->post('/admin/categoryInsert', 'admin\CategoryEdit#categoryInsert', 'admin_category_insert2')
    
    ->get('/admin/user/[*:token]-[i:id]', 'admin\UserEdit#userEdit', 'admin_user_edit')
    ->get('/admin/user-delete/[*:token]-[i:id]', 'admin\UserEdit#userDelete', 'admin_user_delete')
    ->post('/admin/user/[*:token]-[i:id]', 'admin\UserEdit#userUpdate', 'admin_user_update')
    
    ->get('/admin/beer/[*:slug]-[i:id]', 'admin\BeerEdit#beerEdit', 'admin_beer_edit')
    ->get('/admin/beer-delete/[*:slug]-[i:id]', 'admin\BeerEdit#beerDelete', 'admin_beer_delete')
    ->post('/admin/beer/[*:slug]-[i:id]', 'admin\BeerEdit#beerUpdate', 'admin_beer_update')
    ->get('/admin/beerInsert', 'admin\BeerEdit#beerInsert', 'admin_beer_insert')
    ->post('/admin/beerInsert', 'admin\BeerEdit#beerInsert', 'admin_beer_insert2')
    
    ->get('/admin/orders/[i:id]-[i:id_user]', 'admin\OrderEdit#orderEdit', 'admin_order_edit')
    ->post('/admin/orders/[i:id]-[i:id_user]', 'admin\OrderEdit#orderUpdate', 'admin_order_update')
    ->get('/admin/order-delete/[i:id]-[i:id_user]', 'admin\OrderEdit#orderDelete', 'admin_order_delete')
    ->run();
