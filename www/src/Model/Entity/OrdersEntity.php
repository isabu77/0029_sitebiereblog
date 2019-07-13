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
    private $id_client;
    private $number;
    private $token;
    private $id_status;
    private $priceHT;
    private $priceTTC;
    private $createdAt;
    
    public function get_properties(): array
    {
        return get_object_vars($this);
    }

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
    public function getIdClient(): int
    {
        return ((int)$this->id_client);
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
     *  token
     *  @return string
     **/
    public function getToken()
    {
        return ((string)$this->token);
    }

     /**
     *  id_status
     *  @return string
     **/
    public function getIdStatus()
    {
        return ((string)$this->id_status);
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

    public function getAdminUrl():string
    {
        return \App\App::getInstance()->getRouter()->url("admin_order_edit", [
            "id" => $this->getId(),
            "id_user" => $this->getIdClient()
        ]);
    }
    public function deleteUrl():string
    {
        return \App\App::getInstance()->getRouter()->url("admin_order_delete", [
            "id" => $this->getId(),
            "id_user" => $this->getIdClient()
        ]);
    }


    /**
     *  contenu
     *  @return
     **/
    public function setIdClient(int $id_client)
    {
        $this->id_client = $id_client;
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
        $this->number =$number;
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
    public function setIdStatus(string $id_status)
    {
        $this->id_status =$id_status;
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
