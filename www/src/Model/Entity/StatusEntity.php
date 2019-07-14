<?php
namespace App\Model\Entity;

use \Core\Model\Entity;

/**
 *  Classe Status : un status de commande du site bière
 **/
class StatusEntity extends Entity
{
    private $id;
    private $label;

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
    public function getLabel()
    {
        return ((string)$this->label);
    }
        
    
    /**
     * getUrl()
     */
    public function getUrl():string
    {
        return \App\App::getInstance()
            ->getRouter()
            ->url('status', [
            'id' => $this->getId()
            ]);
    }
         
    /**
     * getAdminUrlStatus()
     */
    public function getAdminUrlOrders():string
    {
        return \App\App::getInstance()
            ->getRouter()
            ->url('admin_orders_post', [
            'id' => $this->getId()
            ]);
    }
            
    /**
     *  contenu
     *  @return
     **/
    public function setLabel(string $label)
    {
        $this->label = $label;
    }
}
