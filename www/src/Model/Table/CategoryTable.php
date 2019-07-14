<?php

namespace App\Model\Table;

use \Core\Model\Table;

/**
 *  Classe CategoryTable : accès à la table category
 **/
class CategoryTable extends Table
{

    /**
     * lecture des catégories de plusieurs articles
     */
    public function allInId(string $ids)
    {
        return $this->query("SELECT c.*, pc.post_id
        FROM {$this->prefix}post_category pc 
        LEFT JOIN {$this->table} c on pc.category_id = c.id
        WHERE post_id IN (" . $ids . ")");
    }
    /**
     * lecture des 3 derniers enregistrements
     */
    public function lastThirdItems($ids)
    {
        return $this->query("SELECT *
                            FROM {$this->prefix}post_category 
                            LEFT JOIN {$this->prefix}post on {$this->prefix}post_category.post_id = {$this->prefix}post.id
                            WHERE category_id IN (" . $ids . ") ORDER BY id DESC LIMIT 3");
    }

}
