<?php
namespace App\Model\Table;

use \Core\Model\Table;
use App\Model\Entity\BeerEntity;

/**
 *  Classe BeerTable : accès à la table beer
 **/
class BeerTable extends Table
{
    public function insertBeer($name, $slug, $img, $content, $price)
    {
        $sql = "INSERT INTO {$this->table} 
        (`name`, `slug`, `img`, `content`, `price`) 
        VALUES ( :name, :slug, :img, :content, :price)";
        $attributes = [
            ":name"         => $name,
            ":slug"         => $slug,
            ":img"          => $img,
            ":content"      => $content,
            ":price"        => $price
        ];
        return $this->query($sql, $attributes);
    }
}
