<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "order_api_controller.php";
class orderline_api_controller extends order_api_controller
{
    public function add_api_action()
    {
        $parse_array = $this->getValidParameters();
        $order = $this->object;
        $order->Identifier = $this->_get_order_id();
        if($order->show() && !in_array($order->Status, [8, 9])) {
            HostFact_API::beginTransaction();
            $aOrderLines = $this->_updateOrderLines($parse_array);
            foreach ($order as $key => $value) {
                if(is_string($value)) {
                    $order->{$key} = htmlspecialchars_decode($value);
                }
            }
            if(!empty($order->Error) || !$order->edit()) {
                HostFact_API::parseError($order->Error, true);
            }
            if(!empty($aOrderLines)) {
                HostFact_API::parseSuccess(sprintf(__("success add x orderlines"), count($aOrderLines)));
                HostFact_API::commit();
            }
            return $this->_show_order($order->Identifier);
        } elseif(isset($order->Status) && $order->Status == 8) {
            HostFact_API::parseError(__("error orderline add-delete status 8"), true);
        } elseif(isset($order->Status) && $order->Status == 9) {
            HostFact_API::parseError(__("error orderline add-delete status 9"), true);
        } else {
            HostFact_API::parseError($order->Error, true);
        }
    }
    public function delete_api_action()
    {
        $parse_array = $this->getValidParameters();
        $order = $this->object;
        $order->Identifier = $this->_get_order_id();
        if($order->show() && !in_array($order->Status, [8, 9])) {
            HostFact_API::beginTransaction();
            $aOrderLines = $this->_deleteOrderLines($parse_array);
            if(!empty($order->Error) || HostFact_API::hasErrors()) {
                HostFact_API::parseError($order->Error, true);
            }
            if(0 < $aOrderLines) {
                HostFact_API::parseSuccess(sprintf(__("success del x orderlines"), $aOrderLines));
            } else {
                HostFact_API::parseError(__("there are no order ids found"), true);
            }
            $elements = new neworderelement();
            $count = $elements->all($order->OrderCode);
            if(empty($count)) {
                return parent::delete_api_action();
            }
            foreach ($order->Variables as $key) {
                if(is_string($order->{$key})) {
                    $order->{$key} = htmlspecialchars_decode($order->{$key});
                }
            }
            if($order->edit()) {
                HostFact_API::commit();
                return $this->_show_order($order->Identifier);
            }
            HostFact_API::parseError($order->Error, true);
        } elseif(isset($order->Status) && $order->Status == 8) {
            HostFact_API::parseError(__("error orderline add-delete status 8"), true);
        } elseif(isset($order->Status) && $order->Status == 9) {
            HostFact_API::parseError(__("error orderline add-delete status 9"), true);
        } else {
            HostFact_API::parseError($order->Error, true);
        }
    }
}

?>