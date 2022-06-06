<?php
class ModelCatalogDocuments extends Model {
    public function getOptionValue() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "documents");

		return $query->rows;
	}
}