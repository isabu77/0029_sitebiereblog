<?php
namespace App\Model\Entity;

use \Core\Model\Entity;

/**
 *  Classe Status : un status de commande du site bière
 **/
class StatusEntity extends Entity
{
    private $id;
    private $libelle;

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
    public function getLibelle()
    {
        return ((string)$this->libelle);
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
     * getUrlOrders()
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
    public function setLibelle(string $libelle)
    {
        $this->libelle = $libelle;
    }
}
