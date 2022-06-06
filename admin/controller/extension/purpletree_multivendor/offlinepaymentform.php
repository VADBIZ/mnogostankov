<?php
class ControllerExtensionPurpletreeMultivendorOfflinepaymentform extends Controller {
		private $error = array();
		
		public function index() {
			
			$this->load->language('purpletree_multivendor/stores');
			
			$this->document->setTitle($this->language->get('heading_title'));
			
			///Help code///	
			$data['helplink'] = "https://www.purpletreesoftware.com/knowledgebase/tag/opencart-multivendor-subscription";
			if (defined ('DISABLED_PTS_HELP')){if(DISABLED_PTS_HELP == 0){$data['helpcheck'] = 1;}else{$data['helpcheck'] = 0;}}else{$data['helpcheck'] = 1;}
			if ($this->request->server['HTTPS']) {
				$data['helpimage'] = HTTPS_SERVER . 'view/image/help.png';
			 } else {
				$data['helpimage'] = HTTP_SERVER . 'view/image/help.png';
			}
			/// End Help code///
			$data['breadcrumbs'] = array();
			
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
			);
			
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/purpletree_multivendor/seller_plan_view', 'user_token=' . $this->session->data['user_token'], true)
			);
			
			
			$data['heading_title'] = $this->language->get('heading_title');
			
			$data['user_token'] = $this->session->data['user_token'];
			
			if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
			} else {
			$data['error_warning'] = '';
			}
			
			if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			
			unset($this->session->data['success']);
			} else {
			$data['success'] = '';
			}
			
			if (isset($this->session->data['error_warning'])) {
			$data['error_warning'] = $this->session->data['error_warning'];
			
			unset($this->session->data['error_warning']);
			} else {
			$data['error_warning'] = '';
			}
			
			$this->load->model('setting/store');
			
			
			
			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');
			
			$this->response->setOutput($this->load->view('extension/purpletree_multivendor/offline_payment_form', $data));
			}
}