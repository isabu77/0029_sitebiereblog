<?php
namespace App\Model\Entity;
use Core\Model\Entity;

/**
 *  Classe Category : une catégorie du blog 
 **/
class CategoryEntity extends Entity
{
    private $id;
    private $name;
    private $slug;

    /**
     *  id
     *  @return int
     **/
    public function getId(): int
    {
        return ($this->id);
    }

    /**
     *  name
     *  @return string
     **/
    public function getName(): string
    {
        return ((string)$this->name);
    }

    /**
     *  slug
     *  @return string
     **/
    public function getSlug(): string
    {
        return ($this->slug);
    }
    /**
     * getUrl()
     */
    public function getUrl():string
    {
        return \App\App::getInstance()
            ->getRouter()
            ->url('category', [
            'slug' => $this->getSlug(),
            'id' => $this->getId()
        ]);
    }
}
