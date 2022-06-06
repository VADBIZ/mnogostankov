<?php
class ModelExtensionTotalShipping extends Model {
	public function getTotal($total) {
		if (!isset($this->session->data['shipping_code']))
			$code = 'flat';
		else
			$code = $this->session->data['shipping_code'];
		if ($this->cart->hasShipping() && isset($this->session->data['shipping_method'])) {
			$total['totals'][] = array(
				'code'       => 'shipping',
				'title'      => $this->session->data['shipping_method'],
				'value'      => $this->config->get('shipping_'.$code.'_cost'),
				'sort_order' => $this->config->get('total_shipping_sort_order')
			);
			if ($this->session->data['shipping_method']) {
				$tax_rates = $this->tax->getRates($this->config->get('shipping_'.$code.'_cost'), $this->config->get('shipping_'.$code.'_tax_class_id'));

				foreach ($tax_rates as $tax_rate) {
					if (!isset($total['taxes'][$tax_rate['tax_rate_id']])) {
						$total['taxes'][$tax_rate['tax_rate_id']] = $tax_rate['amount'];
					} else {
						$total['taxes'][$tax_rate['tax_rate_id']] += $tax_rate['amount'];
					}
				}
			}

			$total['total'] += $this->config->get('shipping_'.$code.'_cost');
		}
	}
}