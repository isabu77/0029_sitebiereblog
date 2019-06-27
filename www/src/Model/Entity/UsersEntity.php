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
    private $mail;
    private $password;
    private $token;
    private $createdAt;
    private $verify;

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
    public function getMail()
    {
        return ((string)$this->mail);
    }
        
    /**
     *  contenu
     *  @return string
     **/
    public function getPassword()
    {
        return ((string)$this->password);
    }
        
    /**
     *  contenu
     *  @return string
     **/
    public function getToken()
    {
        return ((string)$this->token);
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
     *  contenu
     *  @return int
     **/
    public function getVerify(): int
    {
        return ((int)$this->verify);
    }
    
    /**
     * getUrl()
     */
    public function getUrl():string
    {
        return \App\App::getInstance()
            ->getRouter()
            ->url('users', [
            'id' => $this->getId()
            ]);
    }
            
    /**
     *  contenu
     *  @return string
     **/
    public function setMail(string $mail)
    {
        $this->mail = $mail;
    }
/**
     *  contenu
     *  @return string
     **/
    public function setPassword(string $password)
    {
        $this->password =$password;
    }
    /**
     *  contenu
     *  @return string
     **/
    public function setCreatedAt(string $createdAt)
    {
        $this->createdAt =$createdAt;
    }
}
