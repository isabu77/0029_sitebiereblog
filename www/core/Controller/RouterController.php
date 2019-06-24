<?php
namespace Core\Controller;

class RouterController
{
    private $router;
    private $viewPath;

    /**
     * constructeur
     */
    public function __construct(string $viewPath)
    {
        $this->viewPath = $viewPath;
        $this->router = new \AltoRouter();
    }

    /**
     * get
     */
    public function get(string $uri, string $file, string $name): self
    {
        $this->router->map('GET', $uri, $file, $name);
        return $this; // pour enchainer les get à l'appel

    }

    /**
     * génère une url avec une route 
     */
    public function url(string $name, array  $params = []): string
    {
        return $this->router->generate($name, $params);
    }

    /**
     * lance la route qui matche
     */
    public function run(): void
    {
        $match = $this->router->match();

        // on définit une variable avec l'instance pour appeler url() 
        // dans toutes les vues qui sont incluses ci-dessous (dans le dossier views)
        $router = $this;

        if (is_array($match)) {
            if (strpos($match['target'], "#")) {
                [$controller, $methode] = explode("#", $match['target']);
                $controller = "App\\Controller\\".ucfirst($controller)."Controller";
               ;
                (new $controller())->$methode(...array_values($match['params']));
                exit();
            }
            $params = $match['params'];	
            require $this->pathToFile($match['target']);
        } else {
            // no route was matched
            header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
            require $this->pathToFile("layout/404");
        }
    }

    /**
     * 
     */
    private function pathToFile(string $file): string
    {
        return $this->viewPath . DIRECTORY_SEPARATOR . $file . '.php';
    }
}
