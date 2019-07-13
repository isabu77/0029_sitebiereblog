<?php
namespace App\Model\Entity;

use \Core\Controller\Helpers\TextController;
use \Core\Model\Entity;

/**
 *  Classe Post : un article du blog
 **/
class PostEntity extends Entity
{
    private $id;
    private $name;
    private $slug;
    private $created_at;
    private $content;
    private $categories = [];

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
    public function getName()
    {
        return ((string)$this->name);
    }

    /**
     *  slug
     *  @return string
     **/

    public function getSlug()
    {
        return ((string)$this->slug);
    }

    /**
     *  date de création
     *  @return  \DateTime
     **/
    public function getCreatedAt()
    {
        return (new \DateTime($this->created_at));
    }
    /**
     *  date de création
     *  @return : string
     **/
    public function getCreatedAtDMY()
    {
        return (new \DateTime($this->created_at))->format('d/m/Y h:i');
    }

    /**
     *  contenu
     *  @return string
     **/
    public function getContent()
    {
        return ((string)$this->content);
    }
    /**
     *  contenu
     *  @return string
     **/
    public function getExcerpt(int $lg):string
    {
        return htmlentities(TextController::excerpt($this->content, $lg));
    }
    /**
     *  catégories du post
     *  @return string
     **/
    public function getCategories():Array
    {
        return $this->categories;
    }
    /**
     *  catégories du post
     *  @return string
     **/
    public function setCategories(Array $categories)
    {
        $this->categories = $categories;
    }
    /**
     *  catégories du post
     *  @return string
     **/
    public function setCategory(CategoryEntity $category)
    {
        $this->categories[] = $category;
    }

    /**
     * getUrl()
     */
    public function getUrl():string
    {
        return \App\App::getInstance()
            ->getRouter()
            ->url('post', [
            'slug' => $this->getSlug(),
            'id' => $this->getId()
            ]);
    }
    public function getAdminUrl():string
    {
        return \App\App::getInstance()->getRouter()->url("admin_posts_edit", [
            "slug" => $this->getSlug(),
            "id" => $this->getId()
        ]);
    }
    public function deleteUrl():string
    {
        return \App\App::getInstance()->getRouter()->url("admin_post_delete", [
            "slug" => $this->getSlug(),
            "id" => $this->getId()
        ]);
    }
}
