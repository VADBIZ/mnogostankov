<?php
class ModelExtensionDashboardMap extends Model {
	public function getTotalOrdersByCountry() {
		$implode = array();
		
		if (is_array($this->config->get('config_complete_status'))) {
			foreach ($this->config->get('config_complete_status') as $order_status_id) {
				$implode[] = (int)$order_status_id;
			}
		}
		
		if ($implode) {
			$query = $this->db->query("SELECT COUNT(*) AS total, SUM(o.total) AS amount FROM `" . DB_PREFIX . "order` o WHERE o.order_status_id IN(" . implode(',', $implode) . ")");

			return $query->rows;
		} else {
			return array();
		}
	}
}
