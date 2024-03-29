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
    private $route = 'category';

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
        return ((string) $this->name);
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
    public function getUrl(): string
    {
        return \App\App::getInstance()->getUri('category', [
            'slug' => $this->getSlug(),
            'id' => $this->getId()
        ]);
    }

    /**
     * getAdminUrl()
     *  @return string
     */
    public function getAdminUrl(): string
    {
        return \App\App::getInstance()->getUri("admin_category_edit", [
            "slug" => $this->getSlug(),
            "id" => $this->getId()
        ]);
    }

    /**
     * getAdminDeleteUrl()
     *  @return string
     */
    public function getAdminDeleteUrl(): string
    {
        return \App\App::getInstance()->getUri("admin_category_delete", [
            "slug" => $this->getSlug(),
            "id" => $this->getId()
        ]);
    }
}
