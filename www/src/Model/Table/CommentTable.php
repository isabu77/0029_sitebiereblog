<?php
namespace App\Model\Table;

use Core\Model\Table;

class CommentTable extends Table
{
    public function allInId($id)
    {
        return $this->query("SELECT * FROM {$this->table} WHERE post_id = ?", [$id]);
    }

    public function post($post_id, $user_id, $name, $content)
    {
        $sql = "INSERT INTO `comment` 
        (`post_id`, `user_id`, `name`, `content`) 
        VALUES ( :post_id, :user_id, :name, :content)";
        $attributes = [
            ":post_id"  => $post_id,
            ":user_id"  => $user_id,
            ":name"     => $name,
            ":content"  => $content
        ];
        return $this->query($sql, $attributes);
    }
}
