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
    private $ids_product;
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
    public function getId_user(): int
    {
        return ((int)$this->id_user);
    }

    /**
     *  ids_product
     *  @return string
     **/

    public function getIds_product()
    {
        return ((string)$this->ids_product);
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
     *  @return string
     **/
    public function setId_user(int $id_user)
    {
        $this->id_user = $id_user;
    }
  
    /**
     *  contenu
     *  @return string
     **/
    public function setPriceTTC(float $priceTTC)
    {
        $this->priceTTC =$priceTTC;
    }
  
    /**
     *  contenu
     *  @return string
     **/
    public function setIds_product(string $ids_product)
    {
        $this->ids_product =$ids_product;
    }
}
