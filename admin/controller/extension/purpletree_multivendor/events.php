<?php
class ControllerExtensionPurpletreeMultivendorEvents extends Controller {

	public function addShippingCharge(&$route, &$data, &$output) {
	if(NULL !== $this->config->get('module_purpletree_multivendor_status')){
	   if(isset($data[1]['pts_shipping_charge'])){
		if($data[1]['pts_shipping_charge'] == ''){
			$pts_shipping_charge = 'NULL';
			}else{
			$pts_shipping_charge = (double)$data[1]['pts_shipping_charge'];
		}
	   }
	   if(isset($data[0]['pts_shipping_charge'])){
		if($data[0]['pts_shipping_charge'] == ''){
			$pts_shipping_charge = 'NULL';
			}else{
			$pts_shipping_charge = (double)$data[0]['pts_shipping_charge'];
		}
	   }
		$this->db->query("UPDATE " . DB_PREFIX . "product SET shipping_charge=".$pts_shipping_charge." WHERE product_id = '" . (int)$output . "'");
	 }
	}
	public function editShippingCharge(&$route, &$data, &$output) {
	    if(NULL !== $this->config->get('module_purpletree_multivendor_status')){
	      if(isset($data[1]['pts_shipping_charge'])){
			if($data[1]['pts_shipping_charge'] == ''){
				$pts_shipping_charge = 'NULL';
				}else{
				$pts_shipping_charge = (double)$data[1]['pts_shipping_charge'];
			}
		   }
		   if(isset($data[0]['pts_shipping_charge'])){
			if($data[0]['pts_shipping_charge'] == ''){
				$pts_shipping_charge = 'NULL';
				}else{
				$pts_shipping_charge = (double)$data[0]['pts_shipping_charge'];
			}
		   }
		$this->db->query("UPDATE " . DB_PREFIX . "product SET shipping_charge=".$pts_shipping_charge." WHERE product_id = '" . (int)$data[0] . "'");
	}
	}
}
