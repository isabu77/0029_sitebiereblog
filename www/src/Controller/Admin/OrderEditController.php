<?php
namespace App\Controller\Admin;

use \Core\Controller\Controller;
use \src\Model\Table\OrderLineTable;

class OrderEditController extends Controller
{
    public function __construct()
    {
        $this->loadModel('order');
        $this->loadModel('orderLine');
        $this->loadModel('status');
    }

    public function orderEdit($post, $id, $id_user)
    {
        $order = $this->order->find($id);
        if (!$order) {
            throw new \Exception('Aucune commande ne correspond à cet ID');
        }
        if ($order->getIdClient() !== (int)$id_user) {
            $url = $this->generateUrl('admin_order_edit', ['id' => $id, 'id_user' => $order->getIdClient()]);
            http_response_code(301);
            header('Location: ' . $url);
            exit();
        }

        $statusList = $this->status->all();

        $lines = $this->orderLine->allInToken($order->getToken());

        $title = "Commande n°".$order->getId();
        
        return $this->render("admin/order/orderEdit", [
            "title" => $title,
            "status" => $statusList,
            "order" => $order,
            "lines" => $lines
        ]);
    }

    public function orderUpdate($post, $id, $id_user)
    {
        $order = $this->order->find($id);
        if (!$order) {
            throw new \Exception('Aucune commande ne correspond à cet ID');
        }

        $url = $this->generateUrl('admin_order_edit', ['id' => $id, 'id_user' => $order->getIdClient()]);

        if (isset($post["select"])) {
            //changer le status
            if ($this->order->update($id, ['id_status'  => $post["select"] ])) {
                $_SESSION['success'] = "La commande a bien été modifiée";
            } else {
                $_SESSION['error'] = "La commande n'a pas été modifiée";
            }
            header('location: '.$url);
        }
    }

    public function orderDelete($post, $id, $id_user)
    {

        $order = $this->order->find($id);
        if (!$order) {
            throw new \Exception('Aucune commande ne correspond à cet ID');
        }
        $lines = $this->orderLine->allInToken($order->getToken());
        foreach ($lines as $line) {
            $this->orderLine->delete($line->getId());
        }

        $this->order->delete($id);

        header('location: /admin/orders');
    }
}
