<?php
class ControllerInformationVakance extends Controller {
	public function index() {
		$this->load->language('information/vakance');
        $this->load->model('catalog/vacancy');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('information/vakance')
		);

        $this->document->addScript('catalog/view/javascript/modernizr.js');
		
		$data['option_values'] = array();
      
        $option_values = $this->model_catalog_vacancy->getOptionValue();
		
		foreach ($option_values as $value) {
		    $data['option_values'][] = array(
		        'name' => $value['name'],
			    'text' => html_entity_decode($value['text'], ENT_QUOTES, 'UTF-8'),
			    'link' => $value['link']
		    );
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('information/vakance', $data));
	}
}