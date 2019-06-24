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
        FROM post_category pc 
        LEFT JOIN category c on pc.category_id = c.id
        WHERE post_id IN (" . $ids . ")");
    }
    
}
