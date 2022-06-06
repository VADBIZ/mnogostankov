<?php
class ModelCatalogVacancy extends Model {
    public function getOptionValue() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "vacancy");

		return $query->rows;
	}
} 