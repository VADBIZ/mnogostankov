<?php
class ControllerExtensionModuleSeoss extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/seoss');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_seoss', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

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
			'href' => $this->url->link('extension/module/seoss', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/seoss', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        $data['href1'] = $this->url->link('extension/module/seoss/urls', 'user_token=' . $this->session->data['user_token'] . '&type=1', true);
        $data['href2'] = $this->url->link('extension/module/seoss/urls', 'user_token=' . $this->session->data['user_token'] . '&type=2', true);
        $data['href3'] = $this->url->link('extension/module/seoss/urls', 'user_token=' . $this->session->data['user_token'] . '&type=3', true);
        $data['href4'] = $this->url->link('extension/module/seoss/urls', 'user_token=' . $this->session->data['user_token'] . '&type=4', true);

		if (isset($this->request->post['module_seoss_status'])) {
			$data['module_seoss_status'] = $this->request->post['module_seoss_status'];
		} else {
			$data['module_seoss_status'] = $this->config->get('module_seoss_status');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/seoss', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/seoss')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

    public function urls() {
        $this->load->language('extension/module/seoss');

		$this->document->setTitle($this->language->get('heading_title'));

        if (isset($this->request->get['type']) && $this->validate()) {
            if($this->request->get['type'] == 1) {
                $this->getCategorySeoUrls();
                $this->session->data['success'] = $this->language->get('text_success1');
            }

            if($this->request->get['type'] == 2) {
                $this->getProductSeoUrls();
                $this->session->data['success'] = $this->language->get('text_success2');
            }

            if($this->request->get['type'] == 3) {
                $this->getInformationSeoUrls();
                $this->session->data['success'] = $this->language->get('text_success3');
            }

            if($this->request->get['type'] == 4) {
                $this->getManufacturerSeoUrls();
                $this->session->data['success'] = $this->language->get('text_success4');
            }

        }

        $this->response->redirect($this->url->link('extension/module/seoss', 'user_token=' . $this->session->data['user_token'], true));
    }

    protected function getCategorySeoUrls() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category");
        foreach ($query->rows as $result) {
            if($result['indexnow'] == '') {
                $this->load->model('setting/setting');
                $this->model_setting_setting->indexProduct('category', $result['category_id']);
            }
        }
    }

    protected function getProductSeoUrls() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product");
        foreach ($query->rows as $result) {
            if($result['indexnow'] == '') {
                $this->load->model('setting/setting');
                $this->model_setting_setting->indexProduct('product', $result['product_id']);
            }
        }
    }

    protected function getInformationSeoUrls() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information");
        foreach ($query->rows as $result) {
            if($result['indexnow'] == '') {
                $this->load->model('setting/setting');
                $this->model_setting_setting->indexProduct('information', $result['information_id']);
            }
        }
    }

    protected function getManufacturerSeoUrls() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manufacturer");
        foreach ($query->rows as $result) {
            if($result['indexnow'] == '') {
                $this->load->model('setting/setting');
                $this->model_setting_setting->indexProduct('manufacturer', $result['manufacturer_id']);
            }
        }
    }


}
