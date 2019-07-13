<?php
namespace App\Model\Table;

use \Core\Model\Table;
use App\Model\Entity\PostEntity;

/**
 *  Classe PostTable : accès à la table post
 **/
class PostTable extends Table
{
    
    /**
     * lecture de tous les articles d'une page avec leurs catégories
     */
    public function allByLimit(int $limit, int $offset)
    {

        $posts = $this->query("SELECT * FROM {$this->table} LIMIT {$limit} OFFSET {$offset}", null);

        $ids = array_map(function (PostEntity $post) {
            return $post->getId();
        }, $posts);


        $categories = (new CategoryTable($this->db))->allInId(implode(', ', $ids));

        $postById = [];
        foreach ($posts as $post) {
            $postById[$post->getId()] = $post;
        }
        //dd($categories);
        foreach ($categories as $category) {
            $postById[$category->post_id]->setCategory($category);
        }
        return $postById;
    }

    /**
     * surcharge de count() pour gérer le nb d'articles d'une catégorie
     */
    public function count(?int $id = null)
    {
        if (!$id) {
            // sans id : appel de la méthode de la classe parente Table.php
            return parent::count();
        } else {
            return $this->query("SELECT COUNT(id) as nbrow FROM {$this->table} as p 
                    JOIN {$this->prefix}post_category as pc ON pc.post_id = p.id 
                    WHERE pc.category_id = {$id}", null, true);
        }
    }

    /**
     * lecture de tous les articles d'une catégorie d'une page
     */
    public function allInIdByLimit(int $limit, int $offset, int $idCategory)
    {

        $posts = $this->query("
        SELECT * FROM {$this->table} as p 
                JOIN  {$this->prefix}post_category  as pc ON pc.post_id = p.id 
                WHERE pc.category_id = {$idCategory}
                LIMIT {$limit} OFFSET {$offset} ", null);

        $ids = array_map(function (PostEntity $post) {
            return $post->getId();
        }, $posts);


        $categories = (new CategoryTable($this->db))->allInId(implode(', ', $ids));


        $postById = [];
        foreach ($posts as $post) {
            $postById[$post->getId()] = $post;
        }
        foreach ($categories as $category) {
            $postById[$category->post_id]->setCategory($category);
        }
        return $postById;
    }

    public function allInIdByThird(int $id)
    {
        $posts = $this->query("SELECT * FROM {$this->table} as p
            JOIN {$this->prefix}post_category as pc ON pc.post_id = p.id
            WHERE pc.category_id = {$id}
            LIMIT 3");
        $ids = array_map(function (PostEntity $post) {
            return $post->getId();
        }, $posts);
        $categories = (new CategoryTable($this->db))->allInId(implode(', ', $ids));
        $postById = [];
        foreach ($posts as $post) {
            $postById[$post->getId()] = $post;
        }
        foreach ($categories as $category) {
            $postById[$category->post_id]->setCategories($category);
        }
        return $postById;
    }
}
