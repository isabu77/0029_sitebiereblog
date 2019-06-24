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
    ->get('/purchaseconfirm/[i:id]', 'Beer#purchaseconfirm', 'purchaseconfirm')
    ->get('/blog', 'Post#all', 'posts')
    ->get('/article/[*:slug]-[i:id]', 'Post#show', 'post')
    ->get('/categories', 'Category#all', 'categories')
    ->get('/category/[*:slug]-[i:id]', 'Category#show', 'category')
    ->get('/contact', 'contact#index', 'contact')
    ->get('/about', 'about#index', 'about')
    ->get('/inscription', 'profil#inscription', 'inscription')
    ->get('/connexion', 'profil#connexion', 'connexion')
    ->get('/profil', 'profil#profil', 'profil')
    ->get('/deconnexion', 'profil#deconnexion', 'deconnexion')
    ->post('/inscription', 'profil#inscription', 'inscription_profil')
    ->post('/connexion', 'profil#connexion', 'connexion_profil')
    ->post('/purchase', 'Beer#purchase', 'purchase_order')
    ->run();
