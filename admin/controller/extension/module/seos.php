<?php
class ControllerExtensionModuleSeos extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/seos');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_seos', $this->request->post);

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
			'href' => $this->url->link('extension/module/seos', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/seos', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        $data['href1'] = $this->url->link('extension/module/seos/urls', 'user_token=' . $this->session->data['user_token'] . '&type=1', true);
        $data['href2'] = $this->url->link('extension/module/seos/urls', 'user_token=' . $this->session->data['user_token'] . '&type=2', true);
        $data['href3'] = $this->url->link('extension/module/seos/urls', 'user_token=' . $this->session->data['user_token'] . '&type=3', true);
        $data['href4'] = $this->url->link('extension/module/seos/urls', 'user_token=' . $this->session->data['user_token'] . '&type=4', true);

		if (isset($this->request->post['module_seos_status'])) {
			$data['module_seos_status'] = $this->request->post['module_seos_status'];
		} else {
			$data['module_seos_status'] = $this->config->get('module_seos_status');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/seos', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/seos')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

    public function urls() {
        $this->load->language('extension/module/seos');

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

        $this->response->redirect($this->url->link('extension/module/seos', 'user_token=' . $this->session->data['user_token'], true));
    }

    protected function getCategorySeoUrls() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category");
        foreach ($query->rows as $result) {
            $query_url = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'category_id=" . (int)$result['category_id'] . "'");

            if (!$query_url->num_rows) {

               $query_name = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_description WHERE category_id = '" . (int)$result['category_id'] . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

              $name = $query_name->row['name'];
              $keyword = $this->getUrls($name);
              $query_keyword = $this->db->query("SELECT * FROM `" . DB_PREFIX . "seo_url` WHERE keyword = '" . $this->db->escape($keyword) . "'");
              if ($query_keyword->num_rows) {
                  $keyword = $keyword . $result['category_id'];
              }

              $this->db->query("INSERT INTO `" . DB_PREFIX . "seo_url` SET store_id = '0', language_id = '" . (int)$this->config->get('config_language_id') . "', query = 'category_id=" . (int)$result['category_id'] . "', keyword = '" . $this->db->escape($keyword) . "'");
            }
        }
    }

    public function getUrls($text) {
	    $translit = array(

            'а' => 'a',   'б' => 'b',   'в' => 'v',

            'г' => 'g',   'д' => 'd',   'е' => 'e',

            'ё' => 'yo',   'ж' => 'zh',  'з' => 'z',

            'и' => 'i',   'й' => 'j',   'к' => 'k',

            'л' => 'l',   'м' => 'm',   'н' => 'n',

            'о' => 'o',   'п' => 'p',   'р' => 'r',

            'с' => 's',   'т' => 't',   'у' => 'u',

            'ф' => 'f',   'х' => 'x',   'ц' => 'c',

            'ч' => 'ch',  'ш' => 'sh',  'щ' => 'shh',

            'ь' => '',  'ы' => 'y',   'ъ' => '',

            'э' => 'e',   'ю' => 'yu',  'я' => 'ya',


            'А' => 'A',   'Б' => 'B',   'В' => 'V',

            'Г' => 'G',   'Д' => 'D',   'Е' => 'E',

            'Ё' => 'YO',   'Ж' => 'Zh',  'З' => 'Z',

            'И' => 'I',   'Й' => 'J',   'К' => 'K',

            'Л' => 'L',   'М' => 'M',   'Н' => 'N',

            'О' => 'O',   'П' => 'P',   'Р' => 'R',

            'С' => 'S',   'Т' => 'T',   'У' => 'U',

            'Ф' => 'F',   'Х' => 'X',   'Ц' => 'C',

            'Ч' => 'CH',  'Ш' => 'SH',  'Щ' => 'SHH',

            'Ь' => '',  'Ы' => 'Y\'',   'Ъ' => '',

            'Э' => 'E',   'Ю' => 'YU',  'Я' => 'YA', ' ' => '-',
            '/' => '',    ',' => '',    "«"=>"", "»"=>"", "„"=>"", "“"=>"", "“"=>"", "”"=>"", "."=>"", '²' => ''
        );

        $keywod = strtr($text, $translit);
        $keyword = $keywod;

        $str = trim($keyword);
        $str = preg_replace('|_|','-',$str);
        $str = preg_replace('![^\w\d\s\-]*!u','',$str); // u - чтобы в том числе № и другие не ASCII-символы
        $str = preg_replace('/\s+/', '-', $str); // Убрать двойные пробелы
        $str = preg_replace('| |','-',$str); // Заменить одинарные пробелы на тире
        $str = preg_replace('|-+|','-',$str); // Заменить поторяющиеся тире на единичное
        $str = preg_replace( array('!^-!', '!-$!'),array('', ''), $str); // Убрать тире в начале и в конце строки

        return $str;
	}

    protected function getProductSeoUrls() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product");
        foreach ($query->rows as $result) {
            $query_url = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'product_id=" . (int)$result['product_id'] . "' AND keyword != ''");

            if (!$query_url->num_rows) {
          // $this->log->write(print_r($query_url,true));
              $query_name = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$result['product_id'] . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

              $name = $query_name->row['name'];
              $keyword = $this->getUrls($name);
              $query_keyword = $this->db->query("SELECT * FROM `" . DB_PREFIX . "seo_url` WHERE keyword = '" . $this->db->escape($keyword) . "'");
              if ($query_keyword->num_rows) {
                  $keyword = $keyword . $result['product_id'];
              }
              $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'product_id=" . (int)$result['product_id'] . "'"
              $this->db->query("INSERT INTO `" . DB_PREFIX . "seo_url` SET store_id = '0', language_id = '" . (int)$this->config->get('config_language_id') . "', query = 'product_id=" . (int)$result['product_id'] . "', keyword = '" . $this->db->escape($keyword) . "'");
            }
        }
    }

    protected function getInformationSeoUrls() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information");
        foreach ($query->rows as $result) {
            $query_url = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'information_id=" . (int)$result['information_id'] . "'");

            if (!$query_url->num_rows) {

              $query_name = $this->db->query("SELECT * FROM " . DB_PREFIX . "information_description WHERE information_id = '" . (int)$result['information_id'] . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

              $name = $query_name->row['title'];
              $keyword = $this->getUrls($name);
              $query_keyword = $this->db->query("SELECT * FROM `" . DB_PREFIX . "seo_url` WHERE keyword = '" . $this->db->escape($keyword) . "'");
              if ($query_keyword->num_rows) {
                  $keyword = $keyword . $result['information_id'];
              }

              $this->db->query("INSERT INTO `" . DB_PREFIX . "seo_url` SET store_id = '0', language_id = '" . (int)$this->config->get('config_language_id') . "', query = 'information_id=" . (int)$result['information_id'] . "', keyword = '" . $this->db->escape($keyword) . "'");
            }
        }
    }

    protected function getManufacturerSeoUrls() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manufacturer");
        foreach ($query->rows as $result) {
            $query_url = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'manufacturer_id=" . (int)$result['manufacturer_id'] . "'");

            if (!$query_url->num_rows) {

              $name = $result['name'];
              $keyword = $this->getUrls($name);
              $query_keyword = $this->db->query("SELECT * FROM `" . DB_PREFIX . "seo_url` WHERE keyword = '" . $this->db->escape($keyword) . "'");
              if ($query_keyword->num_rows) {
                  $keyword = $keyword . $result['product_id'];
              }

              $this->db->query("INSERT INTO `" . DB_PREFIX . "seo_url` SET store_id = '0', language_id = '" . (int)$this->config->get('config_language_id') . "', query = 'manufacturer_id=" . (int)$result['manufacturer_id'] . "', keyword = '" . $this->db->escape($keyword) . "'");
            }
        }
    }
}
