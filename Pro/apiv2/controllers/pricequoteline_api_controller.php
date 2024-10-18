<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "pricequote_api_controller.php";
class pricequoteline_api_controller extends pricequote_api_controller
{
    public function add_api_action()
    {
        $parse_array = $this->getValidParameters();
        $pricequote = $this->object;
        $pricequote->Identifier = $this->_get_pricequote_id();
        if($pricequote->show()) {
            HostFact_API::beginTransaction();
            $aPriceQuoteLines = $this->_updatePriceQuoteLines($parse_array);
            foreach ($pricequote as $key => $value) {
                if(is_string($value)) {
                    $pricequote->{$key} = htmlspecialchars_decode($value);
                }
            }
            if(!empty($pricequote->Error) || !$pricequote->edit()) {
                HostFact_API::parseError($pricequote->Error, true);
            }
            if(!empty($aPriceQuoteLines)) {
                HostFact_API::parseSuccess(sprintf(__("success add x price quote lines"), count($aPriceQuoteLines)));
                HostFact_API::commit();
            }
            return $this->_show_pricequote($pricequote->Identifier);
        } else {
            HostFact_API::parseError($pricequote->Error, true);
        }
    }
    public function delete_api_action()
    {
        $parse_array = $this->getValidParameters();
        $pricequote = $this->object;
        $pricequote->Identifier = $this->_get_pricequote_id();
        if($pricequote->show()) {
            HostFact_API::beginTransaction();
            $aPriceQuoteLines = $this->_deletePriceQuoteLines($parse_array);
            if(!empty($pricequote->Error) || HostFact_API::hasErrors()) {
                HostFact_API::parseError($pricequote->Error, true);
            }
            if(0 < $aPriceQuoteLines) {
                HostFact_API::parseSuccess(sprintf(__("success del x price quote lines"), $aPriceQuoteLines));
            } else {
                HostFact_API::parseError(__("there are no price quote ids found"), true);
            }
            $elements = new pricequoteelement();
            $count = $elements->all($pricequote->PriceQuoteCode);
            if(empty($count)) {
                return parent::delete_api_action();
            }
            foreach ($pricequote->Variables as $key) {
                if(is_string($pricequote->{$key})) {
                    $pricequote->{$key} = htmlspecialchars_decode($pricequote->{$key});
                }
            }
            if($pricequote->edit()) {
                HostFact_API::commit();
                return $this->_show_pricequote($pricequote->Identifier);
            }
            HostFact_API::parseError($pricequote->Error, true);
        } else {
            HostFact_API::parseError($pricequote->Error, true);
        }
    }
}

?>