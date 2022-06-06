<?php
class ControllerExtensionAccountPurpletreeMultivendorApiSellerlogin extends Controller{
		private $error = array();  
		public function index(){
			$json['message']=array();
			$json['status']=array();
			$this->load->language('purpletree_multivendor/api');
		if ($this->config->get('module_purpletree_multivendor_status')) {
			if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
				$seller_email=$this->request->post['email'];
				$seller_password=$this->request->post['password'];
				$this->load->model('extension/purpletree_multivendor/vendor');
				$seller_id = $this->model_extension_purpletree_multivendor_vendor->getSellerId($seller_email);
				$isSeller = $this->model_extension_purpletree_multivendor_vendor->is_seller($seller_id);
		if($isSeller){
			$this->load->model('account/customer');
			$this->model_account_customer->deleteLoginAttempts($seller_email);
			$seller_loggedin=$this->customer->login($seller_email, $seller_password);
			unset($this->session->data['guest']);
			if($seller_loggedin){
				$json['message'] = $this->language->get('seller_login_succ');
				$json['status'] = true;
			} else {
				$json['status'] = false;
				$json['message'] = $this->language->get('seller_login_fail');
			}
			} else {
				$json['status'] = false;
				$json['message'] = $this->language->get('seller_login_fail');// Please enter valid seller credential.
			}
		}
		} else {
		$json['message'] = $this->language->get('multivendor_enable');
		$json['status'] = false;
		}			
		$this->response->addHeader('Content-Type: application/json');
		return $this->response->setOutput(json_encode($json));
		}
}
?>