<?php
// Fichier principal qui initialise l'application et définit les routes
$basepath = dirname(__dir__) . DIRECTORY_SEPARATOR; // contient /var/www/

require_once $basepath . 'vendor/autoload.php';

// App : Instance unique de l'application (Singleton)
$app = \App\App::getInstance();
$app->setStartTime(microtime(true));
$app::load();

// définition des routes : attention à l'ordre des priorités
//$router = new App\Router($basepath . 'views');
$app->getRouter($basepath)
    ->get('/', 'Beer#index', 'home')
    ->get('/boutique', 'Beer#all', 'boutique')
    ->get('/order', 'Order#order', 'order')
    ->get('/cart', 'Cart#cart', 'cart')
    ->get('/blog', 'Post#all', 'posts')
    ->get('/contact', 'Users#contact', 'contact')
    ->get('/about', 'about#index', 'about')
    ->get('/mentions', 'Beer#mentions', 'mentions')
    ->get('/cgv', 'Beer#cgv', 'cgv')
 
    ->match('/inscription', 'Users#inscription', 'inscription')
    //->post('/inscription', 'Users#inscription', 'inscription_profil')

    ->get('/profil', 'UserInfos#profil', 'profil')
    ->get('/resetpwd', 'users#resetpwd', 'resetpwd')

    ->match('/connexion', 'Users#connexion', 'connexion')
    //->post('/connexion', 'Users#connexion', 'connexion_profil')

    ->get('/deconnexion', 'Users#deconnectSession', 'deconnexion')

    ->get('/orderconfirm/[i:id]', 'Order#orderconfirm', 'orderconfirm')

    ->get('/article/[*:slug]-[i:id]', 'Post#show', 'post')
    ->post('/article/[*:slug]-[i:id]', 'post#comment', 'comment')   

    ->get('/categories', 'Category#all', 'categories')
    ->get('/category/[*:slug]-[i:id]', 'Category#show', 'category')

    ->get('/identification/verify/[i:id]&[*:token]', 'Users#inscription', 'inscription_Verify')

    ->post('/profil', 'UserInfos#profil', 'profil_update')
    ->get('/profil/[i:idClient]', 'UserInfos#profil', 'profil_post')

    ->post('/order', 'Order#order', 'order_order')
    ->match('/order/[i:idClient]', 'Order#order', 'order_profil')
    //->post('/order/[i:idClient]', 'Beer#order', 'order_post')

    ->post('/cart', 'Cart#cart', 'cart_post')
    ->post('/contact', 'users#contact', 'contact_send')
    ->post('/resetpwd', 'users#resetpwd', 'resetpwd_send')

    //->match('/addcart', 'Cart#addcart', 'order_addcart')
    //->post('/addcart', 'Beer#addcart', 'order_addcart_post')

    ->post('/addToCart', 'Cart#addToCart', 'order_addToCart_post')

    ->match('/updatecart', 'Cart#updatecart', 'order_updatecart')
    //->post('/updatecart', 'Beer#updatecart', 'order_updatecart_post')
    ->match('/deletecart', 'Cart#deletecart', 'order_deletecart')
    //->post('/deletecart', 'Beer#deletecart', 'order_deletecart_post')
    ->post('/getClient', 'UserInfos#getClient', 'getClient')
    
    ->get('/admin', 'admin\Admin#index', 'admin')
    ->get('/admin/posts', 'admin\Admin#posts', 'admin_posts')
    ->get('/admin/beers', 'admin\Admin#beers', 'admin_beers')
    ->get('/admin/orders', 'admin\Admin#orders', 'admin_orders')
    ->get('/admin/categories', 'admin\Admin#categories', 'admin_categories')
    ->get('/admin/users', 'admin\Admin#users', 'admin_users')

    //->get('/admin/orders/[i:id]', 'admin\Admin#orders', 'admin_orders_get')
    ->match('/admin/orders/[i:id]', 'admin\Admin#orders', 'admin_orders_post')
    ->get('/admin/orders/[i:id]-[i:user_id]', 'admin\OrderEdit#orderEdit', 'admin_order_edit')
    ->post('/admin/orders/[i:id]-[i:user_id]', 'admin\OrderEdit#orderUpdate', 'admin_order_update')
    ->get('/admin/order-delete/[i:id]-[i:user_id]', 'admin\OrderEdit#orderDelete', 'admin_order_delete')

    ->get('/admin/posts/[*:slug]-[i:id]', 'admin\PostEdit#PostEdit', 'admin_posts_edit')
    ->post('/admin/posts/[*:slug]-[i:id]', 'admin\PostEdit#postUpdate', 'admin_post_update')
    ->match('/admin/postInsert', 'admin\PostEdit#postInsert', 'admin_post_insert')
    //->post('/admin/postInsert', 'admin\PostEdit#postInsert', 'admin_post_insert2')
    ->get('/admin/post-delete/[*:slug]-[i:id]', 'admin\PostEdit#postDelete', 'admin_post_delete')
    
    ->get('/admin/category/[*:slug]-[i:id]', 'admin\CategoryEdit#categoryEdit', 'admin_category_edit')
    ->post('/admin/category/[*:slug]-[i:id]', 'admin\CategoryEdit#categoryUpdate', 'admin_category_update')
    ->get('/admin/category-delete/[*:slug]-[i:id]', 'admin\CategoryEdit#categoryDelete', 'admin_category_delete')
    ->match('/admin/categoryInsert', 'admin\CategoryEdit#categoryInsert', 'admin_category_insert')
    //->post('/admin/categoryInsert', 'admin\CategoryEdit#categoryInsert', 'admin_category_insert2')
    
    ->get('/admin/user/[*:token]-[i:id]', 'admin\UserEdit#userEdit', 'admin_user_edit')
    ->post('/admin/user/[*:token]-[i:id]', 'admin\UserEdit#userUpdate', 'admin_user_update')
    ->get('/admin/user-delete/[*:token]-[i:id]', 'admin\UserEdit#userDelete', 'admin_user_delete')
    
    ->get('/admin/beer/[*:slug]-[i:id]', 'admin\BeerEdit#beerEdit', 'admin_beer_edit')
    ->post('/admin/beer/[*:slug]-[i:id]', 'admin\BeerEdit#beerUpdate', 'admin_beer_update')
    ->get('/admin/beer-delete/[*:slug]-[i:id]', 'admin\BeerEdit#beerDelete', 'admin_beer_delete')
     ->match('/admin/beerInsert', 'admin\BeerEdit#beerInsert', 'admin_beer_insert')
    //->post('/admin/beerInsert', 'admin\BeerEdit#beerInsert', 'admin_beer_insert2')


    ->run();
