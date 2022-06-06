<?php
use Dompdf\Dompdf;
class ControllerExtensionModulePrintVersionProduct extends Controller {
	private $error = array();

	public function index() {
        require_once(DIR_SYSTEM . 'library/dompdf/autoload.inc.php');
		$data['direction'] = $this->language->get('direction');
		$data['lang'] = $this->language->get('code');
		
		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}
		
		$data['base'] = $server;
		
		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
		} else {
			$data['logo'] = '';
		}
		
		$this->load->language('information/contact');
		$this->load->language('product/product');
		$this->load->language('extension/module/print_version');
		
		$this->load->model('catalog/manufacturer');

		if (isset($this->request->get['print_id'])) {
			$product_id = (int)$this->request->get['print_id'];
		} else {
			$product_id = 0;
		}

		$this->load->model('catalog/product');

		$product_info = $this->model_catalog_product->getProduct($product_id);

		if ($product_info) {

			$data['href'] = $this->url->link('product/product', 'product_id=' . $product_id);
			$data['heading_title'] = $product_info['name'];
			
			$data['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_info['minimum']);
			$data['tab_review'] = sprintf($this->language->get('tab_review'), $product_info['reviews']);
			
			$data['store'] = $this->config->get('config_name');
			$data['address'] = nl2br($this->config->get('config_address'));
			$data['telephone'] = $this->config->get('config_telephone');
			$data['fax'] = $this->config->get('config_fax');
			$data['email'] = $this->config->get('config_email');
            $data['textv'] = html_entity_decode($this->config->get('config_textv'), ENT_QUOTES, 'UTF-8');
            $data['textn'] = html_entity_decode($this->config->get('config_textn'), ENT_QUOTES, 'UTF-8');
			$data['store_domain'] = preg_replace("(^https?://)", "", $server );
			if(substr($data['store_domain'], -1) == '/') {
				$data['store_domain'] = substr($data['store_domain'], 0, -1);
			}
		
			$data['product_id'] = $product_id;
			$data['manufacturer'] = $product_info['manufacturer'];
			$data['model'] = $product_info['model'];
			$data['sku'] = $product_info['ean'];
            $data['mpn'] = $product_info['mpn'];
			$data['reward'] = $product_info['reward'];
			$data['points'] = $product_info['points'];
			$data['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');
			
			if ($product_info['quantity'] <= 0) {
				$data['stock'] = $product_info['stock_status'];
			} elseif ($this->config->get('config_stock_display')) {
				$data['stock'] = $product_info['quantity'];
			} else {
				$data['stock'] = $this->language->get('text_instock');
			}
          
          
          
            $data['text_tax'] = $this->model_catalog_product->getTax($product_info['tax_class_id']);
          

			$this->load->model('tool/image');

			if ($product_info['image']) {
				$data['image'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'));
			} else {
				$data['image'] = '';
			}			

			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                $data['price_new'] = $this->currency->format($product_info['price'], $this->session->data['currency']);
			} else {
				$data['price'] = false;
                $data['price_new'] = false;
			}

			if ((float)$product_info['special']) {
				$data['special'] = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$data['special'] = false;
			}

			if ($this->config->get('config_tax')) {
				$data['tax'] = number_format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], 0, '.', '') . ' P';
			} else {
				$data['tax'] = false;
			}

			$discounts = $this->model_catalog_product->getProductDiscounts($product_id);

			$data['discounts'] = array();

			foreach ($discounts as $discount) {
				$data['discounts'][] = array(
					'quantity' => $discount['quantity'],
					'price'    => number_format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), 0, '.', '') . ' P'
				);
			}

			$data['options'] = array();
			
			foreach ($this->model_catalog_product->getProductOptions($product_id) as $option) {
				$product_option_value_data = array();
				foreach ($option['product_option_value'] as $option_value) {
					if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
						if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
							$price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);
						} else {
							$price = false;
						}
						$product_option_value_data[] = array(
							'product_option_value_id' => $option_value['product_option_value_id'],
							'option_value_id'         => $option_value['option_value_id'],
							'name'                    => $option_value['name'],
							'image'                   => $this->model_tool_image->resize($option_value['image'], 50, 50),
							'price'                   => $price,
							'price_prefix'            => $option_value['price_prefix']
						);
					}
				}
				$data['options'][] = array(
					'product_option_id'    => $option['product_option_id'],
					'product_option_value' => $product_option_value_data,
					'option_id'            => $option['option_id'],
					'name'                 => $option['name'],
					'type'                 => $option['type'],
					'value'                => $option['value'],
					'required'             => $option['required']
				);
			}

			if ($product_info['minimum']) {
				$data['minimum'] = $product_info['minimum'];
			} else {
				$data['minimum'] = 1;
			}
			
			$data['review_status'] = $this->config->get('config_review_status');
			$data['reviews'] = sprintf($this->language->get('text_reviews'), (int)$product_info['reviews']);
			$data['rating'] = (int)$product_info['rating'];

			$data['attribute_groups'] = $this->model_catalog_product->getProductAttributes($product_id);

			$data['recurrings'] = $this->model_catalog_product->getProfiles($product_id);
			
			$data['products'] = array();
			$results = $this->model_catalog_product->getProductRelated($product_id);
			foreach ($results as $result) {
				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax'));
				} else {
					$price = false;
				}
				if ((float)$result['special']) {
					$special = $this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax'));
				} else {
					$special = false;
				}
				if ($this->config->get('config_tax')) {
					$tax = (float)$result['special'] ? $result['special'] : $result['price'];
				} else {
					$tax = false;
				}
				
				$data['products'][] = array(
					'product_id'  => $result['product_id'],
					'name'        => $result['name'],
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
				);
			}
          
            
            $data['today'] = date("d.m.Y");  
            
			// НАЧАЛО PDF
            $html = '<html><head>
	        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	        <style type="text/css">* { font-family: arial;font-size: 14px;line-height: 14px;}
            p {margin: 0 0 10px;}
            h1 {font-size: 26px;}
            h2 {font-size: 20px;font-weight: 800;}
            .h1, .h2, .h3, h1, h2, h3 {margin-top: 20px;margin-bottom: 10px;}
            .h1, .h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6 {font-weight: 500;line-height: 1.1;}
            .btn-group-vertical > .btn-group::after, .btn-toolbar::after, .clearfix::after, .container-fluid::after, .container::after, .dl-horizontal dd::after, .form-horizontal .form-group::after, .modal-footer::after, .nav::after, .navbar-collapse::after, .navbar-header::after, .navbar::after, .pager::after, .panel-body::after, .row::after {clear: both;}
            .col-xs-1, .col-xs-10, .col-xs-11, .col-xs-12, .col-xs-2, .col-xs-3, .col-xs-4, .col-xs-5, .col-xs-6, .col-xs-7, .col-xs-8, .col-xs-9 {float: left;}
            .col-lg-1, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-md-1, .col-md-10, .col-md-11, .col-md-12, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-sm-1, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-xs-1, .col-xs-10, .col-xs-11, .col-xs-12, .col-xs-2, .col-xs-3, .col-xs-4, .col-xs-5, .col-xs-6, .col-xs-7, .col-xs-8, .col-xs-9 { position: relative;min-height: 1px;padding-right: 15px;padding-left: 15px;}
            .row {margin-right: -15px;margin-left: -15px;}
            .col-xs-12 {width: 100%;}
            .col-xs-6 {width: 50%;}
		    table {margin: 0 0 15px 0;width: 100%;border-collapse: collapse; border-spacing: 0;}		
	        table td {padding: 5px;}	
		    table th {padding: 5px;font-weight: bold;}
            .table {width: 100%;max-width: 100%;margin-bottom: 20px;}
		    .table-bordered {border: 1px solid #ddd;}
            .table-bordered > thead > tr > th, .table-bordered > tbody > tr > th, .table-bordered > tfoot > tr > th, .table-bordered > thead > tr > td, .table-bordered > tbody > tr > td, .table-bordered > tfoot > tr > td  border: 1px solid #ddd;}
            .text-right {text-align: right;}
            .table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {padding: 8px; line-height: 1.42857143; vertical-align: top;border-top: 1px solid #ddd;}
            .table thead > tr > td, .table tbody > tr > td {vertical-align: middle;}
            .img-responsive, .thumbnail a > img, .thumbnail > img {display: block;max-width: 100%;height: auto;}
	        </style>
            </head>
            <body><div class="container">';
          
          
            $html .= '<div id="content"><div class="row" style="margin-top:20px;"><div class="col-xs-12"><p><br>'.$data['textv'].'</p></div></div>
		    <h4 style="text-align: center;margin-top: 20px;">Коммерческое предложение от '.$data['today'].'</h4><hr>';

		    $html .= '<div class="row"><div class="col-xs-6">';
		    if($data['image']) {
				$html .= '<img src="image/'.$product_info['image'].'"  title="'.$data['heading_title'].'" alt="'.$data['heading_title'].'" class="img-responsive" />';
            }
			$html .= '</div>';
			$html .= '<div class="col-xs-6"><h1 class="title page-title">'.$data['heading_title'].'</h1>';

			$html .= '<ul class="list-unstyled">';
            if($data['mpn']) {
              $html .= '<li class="product-mpn"><span name-new" style="">'.$data['mpn'].'</span></li>';
            }
			$html .= '<li class="product-sku"><b>'.$this->language->get('text_sku').':</b> <span> '.$data['sku'].'</span></li>
			<li class="brand-image product-manufacturer"><b>'.$this->language->get('text_manufacturer').'</b> <span> '.$data['manufacturer'].'</span></li>
			<li class="product-model"><b>'.$this->language->get('text_model').'</b> <span>'.$data['model'].'</span></li>';
			$html .= '<li class="product-stock ';
            if((int)$product_info['quantity'] > 0) {
            $html .= 'in-stock ';
            } else {
            $html .= 'out-of-stock ';
            }
            $html .= '"><b>'.$this->language->get('text_stock').'</b> <span>'.$data['stock'].'</span></li></ul>';
			if($data['price']) {
				$html .= '<ul class="list-unstyled">';
				if(!$data['special']) {
				    $html .= '<li><h2><div class="product-price"><b>Цена:</b> '.$data['price'].'</div></h2></li>';
                } else {
				    $html .= '<li><h2><b>Цена:</b> <span class="product-price-old" style="text-decoration: line-through;">'.$data['price'].'</span> <span class="product-price-new">'.$data['special'].'</span></h2></li>';
				}

				if($data['text_tax']) {
                  $html .= '<li><h2><p>Без налога: '.$data['price_new'].'</p></h2></li>';
                }

				$html .= '</ul>';
			}

            //$html .= '<div class="block-body expand-block"><a class ="btn"  href="javascript:(print());"> Распечатать</a></div>';
			$html .= '</div></div></div>';
	        $html .= '<div class="tab-content" style="width:100%;"><div id="tab-description"><h4>'.$this->language->get('tab_description').'</h4>'.$data['description'].'</div>';
	        if($data['attribute_groups']) {
		    $html .= '<div id="tab-specification">
		    <h4>'.$this->language->get('tab_attribute').'</h4>
		    <table class="table table-bordered">';
			foreach ($data['attribute_groups'] as $attribute_group) {
			$html .= '<tbody>';
			  foreach ($attribute_group['attribute'] as $attribute) {
			  $html .= '<tr>
				<td>'.$attribute['name'].'</td>
				<td>'.$attribute['text'].'</td>
			  </tr>';
			  }
			$html .= '</tbody>';
			}
		  $html .= '</table></div>';
		  }
	      $html .= '</div>';
	   
	      $html .= '<div id="tab-related">'.$data['textn'].'</div>';
          
          
           $html .= '</div></body></html>';

		  $dompdf = new Dompdf();
          $dompdf->loadHtml($html, 'UTF-8');

         $dompdf->setPaper('A4', 'portrait');
         $dompdf->render();
 
         // Вывод файла в браузер:
         $dompdf->stream('Commercial-proposal');         
			//$this->response->setOutput($this->load->view('extension/module/print_version_product', $data));
		} else {
			$url = '';

			if (isset($this->request->get['path'])) {
				$url .= '&path=' . $this->request->get['path'];
			}

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['manufacturer_id'])) {
				$url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
			}

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('product/product', $url . '&product_id=' . $product_id)
			);

			$this->document->setTitle($this->language->get('text_error'));

			$data['heading_title'] = $this->language->get('text_error');

			$data['text_error'] = $this->language->get('text_error');

			$data['button_continue'] = $this->language->get('button_continue');

			$data['continue'] = $this->url->link('common/home');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}
}