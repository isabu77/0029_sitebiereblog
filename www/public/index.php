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
    ->run();
