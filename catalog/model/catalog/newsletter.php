<?php
class ModelCatalogNewsletter extends Model {
    public function isSubscribed($email) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "newsletter` WHERE email = '" . $this->db->escape($email)."'");

		return $query->row['total'];
	}
  
    public function addSubscribed($email) {
        $ip = $this->request->server['REMOTE_ADDR'];
        $this->db->query("INSERT INTO " . DB_PREFIX . "newsletter SET email = '" . $this->db->escape($email) . "', ip = '" . $this->db->escape($ip) . "', store_id = '" . (int)$this->config->get('config_store_id') . "'");
    }
  
    public function getTotalSubscribed() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "newsletter ");

		return $query->row['total'];
	}
}