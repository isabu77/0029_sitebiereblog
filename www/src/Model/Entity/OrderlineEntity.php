<?php
namespace App\Model\Entity;

use \Core\Controller\Helpers\TextController;
use \Core\Model\Entity;

/**
 *  Classe Orderline : une ligne de commande de bières
 **/
class OrderlineEntity extends Entity
{
    private $id;
    private $id_product;
    private $id_user;
    private $token;
    private $quantity;
    private $priceHT;
    private $priceTTC;

    /**
     *  id
     *  @return int
     **/
    public function getId(): int
    {
        return ((int)$this->id);
    }

    /**
     *  id_user
     *  @return int
     **/
    public function getIdUser(): int
    {
        return ((int)$this->id_user);
    }

    /**
     *  id_product
     *  @return int
     **/

    public function getIdProduct()
    {
        return ((int)$this->id_product);
    }

    /**
     *  token
     *  @return string
     **/
    public function getToken()
    {
        return ((string)$this->token);
    }
   /**
     *  contenu
     *  @return int
     **/
    public function getQuantity()
    {
        return ((int)$this->quantity);
    }
    
    /**
     *  prix
     *  @return float
     **/
    public function getPriceHT():float
    {
        return ((float)$this->priceHT);
    }

    /**
     *  prix
     *  @return float
     **/
    public function getPriceTTC():float
    {
        return ((float)$this->priceTTC);
    }

    /**
     * getUrl()
     */
    public function getUrl():string
    {
        return \App\App::getInstance()
            ->getRouter()
            ->url('orderline', [
            'id' => $this->getId()
            ]);
    }

    /**
     *  contenu
     *  @return 
     **/
    public function setIdUser(int $id_user)
    {
        $this->id_user = $id_user;
    }
  
    /**
     *  contenu
     *  @return 
     **/
    public function setToken(string $token)
    {
        $this->token =$token;
    }
    /**
     *  contenu
     *  @return 
     **/
    public function setIdProduct(int $id_product)
    {
        $this->id_product =$id_product;
    }
    /**
     *  contenu
     *  @return 
     **/
    public function setQuantity(int $quantity)
    {
        $this->quantity =$quantity;
    }
    /**
     *  contenu
     *  @return 
     **/
    public function setPriceHT(float $priceHT)
    {
        $this->priceHT =$priceHT;
    }
  
    /**
     *  contenu
     *  @return 
     **/
    public function setPriceTTC(float $priceTTC)
    {
        $this->priceTTC =$priceTTC;
    }
  
}
