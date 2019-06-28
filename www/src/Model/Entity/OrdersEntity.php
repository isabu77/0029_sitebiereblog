<?php
namespace App\Model\Entity;

use \Core\Controller\Helpers\TextController;
use \Core\Model\Entity;

/**
 *  Classe Orders : une commande de bières
 **/
class OrdersEntity extends Entity
{
    private $id;
    private $id_user;
    private $number;
    private $ids_product;
    private $priceHT;
    private $priceTTC;
    private $createdAt;

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
     *  number
     *  @return string
     **/

    public function getNumber()
    {
        return ((string)$this->number);
    }

     /**
     *  ids_product
     *  @return string
     **/

    public function getIdsProduct()
    {
        return ((string)$this->ids_product);
    }

   /**
     *  contenu
     *  @return string
     **/
    public function getCreatedAt()
    {
        return ((string)$this->createdAt);
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
            ->url('orders', [
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
  
    /**
     *  contenu
     *  @return 
     **/
    public function setNumber(string $number)
    {
        $this->ids_product =$number;
    }
   /**
     *  contenu
     *  @return 
     **/
    public function setIdsProduct(string $ids_product)
    {
        $this->ids_product =$ids_product;
    }
    /**
     *  contenu
     *  @return 
     **/
    public function setCreatedAt(string $createdAt)
    {
        $this->createdAt =$createdAt;
    }
}
