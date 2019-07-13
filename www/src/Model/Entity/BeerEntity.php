<?php
namespace App\Model\Entity;

use \Core\Controller\Helpers\TextController;
use \Core\Model\Entity;

/**
 *  Classe Beer : une bière
 **/
class BeerEntity extends Entity
{
    private $id;
    private $title;
    private $img;
    private $content;
    private $price;
    private $slug;

    /**
     *  id
     *  @return int
     **/
    public function getId(): int
    {
        return ($this->id);
    }

    /**
     *  title
     *  @return string
     **/
    public function getTitle(): string
    {
        return ((string)$this->title);
    }

    /**
     *  img
     *  @return string
     **/

    public function getImg(): string
    {
        return ((string)$this->img);
    }

    /**
     * Get the value of slug
     */
    public function getSlug()
    {
        return $this->slug;
    }
    
    /**
     *  contenu
     *  @return string
     **/
    public function getContent(): string
    {
        return ((string)$this->content);
    }
    /**
     *  contenu
     *  @return string
     **/
    public function getExcerpt(int $lg = 100): string
    {
        return TextController::excerpt($this->content, $lg);
    }
    
    /**
     *  prix
     *  @return float
     **/
    public function getPrice(): float
    {
        return ((float)$this->price);
    }
    /**
     *  prix ht
     *  @return string
     **/
    public function getPrixHt(): string
    {
        return (String)number_format($this->price, 2, ',', ' ').'€';
    }
    
    /**
     *  prix
     *  @return string
     **/
    public function getPrixTTC(): string
    {
        return (String)number_format($this->price * \App\App::getInstance()->getEnv('ENV_TVA'), 2, ',', ' ').'€';
    }

    /**
     * getUrl()
     */
    public function getUrl():string
    {
        return \App\App::getInstance()
            ->getRouter()
            ->url('beer', [
            'id' => $this->getId()
            ]);
    }

    public function getAdminUrl():string
    {
        return \App\App::getInstance()->getRouter()->url("admin_beer_edit", [
            "slug" => $this->getSlug(),
            "id" => $this->getId()
        ]);
    }
    public function deleteUrl():string
    {
        return \App\App::getInstance()->getRouter()->url("admin_beer_delete", [
            "slug" => $this->getSlug(),
            "id" => $this->getId()
        ]);
    }
}
