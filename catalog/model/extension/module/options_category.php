<?php
class ModelExtensionModuleOptionsCategory extends Model {
	public function getProduct($product_id) {
		$query = $this->db->query("SELECT DISTINCT p.price, p.points, p.tax_class_id, p.minimum, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special FROM " . DB_PREFIX . "product p WHERE p.product_id = '" . (int)$product_id . "'");

		if ($query->num_rows) {
			return array(
				'price'        => ($query->row['discount'] ? $query->row['discount'] : $query->row['price']),
				'special'      => $query->row['special'],
				'points'       => $query->row['points'],
				'tax_class_id' => $query->row['tax_class_id'],
				'minimum'      => $query->row['minimum']
			);
		} else {
			return false;
		}
	}

	public function getOptions($product) {
		$options_category = array();

		foreach ($this->getProductOptions($product['product_id']) as $option) {
			$product_option_value_data = array();
			$check_option_stop = false;

			foreach ($option['product_option_value'] as $option_value) {
				$price_calc = false;
				$price = false;
				$special_no_tax = false;
				$special_calc = false;
				$special = false;
				$tax_calc = false;
				$option_tooltip = false;

				if ($this->config->get('module_options_category_show_no_stock') || !$option_value['subtract'] || ($option_value['quantity'] > 0)) {
					if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {

						if ($this->config->get('module_options_category_show_price')) {
							$option_tooltip = true;
						}

						if ($option_value['price_prefix'] == '+' || $option_value['price_prefix'] == '-' || $option_value['price_prefix'] == '=') {

							if ($option_value['price_prefix'] == '=') {
								$tax = $this->config->get('config_tax');
							} else {
								$tax = $this->config->get('config_tax') ? 'P' : false;
							}

							$price_calc = $this->tax->calculate($option_value['price'], $product['tax_class_id'], $tax);
							$price = $this->currency->format($price_calc, $this->session->data['currency']);

							if ((float)$product['special'] && $this->cart->optionSpecialMode($option, $option_value)) {
								$special_no_tax = $this->cart->getOptionSpecial($option_value, $product);
								$special_calc = $this->tax->calculate($special_no_tax, $product['tax_class_id'], $tax);
								$special = $this->currency->format($special_calc, $this->session->data['currency']);
							}
						} else {
							$price_calc = $option_value['price'];
							$price = (float)$option_value['price'];

							if ((float)$product['special'] && $this->cart->optionSpecialMode($option, $option_value)) {
								$special_no_tax = $this->cart->getOptionSpecial($option_value, $product);
								$special_calc = $special_no_tax;
								$special = (float)$special_calc;
							}
						}

						if ($this->config->get('config_tax')) {
							$tax_calc = (float)$special_no_tax ? $special_no_tax : $option_value['price'];
						}
					}

					// Изображение
					if ($this->config->get('module_options_category_image')) {
						if ($this->config->get('module_options_category_image_select') && $option_value['option_image']) {
							$option_value_image = $option_value['option_image'];
						} else {
							$option_value_image = $option_value['image'];
						}

						if ($option_value_image) {
							$option_value_image = $this->model_tool_image->resize($option_value_image, (int)$this->config->get('module_options_category_image_width'), (int)$this->config->get('module_options_category_image_height'));
						}
					} else {
						$option_value_image = '';
					}

					// Название (Всплывающая подсказка)
					if ($this->config->get('module_options_category_image') && $option_value_image && $this->config->get('module_options_category_name_tooltip')) {
						$name_tooltip = $option_value['name'];
						$option_tooltip = true;
					} else {
						$name_tooltip = '';
					}

					// Увеличенное изображение
					if ($this->config->get('module_options_category_image_popup')) {
						if ($this->config->get('module_options_category_image_select') && $option_value['option_image']) {
							$image_popup = $option_value['option_image'];
						} else {
							$image_popup = $option_value['image'];
						}

						if ($image_popup) {
							$option_tooltip = true;
							$image_popup = $this->model_tool_image->resize($image_popup, (int)$this->config->get('module_options_category_image_popup_width'), (int)$this->config->get('module_options_category_image_popup_height'));
						}
					} else {
						$image_popup = '';
					}

					// Префикс
					$price_prefix_calc = $option_value['price_prefix'];

					// Отключение опции при 0 запасе
					if ($this->config->get('module_options_category_no_stock_disabled') && ($option_value['quantity'] == 0)) {
						$option_no_stock_disabled = ' disabled="disabled"';
					} else {
						$option_no_stock_disabled = '';
					}

					// Автовыбор первой опции
					if ($this->config->get('module_options_category_autoselect') && !$option_no_stock_disabled && !$check_option_stop) {
						$autoselect = true;
						$check_option_stop = true;
					} else {
						$autoselect = false;
					}

					// Количество
					if ($this->config->get('module_options_category_show_quantity')) {
						$quantity = $option_value['quantity'] ? $option_value['quantity'] : $this->language->get('text_option_out_stock');
						$option_tooltip = true;
					} else {
						$quantity = '';
					}

					// Артикул
					if ($this->config->get('module_options_category_sku')) {
						$sku = $option_value['sku'] ? $option_value['sku'] : '';
						$option_tooltip = true;
					} else {
						$sku = '';
					}

					// Вес
					if ($this->config->get('module_options_category_weight') && (float)$option_value['weight']) {
						$weight = $this->weight->format($option_value['weight'], $this->config->get('config_weight_class_id'), $this->language->get('decimal_point'), $this->language->get('thousand_point'));
						$option_tooltip = true;
					} else {
						$weight = '';
					}

					// баллы
					if ($this->config->get('module_options_category_points')) {
						$points = $option_value['points'] ? $option_value['points'] : '';
						$option_tooltip = true;
					} else {
						$points = '';
					}

					$product_option_value_data[] = array(
						'product_option_value_id' => $option_value['product_option_value_id'],
						'option_value_id'         => $option_value['option_value_id'],
						'image'       			  => $option_value_image,
						'image_popup'  			  => $image_popup,
						'name'                    => $option_value['name'],
						'name_tooltip'            => $name_tooltip,
						'price'       			  => $this->getPriceFormat($option_value['price_prefix'], $price),
						'special'                 => $this->getPriceFormat($option_value['price_prefix'], $special),
						'price_calc'   			  => $price_calc,
						'special_calc'            => $special_calc,
						'tax_calc'          	  => $tax_calc,
						'price_prefix_calc'       => $price_prefix_calc,
						'no_stock_disabled'       => $option_no_stock_disabled,
						'autoselect'       		  => $autoselect,
						'points'                  => $points,
						'points_prefix'           => $option_value['points_prefix'],
						'weight'       			  => $weight,
						'weight_prefix'           => $option_value['weight_prefix'],
						'sku'                     => $sku,
						'quantity'                => $quantity,
						'option_tooltip'          => $option_tooltip
					);
				}
			}

			$options_category[] = array(
				'product_option_id'    => $option['product_option_id'],
				'product_option_value' => $product_option_value_data,
				'option_id'            => $option['option_id'],
				'name'                 => $option['name'],
				'type'                 => $option['type'],
				'value'                => $option['value'],
				'required'             => $option['required']
			);
		}

		return $options_category;
	}

	public function getProductOptions($product_id) {
		$product_option_data = array();

		if ($this->config->get('module_options_category_show')) {
			$options_category_show = $this->config->get('module_options_category_show');
		} else {
			$options_category_show = array(0);
		}

		if ($this->config->get('module_options_category_value_show')) {
			$options_category_value_show = $this->config->get('module_options_category_value_show');
		} else {
			$options_category_value_show = array(0);
		}

		if ($this->config->get('module_options_category_show_mode') == 2) {
			$sql = " AND ((po.option_status = '2' AND po.option_id IN (" . implode(',', $options_category_show) . ")) OR po.option_status = '1')";
		} elseif ($this->config->get('module_options_category_show_mode') == 1) {
			$sql = " AND po.option_id IN (" . implode(',', $options_category_show) . ")";
		} else {
			$sql = "";
		}

		$product_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'" . $sql . " ORDER BY o.sort_order");

		foreach ($product_option_query->rows as $product_option) {
			$product_option_value_data = array();

			if ($this->config->get('module_options_category_value_show_mode') == 2) {
				$sql = " AND ((pov.option_status = '2' AND pov.option_value_id IN (" . implode(',', $options_category_value_show) . ")) OR pov.option_status = '1')";
			} elseif ($this->config->get('module_options_category_value_show_mode') == 1) {
				$sql = " AND pov.option_value_id IN (" . implode(',', $options_category_value_show) . ")";
			} else {
				$sql = "";
			}

			$product_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_id = '" . (int)$product_id . "' AND pov.product_option_id = '" . (int)$product_option['product_option_id'] . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'" . $sql . " ORDER BY ov.sort_order");

			foreach ($product_option_value_query->rows as $product_option_value) {
				$product_option_value_data[] = array(
					'product_option_value_id' => $product_option_value['product_option_value_id'],
					'option_value_id'         => $product_option_value['option_value_id'],
					'name'                    => $product_option_value['name'],
					'image'                   => $product_option_value['image'],
					'option_image'       	  => $product_option_value['option_image'],
					'quantity'                => $product_option_value['quantity'],
					'subtract'                => $product_option_value['subtract'],
					'price'                   => $product_option_value['price'],
					'price_prefix'            => $product_option_value['price_prefix'],
					'weight'                  => $product_option_value['weight'],
					'weight_prefix'           => $product_option_value['weight_prefix'],
					'sku'                	  => $product_option_value['sku'],
					'points'                  => $product_option_value['points'],
					'points_prefix'           => $product_option_value['points_prefix'],
					'option_special'       	  => $product_option_value['option_special']
				);
			}

			$product_option_data[] = array(
				'product_option_id'    => $product_option['product_option_id'],
				'product_option_value' => $product_option_value_data,
				'option_id'            => $product_option['option_id'],
				'name'                 => $product_option['name'],
				'type'                 => $product_option['type'],
				'value'                => $product_option['value'],
				'required'             => $product_option['required'],
				'option_special'       => $product_option['option_special']
			);
		}

		return $product_option_data;
	}

	public function getPriceFormat($price_prefix, $price) {
		if ((float)$price && $this->config->get('module_options_category_show_price')) {
			if ($price_prefix == '*') {
				$price_format = '&#215;' . $price;
			} elseif ($price_prefix == '/') {
				$price_format = '&#247;' . $price;
			} elseif ($price_prefix == '=') {
				$price_format = $price;
			} elseif ($price_prefix == '%') {
				$price_format = $price . '%';
			} elseif ($price_prefix == 'p') {
				$price_format = '-' . $price . '%';
			} else {
				$price_format = $price_prefix . $price;
			}

			return $price_format;
		} else {
			return false;
		}
	}

	public function getOptionSetting() {
		$data['option_status'] = $this->config->get('module_options_category_option_status');
		$data['theme'] = $this->config->get('module_options_category_theme');
		$data['autoselect'] = $this->config->get('module_options_category_autoselect');
		$data['position'] = $this->config->get('module_options_category_position');
		$data['select_quantity'] = $this->config->get('module_options_category_quantity');
		$data['quantity_product'] = $this->config->get('module_options_category_quantity_product');

		// Price Format
        $data['value'] = $this->currency->getValue($this->session->data['currency']);
        $data['symbol_left'] = $this->currency->getSymbolLeft($this->session->data['currency']);
        $data['symbol_right'] = $this->currency->getSymbolRight($this->session->data['currency']);

		$data['decimals'] = !empty($this->currency->getDecimalPlace($this->session->data['currency'])) ? $this->currency->getDecimalPlace($this->session->data['currency']) : 0;

		$data['decimal_point'] = $this->language->get('decimal_point');
        $data['thousand_point'] = $this->language->get('thousand_point');

		if (isset($this->session->data['options_category_id']) && (int)$this->session->data['options_category_id']) {
			$data['id'] = $this->session->data['options_category_id'] + 1;
		} else {
			$data['id'] = 1;
		}

		$this->session->data['options_category_id'] = $data['id'];

		return $data;
	}

	public function getOptionScript() {
		$this->document->addScript('catalog/view/javascript/option/option.js');
		$this->document->addStyle('catalog/view/javascript/option/option.css');

		if ($this->config->get('module_options_category_datetimepicker')) {
			$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');
			$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment.js');
			$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
			$this->document->addScript('catalog/view/javascript/option/datetimepicker.js');
		}
	}
}
