<?php
namespace App\Model\Table;

use Core\Model\Table;

class CommentTable extends Table
{
    public function allInId($id)
    {
        return $this->query("SELECT * FROM {$this->table} WHERE post_id = ?", [$id]);
    }
}
