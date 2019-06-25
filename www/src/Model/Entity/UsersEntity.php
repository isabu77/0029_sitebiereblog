<?php
namespace App\Model\Entity;
use \Core\Controller\Helpers\TextController;
use \Core\Model\Entity;
/**
 *  Classe Users : un article du blog 
 **/
class UsersEntity extends Entity
{
    private $id;
    private $lastname;
    private $firstname;
    private $address;
    private $zipCode;
    private $city;
    private $country;
    private $phone;
    private $mail;
    private $password;
    private $token;
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
     *  title
     *  @return string
     **/
    public function getLastname()
    {
        return ((string)$this->lastname);
    }

    /**
     *  img
     *  @return string
     **/

    public function getFirstname()
    {
        return ((string)$this->firstname);
    }

    /**
     *  contenu
     *  @return string
     **/
    public function getZipCode()
    {
        return ((string)$this->zipCode);
    }
    /**
     *  contenu
     *  @return string
     **/
    public function getAddress()
    {
        return ((string)$this->address);
    }
        
    /**
     *  contenu
     *  @return string
     **/
    public function getCity()
    {
        return ((string)$this->city);
    }
        
    /**
     *  contenu
     *  @return string
     **/
    public function getCountry()
    {
        return ((string)$this->country);
    }
        
    /**
     *  contenu
     *  @return string
     **/
    public function getPhone()
    {
        return ((string)$this->phone);
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
     *  id
     *  @return int
     **/
    public function setId(string $id)
    {
        $this->id = $id;
    }

    /**
     *  title
     *  @return string
     **/
    public function setLastname(string $lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     *  img
     *  @return string
     **/

    public function setFirstname(string $firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     *  contenu
     *  @return string
     **/
    public function setZipCode(string $zipCode)
    {
        $this->zipCode = $zipCode;
    }
    /**
     *  contenu
     *  @return string
     **/
    public function setAddress(string $address)
    {
        $this->address =$address;
    }
        
    /**
     *  contenu
     *  @return string
     **/
    public function setCity(string $address)
    {
        $this->city =$address;
    }
        
    /**
     *  contenu
     *  @return string
     **/
    public function setCountry(string $country)
    {
        $this->country = $country;
    }
        
    /**
     *  contenu
     *  @return string
     **/
    public function setPhone(string $phone)
    {
        $this->phone = $phone;
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
