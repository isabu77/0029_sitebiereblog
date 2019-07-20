<?php
namespace App\Model\Entity;

use \Core\Model\Entity;

/**
 *  Classe Status : un status de commande du site bière
 **/
class ConfigEntity extends Entity
{
    private $id;
    private $created_at;
    private $tva;
    private $port;
    private $ship_limit;

    /**
     *  id
     *  @return int
     **/
    public function getId(): int
    {
        return ((int)$this->id);
    }

   /**
     *  contenu
     *  @return string
     **/
    public function getCreatedAt()
    {
        return ((string)$this->created_at);
    }

     /**
     *  contenu
     *  @return float
     **/
    public function getTva()
    {
        return ((float)$this->tva);
    }
    /**
     *  contenu
     *  @return float
     **/
    public function getPort()
    {
        return ((float)$this->port);
    }
    /**
     *  contenu
     *  @return float
     **/
    public function getShipLimit()
    {
        return ((float)$this->ship_limit);
    }
        
    
    /**
     * getUrl()
     */
    public function getUrl():string
    {
        return \App\App::getInstance()->getUri('config', [
            'id' => $this->getId()
            ]);
    }
            
    /**
     *  contenu
     *  @return
     **/
    public function setTva(float $tva)
    {
        $this->tva = $tva;
    }
    /**
     *  contenu
     *  @return
     **/
    public function setPort(float $port)
    {
        $this->port = $port;
    }
    /**
     *  contenu
     *  @return
     **/
    public function setShipLimit(float $ship_limit)
    {
        $this->ship_limit = $ship_limit;
    }
}
