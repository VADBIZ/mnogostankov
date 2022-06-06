<?php
class ControllerCatalogDocuments extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('catalog/documents');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/documents');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/documents', 'user_token=' . $this->session->data['user_token'], true)
		);
      
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

		$data['action'] = $this->url->link('catalog/documents/add', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true);
      
        $data['option_values'] = $this->model_catalog_documents->getOptionValue();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/documents', $data));
	}
  
    public function add() {
		$this->load->language('catalog/documents');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/documents');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_documents->addOption($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');
		}

		$this->response->redirect($this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true));
	}
  
    protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/documents')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}