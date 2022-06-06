<?php
class ControllerExtensionAccountPurpletreeMultivendorStripeconnect extends Controller {
	public function index() {
		$this->load->model('extension/purpletree_multivendor/stripeconnect');
			$code='';
			$stripe = array();
			if(isset($this->request->get['code'])){
				$code=$this->request->get['code'];
			}
		$payment_mode = $this->config->get('payment_pts_stripe_payment_mode');
		if ($this->config->get('payment_pts_stripe_debug')) {
				$pmt_mode='Test';
			if($payment_mode){
				$pmt_mode='Live';
			}
			$this->log->write('Payment Mode' .$pmt_mode);
		}
		if($payment_mode){
			$stripe = array(
			  "secret_key"      => $this->config->get('payment_pts_stripe_secret_key_live'),
			  "publishable_key" => $this->config->get('payment_pts_stripe_publish_key_live')
			);
		} else {
			$stripe = array(
			  "secret_key"      => $this->config->get('payment_pts_stripe_secret_key_test'),
			  "publishable_key" => $this->config->get('payment_pts_stripe_publish_key_test')
			);
		}
		try{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://connect.stripe.com/oauth/token');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "client_secret=".$stripe['secret_key']."&code=".$code."&grant_type=authorization_code");
			$headers = array();
			$headers[] = 'Content-Type: application/x-www-form-urlencoded';
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			$result = json_decode(curl_exec($ch));
			if (curl_errno($ch)) {
				if ($this->config->get('payment_pts_stripe_debug')) {
					$this->log->write('Stripe connect curl error: ' .curl_error($ch));
				}
			}
			
		curl_close($ch);
		
		$store_detail=$this->customer->isSeller();
		$seller_id=$this->customer->isLogged();
		$this->log->write('Seller Id: '.$seller_id);
		if(isset($result->stripe_user_id)){
			if(isset($store_detail['store_status'])){
			$data=array(
			'seller_id'=>$seller_id,
			'account_id'=>$result->stripe_user_id,
			'livemode'=>$result->livemode,
			'scope'=>$result->scope
			);
			/* $num_of_acc= $this->model_extension_purpletree_multivendor_stripeconnect->checkAccountExist($data['account_id']);
			if($num_of_acc){
				$this->session->data['error_stripe_connect_warning']='Account already exist';
				if ($this->config->get('payment_pts_stripe_debug')) {
						$this->log->write('Account already exist');
				 }
			} */
			$res='';
			//if(!$num_of_acc){
			$res= $this->model_extension_purpletree_multivendor_stripeconnect->insertStripeAccount($data);
			//}
			if($res){
				$this->session->data['success_stripe_connect']='Stripe account has been connected successfully';
			if ($this->config->get('payment_pts_stripe_debug')) {
				$livemode='Test';
				if($data['livemode']){
					$livemode='Live';
				}
					$this->log->write('Account has been created successfully');
					$this->log->write('Account Id: ' .$data['account_id']);
					$this->log->write('Live Mode: ' .$livemode);
					$this->log->write('Scope: ' .$data['scope']);
			}	
			} else {
				if ($this->config->get('payment_pts_stripe_debug')) {
					$this->log->write('Account is not created');
				}
			}
		} else {
			if ($this->config->get('payment_pts_stripe_debug')) {
					$this->log->write('Store is not enable');
				}
		}
		 } else {
			if ($this->config->get('payment_pts_stripe_debug')) {
					$this->log->write('Error: '.$result->error_description);
				}
		} 
			
		}
		catch(Exception $e) {
			if ($this->config->get('payment_pts_stripe_debug')) {
				  $this->log->write('Stripe connect response message: ' .$e->getMessage());
			}
		}
		$this->response->redirect($this->url->link('extension/account/purpletree_multivendor/dashboardicons'));
	}	
}
?>