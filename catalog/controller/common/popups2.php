<?php
class ControllerCommonPopups2 extends Controller {
	public function index() {
		$this->load->language('common/popups2');

        $data['name'] = $this->customer->getFirstName();
        $data['email'] = $this->customer->getEmail();
        $data['telephone'] = '';
        $data['action'] = $this->url->link('common/popups2/add', '', true);
        $data['ur'] = $this->config->get('config_ur');

        $data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'), $this->error);

		$this->response->setOutput($this->load->view('common/popups2', $data));
	}

    public function add() {
        $this->load->language('common/popups2');

		$json = array();

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 25)) {
				$json['error']['name'] = $this->language->get('error_name');
			}

			if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
			    $json['error']['telephone'] = $this->language->get('error_telephone');
		    }

            if (!filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
			    $json['error']['email'] = $this->language->get('error_email');
		    }

			if (!isset($this->request->post['agree'])) {
				$json['error']['agree'] = $this->language->get('error_agree');
			}

            if (!isset($json['error'])) {
				$this->load->model('catalog/review');

				$this->model_catalog_review->addReviews($this->request->post);

				$json['success'] = $this->language->get('text_success');

            }

        }

        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
}
