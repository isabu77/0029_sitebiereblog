<?php
namespace App\Model\Entity;

use \Core\Controller\Helpers\TextController;
use \Core\Model\Entity;

/**
 *  Classe Users : un user du site bière
 **/
class UsersEntity extends Entity
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
     *  contenu
     *  @return 
     **/
    public function setLibelle(string $libelle)
    {
        $this->libelle = $libelle;
    }
}
