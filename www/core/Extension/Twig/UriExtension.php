<?php

namespace Core\Extension\Twig;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Core\Controller\Controller;

/**
 * Classe d'extension de Twig pour les uri
 */
class UriExtension extends AbstractExtension
{
    /**
     * controller
     */
    private $controller;

    public function __construct()
    {
        $this->controller = new Controller();
    }

    /**
     * retourne la méthode dans la vue par le mot 'uri'
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('uri', [$this, 'getUri'])
        ];
    }

    /**
     * retourne l'uri d'une route
     */
    public function getUri(string $routeName, array $params = []): string
    {
        return $this->controller->getUri($routeName, $params);
    }
}
