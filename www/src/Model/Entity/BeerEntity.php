<?php

namespace App\Model\Entity;

use \Core\Controller\Helpers\TextController;
use \Core\Model\Entity;

/**
 *  Classe Beer : une bière
 **/
class BeerEntity extends Entity
{
    /**
     * id de la biere
     * @var int
     */
    private $id;
    /**
     * titre de la bière
     * @var string
     */
    private $title;
    /**
     * url de l'image
     * @var  string
     */
    private $img;
    /**
     * content
     * @var  string
     */
    private $content;
    /**
     * prix HT
     * @var  float
     */
    private $price_ht;
    /**
     * stock
     * @var  int
     */
    private $stock;

    /**
     * slug
     * @var  int
     */
    private $slug;

    /**
     * recupère l'id de la bière
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
    
    /**
     * recupère le titre de la bière
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * recupère l'url de l'image de la bière
     * @return string
     */
    public function getImg(): string
    {
        return $this->img;
    }

    /**
     * recupère le contenu de la bière
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * recupère le prix HT de la bière
     * @return float
     */
    public function getPriceHt(): float
    {
        return $this->price_ht;
    }

    /**
     * recupère le stock de la bière
     * @return int
     */
    public function getStock(): int
    {
        return $this->stock;
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
    public function getExcerpt(int $lg = 100): string
    {
        return TextController::excerpt($this->content, $lg);
    }

    /**
     *  prix ht
     *  @return string
     **/
    public function getPrixHt(): string
    {
        return (string) number_format($this->price_ht, 2, ',', ' ') . '€';
    }

    /**
     *  prix
     *  @return string
     **/
    public function getPrixTTC(): string
    {
        return (string) number_format($this->price_ht * \App\App::getInstance()->getEnv('ENV_TVA'), 2, ',', ' ') . '€';
    }

    /**
     * getUrl()
     *  @return string
     */
    public function getUrl(): string
    {
        return \App\App::getInstance()->getRouter()->url('beer', [
            'id' => $this->getId()
        ]);
    }

    /**
     * getAdminUrl()
     *  @return string
     */
    public function getAdminUrl(): string
    {
        return \App\App::getInstance()->getRouter()->url("admin_beer_edit", [
            "slug" => $this->getSlug(),
            "id" => $this->getId()
        ]);
    }

    /**
     * getAdminDeleteUrl()
     *  @return string
     */
    public function getAdminDeleteUrl(): string
    {
        return \App\App::getInstance()->getRouter()->url("admin_beer_delete", [
            "slug" => $this->getSlug(),
            "id" => $this->getId()
        ]);
    }
}
