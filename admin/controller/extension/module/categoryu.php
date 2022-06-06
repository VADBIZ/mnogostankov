<?php
class ControllerExtensionModuleCategoryu extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/categoryu');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/categoryu', 'user_token=' . $this->session->data['user_token'], true)
		);
      
        $data['user_token'] = $this->session->data['user_token'];

		$data['action'] = $this->url->link('extension/module/categoryu/delet', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/categoryu', $data));
	}
  
    public function delet() {
        $json = array();
      
        $this->load->language('extension/module/categoryu');
      
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            if(isset($this->request->post['url']) && $this->request->post['url'] != '') {
                $this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE keyword = '" . $this->db->escape($this->request->post['url']) . "'");
              
              $json['success'] = $this->language->get('text_success');
            }
        }
      
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/categoryu')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}