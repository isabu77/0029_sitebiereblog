<?php

namespace Core\Extension\Twig;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Core\Controller\FlashController;

/**
 * Classe d'extension de Twig pour les messages Flash
 */
class FlashExtension extends AbstractExtension
{

    /**
     * FlashService
     */
    private $flashService;

    public function __construct()
    {
        $this->flashService = new FlashController();
    }

    /**
     * retourne la méthode dans la vue par le mot 'flash'
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('flash', [$this, 'getFlash'])
        ];
    }

    public function getFlash(string $type): ?array
    {
        return $this->flashService->get($type);
    }
}
