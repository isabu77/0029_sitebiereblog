<?php
namespace App\Model\Entity;

use \Core\Controller\Helpers\TextController;
use \Core\Model\Entity;

/**
 *  Classe Users : un user du site bière
 **/
class UserEntity extends Entity
{
    private $id;
    private $mail;
    private $password;
    private $token;
    private $created_at;
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
        return ((string)$this->created_at);
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
        return \App\App::getInstance()->getUri('users', [
            'id' => $this->getId()
            ]);
    }
    /**
     * getAdminUrl()
     *  @return string
     */
    public function getAdminUrl():string
    {
        return \App\App::getInstance()->getUri("admin_user_edit", [
            "lastname" => $this->getToken(),
            "id" => $this->getId()
        ]);
    }
    /**
     * getAdminDeleteUrl()
     *  @return string
     */
    public function getAdminDeleteUrl():string
    {
        return \App\App::getInstance()->getUri("admin_user_delete", [
            "lastname" => $this->getToken(),
            "id" => $this->getId()
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
    public function setCreatedAt(string $created_at)
    {
        $this->created_at =$created_at;
    }
}
