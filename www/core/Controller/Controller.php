<?php
namespace Core\Controller;

class Controller
{
    private $app;
    private $twig;

    /**
     * getenv : retourne une variable d'environnement de l'application
     * définie dans config.php
     */
    public function getenv(string $name)
    {
        require 'config.php';
        return $env[$name]; 
    }

    /**
     * Rendu d'une page en .twig
     */
    protected function render(string $view, array $variable = [])
    {
        $variable['debugTime'] = $this->getApp()->getDebugTime();
        echo $this->getTwig()->render($view . '.twig', $variable);
    }

    /**
     * retourne l'instance du moteur de template Twig
     */
    private function getTwig()
    {
        if (is_null($this->twig)) {
            // initialisation de Twig : moteur de template PHP
            $loader = new \Twig\Loader\FilesystemLoader(dirname(dirname(__dir__)) . '/views/');
            $this->twig = new \Twig\Environment($loader);
            // passage des variables globales de session et de constantes
            $this->twig->addGlobal('session', $_SESSION);
            $this->twig->addGlobal('constant', get_defined_constants());
            }

        return $this->twig;
    }

    /**
     * retourne l'instance de la classe App (Application)
     */
    protected function getApp()
    {
        if (is_null($this->app)) {
            $this->app = \App\App::getInstance();
        }
        return $this->app;
    }

    /**
     * génère l'Url de la route pour la page routeName
     */
    protected function generateUrl(string $routeName, array $params = []): string
    {
        return $this->getApp()->getRouter()->url($routeName, $params);
    }

    /**
     * crée dynamiquement une instance de la classe $nameTable
     * et la stocke dans la propriété de nom $nameTable
     * héritée dans sa sous-classe qui appelle ce loadModel dans son constructeur
     */
    protected function loadModel(string $nameTable): void
    {
        // crée une propriété de nom '$nameTable' contenant l'instance de la sous-classe de Table
        // (par exemple : 'post' crée une instance $post= new PostTable() )
        $this->$nameTable = $this->getApp()->getTable($nameTable);
    }

    /**
     * retourne l'utilisateur connecté
     * @return object|void
     */
    public function connectedSession():?object
    {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
        // n'est pas defini et false
        if (!$_SESSION["auth"]) {
            header('Location: /');
            exit();
        }
        return $_SESSION["auth"];
    }

    /**
     * la connexion au site
     *
     */
    public function connectSession($user)
    {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION['auth'] = $user;
    }

    /**
     * la déconnexion du site
     *
     */
    public function deconnectSession()
    {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
        unset($_SESSION["auth"]);
        header('Location: /');
        exit();
    }
}
