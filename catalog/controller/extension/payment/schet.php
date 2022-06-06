<?php
class ControllerExtensionPaymentSchet extends Controller {
	public function index() {
		$this->load->language('extension/payment/schet');

		$data['text_instruction'] = $this->language->get('text_instruction');

		if (nl2br($this->config->get('payment_schet_description' . $this->config->get('config_language_id')))) {
			$data['description'] = nl2br($this->config->get('payment_schet_description' . $this->config->get('config_language_id')));
		} else {
			$data['description'] = $this->language->get('text_description');
		}
		
		$data['print'] = $this->url->link('extension/payment/schet/print_schet');		

		$data['continue'] = $this->url->link('checkout/success');
		
		$data['button_print'] = $this->language->get('button_print');
		$data['button_confirm'] = $this->language->get('button_confirm');

		return $this->load->view('extension/payment/schet', $data);
	}
	
	public function print_schet() {
		
	
		$this->load->language('checkout/checkout');
      $this->load->language('extension/payment/schet');
			
		$data['title'] = $this->language->get('text_title');

		if ($this->request->server['HTTPS']) {
			$data['base'] = HTTPS_SERVER;
		} else {
			$data['base'] = HTTP_SERVER;
		}
		$error_user = 1;
		$data['direction'] = $this->language->get('direction');
		$data['lang'] = $this->language->get('code');
			
		
			
			$data['payment_schet_inn'] = $this->config->get('payment_schet_inn');
			$data['payment_schet_kpp'] = $this->config->get('payment_schet_kpp');
			$data['payment_schet_account'] = $this->config->get('payment_schet_account');
			$data['payment_schet_bik'] = $this->config->get('payment_schet_bik');
			$data['payment_schet_corschet'] = $this->config->get('payment_schet_corschet');
			$data['payment_schet_supplier'] = nl2br($this->config->get('payment_schet_supplier' . $this->config->get('config_language_id')));
			$data['payment_schet_bank_supplier'] = nl2br($this->config->get('payment_schet_bank_supplier' . $this->config->get('config_language_id')));
			$data['payment_schet_supplier'] = nl2br($this->config->get('payment_schet_supplier' . $this->config->get('config_language_id')));
			$data['payment_schet_legal_address'] = nl2br($this->config->get('payment_schet_legal_address' . $this->config->get('config_language_id')));
			$data['payment_schet_actual_address'] = nl2br($this->config->get('payment_schet_actual_address' . $this->config->get('config_language_id')));
			$data['payment_schet_telephone'] = $this->config->get('payment_schet_telephone');
			$data['payment_schet_mobilephone'] = $this->config->get('payment_schet_mobilephone');
			$data['payment_schet_director'] = nl2br($this->config->get('payment_schet_director' . $this->config->get('config_language_id')));
			$data['payment_schet_accountant'] = nl2br($this->config->get('payment_schet_accountant' . $this->config->get('config_language_id')));
			
			if ($this->config->get('payment_schet_image_signature')) {
				$payment_schet_image_signature = $this->config->get('payment_schet_image_signature');
			} else {
				$payment_schet_image_signature = '';
			}
			
			if ($this->config->get('payment_schet_signature_width')) {
				$payment_schet_signature_width = $this->config->get('payment_schet_signature_width');
			} else {
				$payment_schet_signature_width = 200;
			}
			
			if ($this->config->get('payment_schet_signature_height')) {
				$payment_schet_signature_height = $this->config->get('payment_schet_signature_height');
			} else {
				$payment_schet_signature_height = 50;
			}
			
			if ($this->config->get('payment_schet_image_signature_accountant')) {
				$payment_schet_image_signature_accountant = $this->config->get('payment_schet_image_signature_accountant');
			} else {
				$payment_schet_image_signature_accountant = '';
			}
			
			if ($this->config->get('payment_schet_signature_accountant_width')) {
				$payment_schet_signature_accountant_width = $this->config->get('payment_schet_signature_accountant_width');
			} else {
				$payment_schet_signature_accountant_width = 200;
			}
			
			if ($this->config->get('payment_schet_signature_accountant_height')) {
				$payment_schet_signature_accountant_height = $this->config->get('payment_schet_signature_accountant_height');
			} else {
				$payment_schet_signature_accountant_height = 50;
			}
			
			if ($this->config->get('payment_schet_image_stamp')) {
				$payment_schet_image_stamp = $this->config->get('payment_schet_image_stamp');
			} else {
				$payment_schet_image_stamp = '';
			}
			
			if ($this->config->get('payment_schet_stamp_width')) {
				$payment_schet_stamp_width = $this->config->get('payment_schet_stamp_width');
			} else {
				$payment_schet_stamp_width = 110;
			}
			
			if ($this->config->get('payment_schet_stamp_height')) {
				$payment_schet_stamp_height = $this->config->get('payment_schet_stamp_height');
			} else {
				$payment_schet_stamp_height = 110;
			}
			
			$this->load->model('tool/image');
			
			$data['payment_schet_image_signature'] = $this->model_tool_image->resize($payment_schet_image_signature, $payment_schet_signature_width, $payment_schet_signature_height);
			$data['payment_schet_image_signature_accountant'] = $this->model_tool_image->resize($payment_schet_image_signature_accountant, $payment_schet_signature_accountant_width, $payment_schet_signature_accountant_height);
			$data['payment_schet_image_stamp'] = $this->model_tool_image->resize($payment_schet_image_stamp, $payment_schet_stamp_width, $payment_schet_stamp_height);
			
			if (!empty($this->request->get['order_id']) && (isset($this->session->data['user_token']) || $this->customer->isLogged())) {
				
				$error_user = 0;
				$order_id = intval($this->request->get['order_id']);

				$this->load->model('checkout/order');
				$order_info = $this->model_checkout_order->getOrder($order_id);

				if ($this->customer->isLogged() && !isset($this->session->data['user_token']) && !empty($order_info['order_id'])) {
					$customer_id = $this->customer->getId();
					if ($customer_id != $order_info['customer_id']) { $error_user = 1; }
				} elseif (empty($order_info['order_id'])) {
					$error_user = 1;
				}

				if ($error_user == 0) {
					$this->load->model('account/order');
					$this->load->model('catalog/product');
					$this->load->model('tool/upload');
					
					$data['products'] = array();
                  
                    $total1 = 0;
                    $total2 = 0;
                    $total3 = 0;
                    $total4 = 0;

					$products = $this->model_account_order->getOrderProducts($order_id);

					foreach ($products as $product) {
						$option_data = array();

						$options = $this->model_account_order->getOrderOptions($order_id, $product['order_product_id']);

						foreach ($options as $option) {
							if ($option['type'] != 'file') {
								$value = $option['value'];
							} else {
								$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

								if ($upload_info) {
									$value = $upload_info['name'];
								} else {
									$value = '';
								}
							}

							$option_data[] = array(
								'name'  => $option['name'],
								'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
							);
						}

						$product_info = $this->model_catalog_product->getProduct($product['product_id']);
                      
                        if((float)$product['tax'] > 0){
                            $tax_proc = ($product['tax']/$product['price'])*100;
                        } else {
                            $tax_proc = 'Без НДС';
                        }
                      
                        $total1 = $total1 + ($product['price'] * $product['quantity']);
                        $total2 = $total2 + ($product['tax'] * $product['quantity']);
                        $total3 = $total3 + $product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0);
                        $total4 = $total4 + $product['quantity'];

						$data['products'][] = array(
							'name'     => $product['name'],
							'model'    => $product['model'],
							'option'   => $option_data,
							'quantity' => $product['quantity'],
                            'tax_proc'    => $tax_proc,
                            'price_new'    => $this->currency->format($product['price'], $order_info['currency_code'], $order_info['currency_value']),
                            'tax'    => $this->currency->format(($this->config->get('config_tax') ? $product['tax'] * $product['quantity'] : 0), $order_info['currency_code'], $order_info['currency_value']),
							'price'    => $this->currency->format($product['price'] * $product['quantity'], $order_info['currency_code'], $order_info['currency_value']),
							'total'    => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']),
							'return'   => $this->url->link('account/return/add', 'order_id=' . $order_info['order_id'] . '&product_id=' . $product['product_id'], true)
						);
					}
                  
                    $data['total1'] = $this->currency->format($total1, $order_info['currency_code'], $order_info['currency_value']);
                    $data['total2'] = $this->currency->format($total2, $order_info['currency_code'], $order_info['currency_value']);
                    $data['total3'] = $this->currency->format($total3, $order_info['currency_code'], $order_info['currency_value']);
                    $data['total4'] = $total4;

					// Voucher
					$data['vouchers'] = array();

					$vouchers = $this->model_account_order->getOrderVouchers($order_id);

					foreach ($vouchers as $voucher) {
						$data['vouchers'][] = array(
							'description' => $voucher['description'],
							'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value'])
						);
					}

					// Totals
					$data['totals'] = array();

					$totals = $this->model_account_order->getOrderTotals($order_id);

					foreach ($totals as $total) {
						$data['totals'][] = array(
							'title' => $total['title'],
							'text'  => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value']),
						);
					}
					
					$this->load->model('account/customer');
					$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
					
					if (isset($customer_info['telephone'])) {
						$customer_telephone = ', ' . $this->language->get('text_telephones') . ' ' . $customer_info['telephone'];
					} else {
						$customer_telephone = '';
					}
					
					if (isset($order_info['payment_company'])) {
						$payment_company = ', ' . $order_info['payment_company'];
					} else {
						$payment_company = '';
					}
					
					$data['text_payment_schet_title'] = sprintf($this->language->get('text_payment_schet_title'), $order_info['order_id'], date($this->language->get('date_format_short'), strtotime($order_info['date_added'])));
					$data['buyer'] = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'] . $payment_company . ', ' . $order_info['payment_postcode'] . ', ' . $order_info['payment_zone'] . ', ' . $order_info['payment_city'] . ', ' . $order_info['payment_address_1'] . $customer_telephone;
				
					$this->load->model('tool/summ');
					
					$rur_code = 'RUB';
					$rur_order_total = $this->currency->convert($order_info['total'], $order_info['currency_code'], $rur_code);
					$price_convert = $this->model_tool_summ->num2str($this->currency->format($rur_order_total, $rur_code, $order_info['currency_value'], FALSE));
					
					$this->load->model('account/order');
					
					$shet_product_total = 0;
					$ordered_products = $this->model_account_order->getOrderProducts($order_id);
					foreach ($ordered_products as $op) {
						$shet_product_total = $shet_product_total + $op['quantity'];
					}
				
					$shet_voucher_total = $this->model_account_order->getTotalOrderVouchersByOrderId($order_id);

					
					$data['items'] = sprintf($this->language->get('text_items'), ($shet_product_total + $shet_voucher_total), $this->currency->format($rur_order_total, $rur_code, $order_info['currency_value']));
						
					$first_part = mb_substr($price_convert,0,1, 'UTF-8');
					$last_part = mb_substr($price_convert,1);
					$first_part = mb_strtoupper($first_part, 'UTF-8');
					$data['amount'] = $first_part.$last_part;
				} else {
					$error_user = 1;
				}
			} else {

				if (isset($this->session->data['order_id'])) {
				$redirect = '';
				$error_user = 0;
				if ($this->cart->hasShipping()) {
					// Validate if shipping address has been set.
					if (!isset($this->session->data['shipping_address'])) {
						$redirect = $this->url->link('checkout/checkout', '', true);
					}

					// Validate if shipping method has been set.
					if (!isset($this->session->data['shipping_method'])) {
						$redirect = $this->url->link('checkout/checkout', '', true);
					}
				} else {
					unset($this->session->data['shipping_address']);
					unset($this->session->data['shipping_method']);
					unset($this->session->data['shipping_methods']);
				}

				// Validate if payment address has been set.
				if (!isset($this->session->data['payment_address'])) {
					$redirect = $this->url->link('checkout/checkout', '', true);
				}

				// Validate if payment method has been set.
				if (!isset($this->session->data['payment_method'])) {
					$redirect = $this->url->link('checkout/checkout', '', true);
				}

				// Validate cart has products and has stock.
				if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
					$redirect = $this->url->link('checkout/cart');
				}
                  
                $total1 = 0;
                $total2 = 0;
                $total3 = 0;
                $total4 = 0;  

				// Validate minimum quantity requirements.
				$products = $this->cart->getProducts();

				foreach ($products as $product) {
					$product_total = 0;

					foreach ($products as $product_2) {
						if ($product_2['product_id'] == $product['product_id']) {
							$product_total += $product_2['quantity'];
						}
					}

					if ($product['minimum'] > $product_total) {
						$redirect = $this->url->link('checkout/cart');

						break;
					}
				}

				if (!$redirect) {
					$order_data = array();

					$totals = array();
					$taxes = $this->cart->getTaxes();
					$total = 0;
					
					// Because __call can not keep var references so we put them into an array.
					$total_data = array(
						'totals' => &$totals,
						'taxes'  => &$taxes,
						'total'  => &$total
					);

					$this->load->model('setting/extension');

					$sort_order = array();

					$results = $this->model_setting_extension->getExtensions('total');

					foreach ($results as $key => $value) {
						$sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
					}

					array_multisort($sort_order, SORT_ASC, $results);

					foreach ($results as $result) {
						if ($this->config->get('total_' . $result['code'] . '_status')) {
							$this->load->model('extension/total/' . $result['code']);

							// We have to put the totals in an array so that they pass by reference.
							$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
						}
					}

					$sort_order = array();

					foreach ($totals as $key => $value) {
						$sort_order[$key] = $value['sort_order'];
					}

					array_multisort($sort_order, SORT_ASC, $totals);

					$order_data['totals'] = $totals;

					$this->load->language('checkout/checkout');

					$order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
					$order_data['store_id'] = $this->config->get('config_store_id');
					$order_data['store_name'] = $this->config->get('config_name');

					if ($order_data['store_id']) {
						$order_data['store_url'] = $this->config->get('config_url');
					} else {
						if ($this->request->server['HTTPS']) {
							$order_data['store_url'] = HTTPS_SERVER;
						} else {
							$order_data['store_url'] = HTTP_SERVER;
						}
					}

					if ($this->customer->isLogged()) {
						$this->load->model('account/customer');

						$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());

						$order_data['customer_id'] = $this->customer->getId();
						$order_data['customer_group_id'] = $customer_info['customer_group_id'];
						$order_data['firstname'] = $customer_info['firstname'];
						$order_data['lastname'] = $customer_info['lastname'];
						$order_data['email'] = $customer_info['email'];
						$order_data['telephone'] = $customer_info['telephone'];
						//$order_data['fax'] = $customer_info['fax'];
						$order_data['custom_field'] = json_decode($customer_info['custom_field'], true);						
					} elseif (isset($this->session->data['guest'])) {
						$order_data['customer_id'] = 0;
						$order_data['customer_group_id'] = isset($this->session->data['guest']['customer_group_id']) ? $this->session->data['guest']['customer_group_id'] : 1;
						$order_data['firstname'] = isset($this->session->data['guest']['firstname']) ? $this->session->data['guest']['firstname'] : '';
						$order_data['lastname'] = isset($this->session->data['guest']['lastname']) ? $this->session->data['guest']['lastname'] : '';
						$order_data['email'] = isset($this->session->data['guest']['email']) ? $this->session->data['guest']['email'] : '';
						$order_data['telephone'] = isset($this->session->data['guest']['telephone']) ? $this->session->data['guest']['telephone'] : '';
						//$order_data['fax'] = $this->session->data['guest']['fax'];
						$order_data['custom_field'] = isset($this->session->data['guest']['custom_field']) ? $this->session->data['guest']['custom_field'] : '';
					}

					$order_data['payment_firstname'] = isset($this->session->data['payment_address']['firstname']) ? $this->session->data['payment_address']['firstname'] : $order_data['firstname'];
					$order_data['payment_lastname'] = isset($this->session->data['payment_address']['firstname']) ? $this->session->data['payment_address']['lastname'] : $order_data['lastname'];
					$order_data['payment_company'] = isset($this->session->data['payment_address']['firstname']) ? $this->session->data['payment_address']['company'] : '';
					$order_data['payment_address_1'] = isset($this->session->data['payment_address']['firstname']) ? $this->session->data['payment_address']['address_1'] : '';
					$order_data['payment_address_2'] = isset($this->session->data['payment_address']['firstname']) ? $this->session->data['payment_address']['address_2'] : '';
					$order_data['payment_city'] = isset($this->session->data['payment_address']['firstname']) ? $this->session->data['payment_address']['city'] : '';
					$order_data['payment_postcode'] = isset($this->session->data['payment_address']['firstname']) ? $this->session->data['payment_address']['postcode'] : '';
					$order_data['payment_zone'] = isset($this->session->data['payment_address']['firstname']) ? $this->session->data['payment_address']['zone'] : '';
					$order_data['payment_zone_id'] = isset($this->session->data['payment_address']['firstname']) ? $this->session->data['payment_address']['zone_id'] : '';
					$order_data['payment_country'] = isset($this->session->data['payment_address']['firstname']) ? $this->session->data['payment_address']['country'] : '';
					$order_data['payment_country_id'] = isset($this->session->data['payment_address']['firstname']) ? $this->session->data['payment_address']['country_id'] : '';
					$order_data['payment_address_format'] = isset($this->session->data['payment_address']['firstname']) ? $this->session->data['payment_address']['address_format'] : '';
					$order_data['payment_custom_field'] = (isset($this->session->data['payment_address']['custom_field']) ? $this->session->data['payment_address']['custom_field'] : array());

					if (isset($this->session->data['payment_method']['title'])) {
						$order_data['payment_method'] = $this->session->data['payment_method']['title'];
					} else {
						$order_data['payment_method'] = '';
					}

					if (isset($this->session->data['payment_method']['code'])) {
						$order_data['payment_code'] = $this->session->data['payment_method']['code'];
					} else {
						$order_data['payment_code'] = '';
					}

					if ($this->cart->hasShipping()) {
						$order_data['shipping_firstname'] = isset($this->session->data['shipping_address']['firstname']) ? $this->session->data['shipping_address']['firstname'] : $order_data['firstname'];
						$order_data['shipping_lastname'] = isset($this->session->data['shipping_address']['firstname']) ? $this->session->data['shipping_address']['lastname'] : $order_data['lastname'];
						$order_data['shipping_company'] = isset($this->session->data['shipping_address']['firstname']) ? $this->session->data['shipping_address']['company'] : '';
						$order_data['shipping_address_1'] = isset($this->session->data['shipping_address']['firstname']) ? $this->session->data['shipping_address']['address_1'] : '';
						$order_data['shipping_address_2'] = isset($this->session->data['shipping_address']['firstname']) ? $this->session->data['shipping_address']['address_2'] : '';
						$order_data['shipping_city'] = isset($this->session->data['shipping_address']['firstname']) ? $this->session->data['shipping_address']['city'] : '';
						$order_data['shipping_postcode'] = isset($this->session->data['shipping_address']['firstname']) ? $this->session->data['shipping_address']['postcode'] : '';
						$order_data['shipping_zone'] = isset($this->session->data['shipping_address']['firstname']) ? $this->session->data['shipping_address']['zone'] : '';
						$order_data['shipping_zone_id'] = isset($this->session->data['shipping_address']['firstname']) ? $this->session->data['shipping_address']['zone_id'] : '';
						$order_data['shipping_country'] = isset($this->session->data['shipping_address']['firstname']) ? $this->session->data['shipping_address']['country'] : '';
						$order_data['shipping_country_id'] = isset($this->session->data['shipping_address']['firstname']) ? $this->session->data['shipping_address']['country_id'] : '';
						$order_data['shipping_address_format'] = isset($this->session->data['shipping_address']['firstname']) ? $this->session->data['shipping_address']['address_format'] : '';
						$order_data['shipping_custom_field'] = (isset($this->session->data['shipping_address']['custom_field']) ? $this->session->data['shipping_address']['custom_field'] : array());

						if (isset($this->session->data['shipping_method']['title'])) {
							$order_data['shipping_method'] = $this->session->data['shipping_method']['title'];
						} else {
							$order_data['shipping_method'] = '';
						}

						if (isset($this->session->data['shipping_method']['code'])) {
							$order_data['shipping_code'] = $this->session->data['shipping_method']['code'];
						} else {
							$order_data['shipping_code'] = '';
						}
					} else {
						$order_data['shipping_firstname'] = '';
						$order_data['shipping_lastname'] = '';
						$order_data['shipping_company'] = '';
						$order_data['shipping_address_1'] = '';
						$order_data['shipping_address_2'] = '';
						$order_data['shipping_city'] = '';
						$order_data['shipping_postcode'] = '';
						$order_data['shipping_zone'] = '';
						$order_data['shipping_zone_id'] = '';
						$order_data['shipping_country'] = '';
						$order_data['shipping_country_id'] = '';
						$order_data['shipping_address_format'] = '';
						$order_data['shipping_custom_field'] = array();
						$order_data['shipping_method'] = '';
						$order_data['shipping_code'] = '';
					}
                  
                  $order_data['currency_value'] = $this->currency->getValue($this->session->data['currency']);

					$total1 = 0;
                    $total2 = 0;
                    $total3 = 0;
                    $total4 = 0;
                  
                    $order_data['products'] = array();

					foreach ($this->cart->getProducts() as $product) {
                        $total1 = $total1 + ($product['price'] * $product['quantity']);
                        $total2 = $total2 + ($product['tax'] * $product['quantity']);
                        $total3 = $total3 + $product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0);
                        $total4 = $total4 + $product['quantity'];
                      
                        if((float)$product['tax'] > 0){
                            $tax_proc = ($product['tax']/$product['price'])*100;
                        } else {
                            $tax_proc = 'Без НДС';
                        }
                      
						$option_data = array();

						foreach ($product['option'] as $option) {
							$option_data[] = array(
								'product_option_id'       => $option['product_option_id'],
								'product_option_value_id' => $option['product_option_value_id'],
								'option_id'               => $option['option_id'],
								'option_value_id'         => $option['option_value_id'],
								'name'                    => $option['name'],
								'value'                   => $option['value'],
								'type'                    => $option['type']
							);
						}

						$order_data['products'][] = array(
							'product_id' => $product['product_id'],
							'name'       => $product['name'],
							'model'      => $product['model'],
							'option'     => $option_data,
							'download'   => $product['download'],
							'quantity'   => $product['quantity'],
							'subtract'   => $product['subtract'],
                            'tax_proc'    => $tax_proc,
                            'price_new'    => $this->currency->format($product['price'], $this->session->data['currency'], $order_data['currency_value']),
                            'tax'    => $this->currency->format(($this->config->get('config_tax') ? $product['tax'] * $product['quantity'] : 0), $this->session->data['currency'], $order_data['currency_value']),
							'price'      => $product['price'],
							'total'      => $product['total'],
							//'tax'        => $this->tax->getTax($product['price'], $product['tax_class_id']),
							'reward'     => $product['reward']
						);
					}
                  
                    $data['total1'] = $this->currency->format($total1, $this->session->data['currency'], $order_data['currency_value']);
                    $data['total2'] = $this->currency->format($total2, $this->session->data['currency'], $order_data['currency_value']);
                    $data['total3'] = $this->currency->format($total3, $this->session->data['currency'], $order_data['currency_value']);
                    $data['total4'] = $total4;

					// Gift Voucher
					$order_data['vouchers'] = array();

					if (!empty($this->session->data['vouchers'])) {
						foreach ($this->session->data['vouchers'] as $voucher) {
							$order_data['vouchers'][] = array(
								'description'      => $voucher['description'],
								'code'             => token(10),
								'to_name'          => $voucher['to_name'],
								'to_email'         => $voucher['to_email'],
								'from_name'        => $voucher['from_name'],
								'from_email'       => $voucher['from_email'],
								'voucher_theme_id' => $voucher['voucher_theme_id'],
								'message'          => $voucher['message'],
								'amount'           => $voucher['amount']
							);
						}
					}

					$order_data['comment'] = isset($this->session->data['comment']) ? $this->session->data['comment'] : '';
					$order_data['total'] = $total;

					if (isset($this->request->cookie['tracking'])) {
						$order_data['tracking'] = $this->request->cookie['tracking'];

						$subtotal = $this->cart->getSubTotal();

						// Affiliate
						$this->load->model('account/customer');

						$affiliate_info = $this->model_account_customer->getAffiliateByTracking($this->request->cookie['tracking']);

						if ($affiliate_info) {
						$order_data['affiliate_id'] = $affiliate_info['customer_id'];
						$order_data['commission'] = ($subtotal / 100) * $affiliate_info['commission'];
						} else {
						$order_data['affiliate_id'] = 0;
						$order_data['commission'] = 0;
						}

						// Marketing
						$this->load->model('checkout/marketing');

						$marketing_info = $this->model_checkout_marketing->getMarketingByCode($this->request->cookie['tracking']);

						if ($marketing_info) {
							$order_data['marketing_id'] = $marketing_info['marketing_id'];
						} else {
							$order_data['marketing_id'] = 0;
						}
					} else {
						$order_data['affiliate_id'] = 0;
						$order_data['commission'] = 0;
						$order_data['marketing_id'] = 0;
						$order_data['tracking'] = '';
					}

					$order_data['language_id'] = $this->config->get('config_language_id');
					$order_data['currency_id'] = $this->currency->getId($this->session->data['currency']);
					$order_data['currency_code'] = $this->session->data['currency'];
					$order_data['currency_value'] = $this->currency->getValue($this->session->data['currency']);
					$order_data['ip'] = $this->request->server['REMOTE_ADDR'];

					if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
						$order_data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
					} elseif (!empty($this->request->server['HTTP_CLIENT_IP'])) {
						$order_data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
					} else {
						$order_data['forwarded_ip'] = '';
					}

					if (isset($this->request->server['HTTP_USER_AGENT'])) {
						$order_data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
					} else {
						$order_data['user_agent'] = '';
					}

					if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
						$order_data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
					} else {
						$order_data['accept_language'] = '';
					}

					$this->load->model('checkout/order');
					if (!isset($this->session->data['order_id'])){
						$this->session->data['order_id'] = $this->model_checkout_order->addOrder($order_data);
					}

					$this->load->model('tool/upload');

					$total1 = 0;
                    $total2 = 0;
                    $total3 = 0;
                    $total4 = 0;
                  
                    $data['products'] = array();

					foreach ($this->cart->getProducts() as $product) {
						$option_data = array();

						foreach ($product['option'] as $option) {
							if ($option['type'] != 'file') {
								$value = $option['value'];
							} else {
								$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

								if ($upload_info) {
									$value = $upload_info['name'];
								} else {
									$value = '';
								}
							}

							$option_data[] = array(
								'name'  => $option['name'],
								'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
							);
						}
                      
                        $total1 = $total1 + ($product['price'] * $product['quantity']);
                        $total2 = $total2 + ($product['tax'] * $product['quantity']);
                        $total3 = $total3 + $product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0);
                        $total4 = $total4 + $product['quantity'];
                      
                        if((float)$product['tax'] > 0){
                            $tax_proc = ($product['tax']/$product['price'])*100;
                        } else {
                            $tax_proc = 'Без НДС';
                        }
						
						$data['products'][] = array(
							'product_id' => $product['product_id'],
							'name'       => $product['name'],
							'model'      => $product['model'],
							'option'     => $option_data,
							'quantity'   => $product['quantity'],
							'subtract'   => $product['subtract'],
                           // 'price'      => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']),
                            'price'      => $this->currency->format($product['price'] * $product['quantity'], $this->session->data['currency']),
							'total'      => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'], $this->session->data['currency']),
                            'price_new'  => $this->currency->format($product['price'], $this->session->data['currency']),
                            'tax_proc'    => $tax_proc,
                            'tax'    => $this->currency->format(($this->config->get('config_tax') ? $product['tax'] * $product['quantity'] : 0), $this->session->data['currency']),
							
							'href'       => $this->url->link('product/product', 'product_id=' . $product['product_id']),
						);
					}
                          
                    $data['total1'] = $this->currency->format($total1, $this->session->data['currency']);
                    $data['total2'] = $this->currency->format($total2, $this->session->data['currency']);
                    $data['total3'] = $this->currency->format($total3, $this->session->data['currency']);
                    $data['total4'] = $total4;

					// Gift Voucher
					$data['vouchers'] = array();

					if (!empty($this->session->data['vouchers'])) {
						foreach ($this->session->data['vouchers'] as $voucher) {
							$data['vouchers'][] = array(
								'description' => $voucher['description'],
								'amount'      => $this->currency->format($voucher['amount'], $this->session->data['currency'])
							);
						}
					}

					$data['totals'] = array();

					foreach ($order_data['totals'] as $total) {
						$data['totals'][] = array(
							'title' => $total['title'],
							'text'  => $this->currency->format($total['value'], $this->session->data['currency'])
						);
					}

					$data['payment'] = $this->load->controller('extension/payment/' . $this->session->data['payment_method']['code']);
				} else {
					$data['redirect'] = $redirect;
				}	

				$this->load->model('checkout/order');
				if (isset($this->session->data['order_id'])) {
					$error_user = 0;
					$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
					
					if ($this->customer->isLogged()) {
						$this->load->model('account/customer');
						$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
						
						if (isset($customer_info['telephone'])) {
							$customer_telephone = ', ' . $this->language->get('text_telephones') . ' ' . $customer_info['telephone'];
						} else {
							$customer_telephone = '';
						}
					} else {				
						if (isset($this->session->data['guest']['telephone'])) {
							$customer_telephone = ', ' . $this->language->get('text_telephones') . ' ' . $this->session->data['guest']['telephone'];
						} else {
							$customer_telephone = '';
						}
					}
					
					if (isset($order_info['payment_company'])) {
						$payment_company = ', ' . $order_info['payment_company'];
					} else {
						$payment_company = '';
					}
					
					$data['text_payment_schet_title'] = sprintf($this->language->get('text_payment_schet_title'), $order_info['order_id'], date($this->language->get('date_format_short'), strtotime($order_info['date_added'])));
					$data['buyer'] = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'] . $payment_company . ', ' . $order_info['payment_postcode'] . ', ' . $order_info['payment_zone'] . ', ' . $order_info['payment_city'] . ', ' . $order_info['payment_address_1'] . $customer_telephone;

					$this->load->model('tool/summ');
					
					$rur_code = 'RUB';
					$rur_order_total = $this->currency->convert($order_info['total'], $order_info['currency_code'], $rur_code);
					$price_convert = $this->model_tool_summ->num2str($this->currency->format($rur_order_total, $rur_code, $order_info['currency_value'], FALSE));
					
					$data['items'] = sprintf($this->language->get('text_items'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($rur_order_total, $rur_code, $order_info['currency_value']));
						
					$first_part = mb_substr($price_convert,0,1, 'UTF-8');
					$last_part = mb_substr($price_convert,1);
					$first_part = mb_strtoupper($first_part, 'UTF-8');
					$data['amount'] = $first_part.$last_part;
				}
			}
		}

		if (!$error_user) {
			$this->response->setOutput($this->load->view('extension/payment/schet_print', $data));
		} else {
			$this->response->setOutput($this->load->view('extension/payment/schet_print_error', $data));
		}
	}

	public function confirm() {
		if ($this->session->data['payment_method']['code'] == 'schet') {
			$this->load->language('extension/payment/schet');

			$this->load->model('checkout/order');

			$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
			
			if ($this->config->get('payment_schet_instruction' . $this->config->get('config_language_id'))) {
				$payment_schet_instruction = $this->config->get('payment_schet_instruction' . $this->config->get('config_language_id'));
			} else {
				$payment_schet_instruction = $this->language->get('text_account');
			}

			$comment = $payment_schet_instruction . "\n\n";
			$comment .= sprintf($this->language->get('button_print_account'), $this->url->link('extension/payment/schet/print_schet', 'order_id=' . $order_info['order_id'], true)) . "\n\n";

			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payment_schet_order_status_id'), $comment, true);

			return true;
		}
	}

}