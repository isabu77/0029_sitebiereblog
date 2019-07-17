<?php

namespace App;

use \Core\Controller\Controller;
use \Core\Controller\RouterController;
use \Core\Controller\URLController;
use \Core\Controller\Database\DatabaseMysqlController;
use \App\Model\Entity\UserEntity;
use \App\Controller\ConfigController;
use Core\Controller\Session\FlashService;
use Core\Controller\Session\PhpSession;

/**
 * classe SINGLETON : classe PRINCIPALE de l'application
 * méthodes acessibles de partout (controller et model)
 */
class App
{

    private static $INSTANCE;

    public $title;

    private $configTable;
    private $router;
    private $startTime;
    private $db_instance;
    private $flashService;
    private $phpSession;

    /**
     * retourne l'instance UNIQUE de la classe App
     */
    public static function getInstance()
    {
        if (is_null(self::$INSTANCE)) {
            self::$INSTANCE = new App();
        }
        return self::$INSTANCE;
    }

    /**
     * charge les outils et vérifie la page demandée
     */
    public static function load()
    {
        // lecture dans le fichier config.php contenant des variables d'environnement
        // situé dans /src
        // qui lit le fichier config.php situé au meme endroit

        if (self::getInstance()->getEnv("ENV_DEV")) {
            $whoops = new \Whoops\Run;
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
            $whoops->register();
        }

        // lecture dans la base de la table config contenant tva, port et shiplimit
        // accès dans Twig : constant.TVA
        $configTable = new ConfigController();
        $configObj = $configTable->config->lastConfig();
        if ($configObj) {
            define('TVA', $configObj->getTva());
            define('PORT', $configObj->getPort());
            define('SHIPLIMIT', $configObj->getShipLimit());
        } else {
            define('TVA', 1.2);
            define('PORT', 3.5);
            define('SHIPLIMIT', 30);
        }

        // constantes de cookies pur le panier
        define('PANIER', 'panier');
        define('QTYPANIER', 'qtypanier');


        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }

        // la pagination
        $numPage = URLController::getPositiveInt("page");

        if ($numPage !== null) {
            if ($numPage == 1) {
                $uri = explode('?', $_SERVER["REQUEST_URI"])[0];
                $get = $_GET;
                // retirer "page=" de l'url
                unset($get["page"]);
                $query = http_build_query($get);
                if (!empty($query)) {
                    $uri = $uri . '?' . $query;
                }
                http_response_code(301);
                header('location: ' . $uri);
                exit();
            }
        }
    }

    /**
     * getenv : retourne une variable d'environnement de l'application
     * définie dans config.php
     */
    public function getEnv(string $name)
    {
        require 'config.php';
        return $env[$name];
    }

    /**
     * crée l'instance de la session
     * et la retourne
     */
    public function getSession(): PhpSession
    {
        if (is_null($this->phpSession)) {
            $this->phpSession = new PhpSession();
        }
        return $this->phpSession;
    }

    /**
     * retourne l'utilisateur connecté
     * @return object|void
     */
    public function getConnectedUser($isConnected = true):?UserEntity
    {
        
        //if (session_status() != PHP_SESSION_ACTIVE) {
        //    session_start();
        //}
        // n'est pas defini et false
        //if (!$_SESSION["auth"]) {
        if (!$this->getSession()->get("auth", null)) {
            if ($isConnected) {
                header('Location: /connexion');
                exit();
            }
        }
        //return $_SESSION["auth"];
        return $this->getSession()->get("auth", null)[0];
   }

    /**
     * crée l'instance du Router
     * et la retourne
     */
    public function getRouter(string $basePath = '/var/www'): RouterController
    {
        if (is_null($this->router)) {
            $this->router = new RouterController($basePath . 'views');
        }
        return $this->router;
    }

    /**
     * retourne l'instance de la classe App (Application)
     */
    public function getFlashService(): FlashService
    {
        if (is_null($this->flashService)) {
            $this->flashService = new FlashService(new PhpSession(), true);
        }
        return $this->flashService;
    }

    /**
     * démarre le compteur pour le chargement de l'Application
     */
    public function setStartTime()
    {
        $this->startTime = microtime(true);
    }

    /**
     *
     */
    public function getDebugTime(): string
    {
        return number_format((microtime(true) - $this->startTime) * 1000, 2);
    }

    //================= correction AFORMAC
    /**
     * retourne l'instance DatabaseController
     */
    public function getDb(): DatabaseMysqlController
    {
        if (is_null($this->db_instance)) {
            $this->db_instance = new DatabaseMysqlController(
                $this->getEnv('MYSQL_DATABASE'),
                $this->getEnv('MYSQL_USER'),
                $this->getEnv('MYSQL_PASSWORD'),
                $this->getEnv('MYSQL_HOSTNAME')
            );
        }
        return $this->db_instance;
    }

    /**
     * retourne l'instance de la table dans le modèle
     */
    public function getTable(string $nameTable)
    {
        $nameTable = '\\App\\Model\\Table\\' . ucfirst($nameTable) . "Table";
        return new $nameTable($this->getDb());
    }
}
