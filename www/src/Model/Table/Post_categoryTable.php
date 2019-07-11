<?php
namespace App\Model\Table;
use Core\Model\Table;
class Post_categoryTable extends Table
{
    public function insertPC($post_id, $category_id)
    {
        return $this->query("INSERT INTO post_category (post_id, category_id) VALUES ($post_id, $category_id)");
    }
}