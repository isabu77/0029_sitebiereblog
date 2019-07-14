<?php
namespace App\Model\Entity;

use \Core\Controller\Helpers\TextController;
use \Core\Model\Entity;

/**
 *  Classe Users : un client du site bière
 **/
class UserInfosEntity extends Entity
{
    private $id;
    private $user_id;
    private $lastname;
    private $firstname;
    private $address;
    private $city;
    private $zip_code;
    private $country;
    private $phone;

    public function getProperties(): array
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
     *  user_id
     *  @return int
     **/
    public function getUserId(): int
    {
        return ((int)$this->user_id);
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
        return ((string)$this->zip_code);
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
     * getUrl()
     */
    public function getUrl():string
    {
        return \App\App::getInstance()
            ->getRouter()
            ->url('client', [
            'id' => $this->getId()
            ]);
    }
    /**
     *  id
     *  @return
     **/
    public function setId(string $id)
    {
        $this->id = $id;
    }

    /**
     *  user_id
     *  @return
     **/
    public function setUserId(string $user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     *  title
     *  @return
     **/
    public function setLastname(string $lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     *  img
     *  @return
     **/

    public function setFirstname(string $firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     *  contenu
     *  @return
     **/
    public function setZipCode(string $zip_code)
    {
        $this->zip_code = $zip_code;
    }
    /**
     *  contenu
     *  @return
     **/
    public function setAddress(string $address)
    {
        $this->address =$address;
    }
        
    /**
     *  contenu
     *  @return
     **/
    public function setCity(string $address)
    {
        $this->city =$address;
    }
        
    /**
     *  contenu
     *  @return
     **/
    public function setCountry(string $country)
    {
        $this->country = $country;
    }
        
    /**
     *  contenu
     *  @return
     **/
    public function setPhone(string $phone)
    {
        $this->phone = $phone;
    }
}
