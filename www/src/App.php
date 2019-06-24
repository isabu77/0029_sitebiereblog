<?php
namespace App;

use \Core\Controller\RouterController;
use \Core\Controller\URLController;
use \Core\Controller\Database\DatabaseMysqlController;

/**
 * classe SINGLETON : classe PRINCIPALE de l'application
 */
class App
{

    private static $INSTANCE;

    public $title;

    private $router;
    private $startTime;
    private $db_instance;

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
        if (getenv("ENV_DEV")) {
            $whoops = new \Whoops\Run;
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
            $whoops->register();
        }

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
     * crée l'instance du Router
     * et la retourne
     */
    public function getRouter(string $basePath = '/var/www')
    {
        if (is_null($this->router)) {
            $this->router = new RouterController($basePath . 'views');
        }
        return $this->router;
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
    public function getDebugTime()
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
                getenv('MYSQL_DATABASE'),
                getenv('MYSQL_USER'),
                getenv('MYSQL_PASSWORD'),
                getenv('CONTAINER_MYSQL')
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
