<?php
class ControllerCatalogVacancy extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('catalog/vacancy');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/vacancy');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/vacancy', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('catalog/vacancy/add', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true);
      
        $data['option_values'] = $this->model_catalog_vacancy->getOptionValue();
      
        $data['user_token'] = $this->session->data['user_token'];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/vacancy', $data));
	}
  
    public function add() {
		$this->load->language('catalog/vacancy');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/vacancy');

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$this->model_catalog_vacancy->addOption($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');
		}

		$this->response->redirect($this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true));
	}
}