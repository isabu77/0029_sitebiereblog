<?php
namespace Core\Controller\Helpers;

/**
 *  Classe Text   
 * @var string
 * @access public
 * @static
 **/
class TextController
{
    /**
     *  extrait du contenu
     * @param string
     * @param int
     * @return string
     * @access public
     * @static
     **/
    public static function excerpt(string $content, int $limit = 100): string
    {

        // pour oter les balises html :
        //$content = strip_tags($content);
        // avec une expression régulière : remplacer tout ce qui est entre < et > par rien
        $text = preg_replace('/<(.*?)>/', "",$content);

        // si la chaine est plus petite que la limite, on la rend entière
        if (mb_strlen($text) <= $limit) 
            return $text;

        // la manière la plus factorisée : 
        return mb_substr($text, 0, mb_strpos($text, ' ', $limit-1)?: $limit) . '...';

        // en décomposition : 
        // pour ne pas couper le dernier mot, on cherche le premier espace derrière la limite
        // $limit-1 pour gérer le cas d'un espace en position 100
        //$lastSpace = mb_strpos($text, ' ', $limit-1);

        // cas d'une chaine sans espaces : on tronque à la limite demandée
        //$lastSpace = $lastSpace?: $limit;

        //$lastSpace = lastSpace?: $limit;

        // autres cas : on tronque à la limite ou après le dernier mot derrière la limite et ajout des '...'
        //return mb_substr($text, 0, $lastSpace) . '...';

        
    }
}
