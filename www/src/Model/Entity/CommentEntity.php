<?php
namespace App\Model\Entity;

use \Core\Model\Entity;

class CommentEntity extends Entity
{
    private $id;
    private $post_id;
    private $user_id;
    private $name;
    private $content;
    private $created_at;

    public function getId()
    {
        return $this->id;
    }

    public function getPostId()
    {
        return $this->post_id;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }
}
