<?php
class ModelCatalogDocuments extends Model {
	public function addOption($data) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "documents");
      
        if (isset($data['option_value'])) {
			foreach ($data['option_value'] as $option_value) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "documents SET name = '" . $this->db->escape($option_value['name']) . "', link = '" . $this->db->escape($option_value['link']) . "'");
            }
        }
    }
  
    public function getOptionValue() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "documents");

		return $query->rows;
	}
}