<?php
class ControllerExtensionModuleCategorycat extends Controller {
	public function index($setting) {
        static $module = 0;
		$this->load->language('extension/module/categorycat');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');
      
        $this->document->addScript('catalog/view/javascript/slick/slick.js');
        $this->document->addStyle('catalog/view/javascript/slick/slick.css');
        $this->document->addStyle('catalog/view/javascript/slick/slick-theme.css');
      
        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$server = $this->config->get('config_ssl') . 'image/';
		} else {
			$server = $this->config->get('config_url') . 'image/';
		}
      
        if($setting['image']) {
            $data['background'] = $server.$setting['image'];
        } else {
            $data['background'] = false;
        }
		
		$data['link'] = $setting['link'];
      
        $data['heading_title'] = $setting['name'];

		$data['products'] = array();
      
        $page = 1;
        $order = 'ASC';
        $sort = 'p.sort_order';
      
        $filter_data = array(
				'filter_manufacturer_id' => $setting['product_manufacturer'],
				'sort'                   => $sort,
				'order'                  => $order,
				'start'                  => ($page - 1) * $setting['limit'],
				'limit'                  => $setting['limit']
		);

		$results = $this->model_catalog_product->getProducts($filter_data);

		if ($results) {
			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if (!is_null($result['special']) && (float)$result['special'] >= 0) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					$tax_price = (float)$result['special'];
				} else {
					$special = false;
					$tax_price = (float)$result['price'];
				}
	
				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format($tax_price, $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = $result['rating'];
				} else {
					$rating = false;
				}
              
                if ($result['quantity'] <= 0) {
				    $stock = $result['stock_status'];
			    } else {
				    $stock = $this->language->get('text_instock');
			    }

				$data['products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
                    'compare'     => $this->model_catalog_product->getProductCompare($result['product_id']),
					'special'     => $special,
					'tax'         => $tax,
					'rating'      => $rating,
                    'ean'        => $result['ean'],
                    'stock'        => $stock,
                  'manufacturer'=> $result['manufacturer'],
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
				);
			}
          
            $data['module'] = $module++;

			if ($data['products']) {
                return $this->load->view('extension/module/categorycat', $data);
            }
		}
	}
}
