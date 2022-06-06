<?php
class ControllerInformationParagraph3 extends Controller {
	public function index() {
		$this->load->language('information/paragraph3');

		$this->load->model('catalog/paragraph3');
		$this->load->model('tool/image');
		
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'sort_order';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
			$this->document->setRobots('noindex,follow');
		} else {
			$page = 1;
		}

		if (isset($this->request->get['limit'])) {
			$limit = (int)$this->request->get['limit'];
			$this->document->setRobots('noindex,follow');
		} else {
			$limit = $this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit');
		}
		
		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('title'),
			'href' => $this->url->link('information/paragraph3', '')
		);
		
		$this->document->addStyle('catalog/view/theme/default/stylesheet/photo_gallery.css');
		$this->document->addStyle('catalog/view/javascript/fancyapps/fancybox.min.css');
		$this->document->addScript('catalog/view/javascript/fancyapps/fancybox.min.js');
		
		$this->document->addScript('catalog/view/javascript/masonry.pkgd.js');
		
		$this->document->setTitle($this->language->get('title'));
		$this->document->setDescription($this->language->get('meta_description'));
		$this->document->setKeywords($this->language->get('meta_keyword'));
		
        $data['paragraphs'] = array();
        
        $filter_data = array(
			'sort'               => $sort,
			'order'              => $order,
			'start'              => ($page - 1) * $limit,
			'limit'              => $limit
		);

		$paragraph_total = $this->model_catalog_paragraph3->getTotalParagraphs($filter_data);

		$results = $this->model_catalog_paragraph3->getParagraphs($filter_data);

		foreach ($results as $result) {
		    if ($result['image']) {
				$image = $server . 'image/' . $result['image'];
			} else {
				$image = false;
			}
			
			if ($result['image']) {
				$image_pop = $this->model_tool_image->resize($result['image'], 500, 500);
			} else {
				$image_pop = false;
			}
		    
		    $data['paragraphs'][] = array(
		        'image'      => $image,
		        'pop_image'      => $image_pop
		    );
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('information/paragraph3', $data));
	}

	
}