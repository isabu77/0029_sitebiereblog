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

    public function orderEdit($post, $id, $user_id)
    {
        $order = $this->order->find($id);
        if (!$order) {
            throw new \Exception('Aucune commande ne correspond à cet ID');
        }
        if ($order->getUserInfosId() !== (int)$user_id) {
            $url = $this->generateUrl('admin_order_edit', ['id' => $id, 'user_id' => $order->getUserInfosId()]);
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

    public function orderUpdate($post, $id, $user_id)
    {
        $order = $this->order->find($id);
        if (!$order) {
            throw new \Exception('Aucune commande ne correspond à cet ID');
        }

        $url = $this->generateUrl('admin_order_edit', ['id' => $id, 'user_id' => $order->getUserInfosId()]);

        if (isset($post["select"])) {
            //changer le status
            if ($this->order->update($id, ['status_id'  => $post["select"] ])) {
                $this->getFlashService()->addSuccess("La commande a bien été modifiée");
            } else {
                $this->getFlashService()->addAlert("La commande n'a pas été modifiée");
            }
            header('location: '.$url);
        }
    }

    public function orderDelete($post, $id, $user_id)
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
