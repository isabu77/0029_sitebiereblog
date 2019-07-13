<?php
namespace App\Model\Table;

use Core\Model\Table;

class PostCategoryTable extends Table
{
    public function insertPC($post_id, $category_id)
    {
        return $this->query("INSERT INTO {$this->table} (post_id, category_id) VALUES ($post_id, $category_id)");
    }
}
