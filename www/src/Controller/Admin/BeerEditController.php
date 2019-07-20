<?php
namespace App\Controller\Admin;

use \Core\Controller\Controller;

class BeerEditController extends Controller
{
    public function __construct()
    {
        $this->loadModel('beer');
    }

    public function beerEdit($post, $slug, $id)
    {
        $beer = $this->beer->find($id);
        if (!$beer) {
            throw new \Exception('Aucun article ne correspond à cet ID');
        }
        $title = $beer->getTitle();
        
        return $this->render("admin/beer/beerEdit", [
            "title" => $title,
            "beer" => $beer
        ]);
    }

    public function beerUpdate($post, $slug, $id)
    {
        $beer = $this->beer->find($id);
        $url = $this->generateUrl('admin_beer_edit', ['slug' => $beer->getSlug(), 'id' => $id]);
        
        if (isset($post)) {
            $attributes["title"] =  htmlspecialchars($post['beer_name']);
            $attributes["img"] =  htmlspecialchars($post['beer_img']);
            $attributes["content"] =  htmlspecialchars($post['beer_content']);
            $attributes["price"] =  htmlspecialchars($post['beer_price']);

            $res = $this->beer->update($id, $attributes);
            if ($res) {
                $this->getFlashService()->addSuccess("La bière a bien été modifiée");
            } else {
                $this->getFlashService()->addAlert("La bière n'a pas été modifiée");
            }

            header('Location: ' . $url);
        }
    }

    public function beerInsert($post)
    {
        if (isset($post['name']) && !empty($post['name']) &&
            //isset($post['img']) && !empty($post['img']) &&
            isset($post['content']) && !empty($post['content']) &&
            isset($post['price']) && !empty($post['price'])) {
            $price = (int)$post['price'];
            $attributes =
            [
                "title"     => htmlspecialchars($post['name']),
                "img"      => htmlspecialchars($post['img']),
                "content"      => htmlspecialchars($post['content']),
                "price"      => (int)$post['price']
            ];
            $res = $this->beer->insert($attributes);
            if ($res) {
                $this->getFlashService()->addSuccess("La bière a bien été ajoutée");
            } else {
                $this->getFlashService()->addAlert("La bière n'a pas été ajoutée");
            }
        }

        $title = "Ajouter une bière";
        
        return $this->render("admin/beer/beerInsert", [
            "title" => $title
        ]);
    }

    public function beerDelete($post, $slug, $id)
    {
        $this->beer->delete($id);
        header('location: /admin/beers');
    }
}
