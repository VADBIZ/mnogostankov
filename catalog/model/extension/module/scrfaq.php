<?php
class ModelExtensionModuleScrfaq extends Model {

    public function getAnswer($module_id)
    {
        $query = $this->db->query("SELECT * FROM ". DB_PREFIX . "scrfaq f
            LEFT JOIN " . DB_PREFIX . "scrfaq_description fd
	            ON f.id = fd.question_id
            WHERE f.status = 1 AND
                f.module_id = {$module_id} AND
	            fd.language_id = " . (int)$this->config->get('config_language_id') . "
            ORDER BY f.sort_order ASC");

        return $query->rows;
    }
}