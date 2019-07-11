<?php
namespace App\Controller\Admin;

use \Core\Controller\Controller;
use \src\Model\Table\OrderlineTable;

class OrderEditController extends Controller
{
    public function __construct()
    {
        $this->loadModel('orders');
        $this->loadModel('orderline');
    }

    public function orderEdit($post=null, $id, $id_user)
    {
        $order = $this->orders->find($id);
        if (!$order) {
            throw new \Exception('Aucune commande ne correspond à cet ID');
        }
        if ($order->getIdClient() !== (int)$id_user) {
            $url = $this->generateUrl('admin_order_edit', ['id' => $id, 'id_user' => $order->getIdClient()]);
            http_response_code(301);
            header('Location: ' . $url);
            exit();
        }

        $lines = $this->orderline->allInToken($order->getToken());

        $title = "Commande n°".$order->getId();
        
        return $this->render("admin/order/orderEdit", [
            "title" => $title,
            "order" => $order,
            "lines" => $lines
        ]);
    }

    public function orderUpdate($post=null, $slug, $id)
    {
        $order = $this->order->find($id);
        $url = $this->generateUrl('admin_order_edit', ['slug' => $order->getSlug(), 'id' => $id]);
        if (isset($post)) {
            //TODO : changer le status
        }
    }

    public function orderDelete($post=null, $id, $id_user)
    {

        $order = $this->orders->find($id);
        if (!$order) {
            throw new \Exception('Aucune commande ne correspond à cet ID');
        }
        $lines = $this->orderline->allInToken($order->getToken());
        foreach($lines as $line){
            $this->orderline->delete($line->getId());
        }

        $this->orders->delete($id);

        header('location: /admin/orders');
    }
}
