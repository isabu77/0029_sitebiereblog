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
    public function getTitle()
    {
        return ((string)$this->title);
    }

    /**
     *  img
     *  @return string
     **/

    public function getImg()
    {
        return ((string)$this->img);
    }

    /**
     *  contenu
     *  @return string
     **/
    public function getContent()
    {
        return ((string)$this->content);
    }
    /**
     *  contenu
     *  @return string
     **/
    public function getExcerpt(int $lg = 100):string
    {
        return TextController::excerpt($this->content, $lg);
    }
    
    /**
     *  prix
     *  @return float
     **/
    public function getPrice()
    {
        return ((float)$this->price);
    }
    /**
     *  prix ht
     *  @return string
     **/
    public function getPrixHt()
    {
        return (String)number_format($this->price, 2, ',', ' ').'€';
    }
    
    /**
     *  prix
     *  @return string
     **/
    public function getPrixTTC()
    {
        return (String)number_format($this->price*1.2, 2, ',', ' ').'€';
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
}
