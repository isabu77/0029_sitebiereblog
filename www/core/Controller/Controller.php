<?php
namespace Core\Controller;

class Controller
{
    private $app;
    private $twig;

    /**
     * Rendu d'une page en .twig
     */
    protected function render(string $view, array $variable = [])
    {
        $variable['debugTime'] = $this->getApp()->getDebugTime();
        $variable['cookie'] = $_COOKIE;
        $variable['session'] =  $_SESSION;
        $variable['constant'] =  get_defined_constants();

        echo $this->getTwig()->render($view . '.twig', $variable);
        
        unset($_SESSION["success"]); //Supprime la SESSION['success']
        unset($_SESSION["error"]); //Supprime la SESSION['error']
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
            //$this->twig->addGlobal('cookie', $_COOKIE);
            //$this->twig->addGlobal('session', $_SESSION);
            //$this->twig->addGlobal('constant', get_defined_constants());
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
    public function connectedSession($isConnected = true):?object
    {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
        // n'est pas defini et false
        if (!$_SESSION["auth"]) {
            if ($isConnected ){
                header('Location: /connexion');
                exit();
            }
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
