<?php

class ModelExtensionScrfaq extends Model
{
    public function addModule($code, $data)
    {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "module` SET 
        `name` = '" . $this->db->escape($data['name']) . "', 
        `code` = '" . $this->db->escape($code) . "', 
        `setting` = '" . $this->db->escape(json_encode($data)) . "'");
        return $this->db->getLastId();
    }

    public function getTotalQuestions($data = array())
    {
        $sql = "SELECT COUNT(DISTINCT f.id) AS total FROM " . DB_PREFIX . "scrfaq f LEFT JOIN " . DB_PREFIX . "scrfaq_description fd ON (f.id = fd.question_id)";

        $sql .= " WHERE fd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $sql .= " AND f.status = '" . (int)$data['filter_status'] . "'";
        }

        if (isset($data['module_id']) && $data['module_id'] > 0) {
            $sql .= " AND f.module_id = '" . (int)$data['module_id'] . "'";
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function getQuestions($data = array())
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "scrfaq f LEFT JOIN " . DB_PREFIX . "scrfaq_description fd ON (f.id = fd.question_id) WHERE fd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $sql .= " AND f.status = '" . (int)$data['filter_status'] . "'";
        }

        if (isset($data['module_id']) && $data['module_id'] > 0) {
            $sql .= " AND f.module_id = '" . (int)$data['module_id'] . "'";
        }

        $sql .= " GROUP BY f.id";

        $sql .= " ORDER BY f.sort_order ASC, f.create DESC";

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getQuestion($question_id)
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "scrfaq f
                    LEFT JOIN " . DB_PREFIX . "scrfaq_description fd
                        ON f.id = fd.question_id
                    WHERE f.id = '" . (int)$question_id . "' AND
                    fd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        $query = $this->db->query($sql);

        return $query->row;
    }

    public function getFaqDescriptions($question_id)
    {
        $faq_description_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "scrfaq_description WHERE question_id = '" . (int)$question_id . "'");

        foreach ($query->rows as $result) {
            $faq_description_data[$result['language_id']] = array(
                'title' => $result['title'],
                'description' => $result['description']
            );
        }

        return $faq_description_data;
    }

    public function addFaq($data, $module_id)
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "scrfaq SET `sort_order` = '" . (int)$data['sort_order'] . "', `module_id` = " . (int)$module_id . ", `status` = '" . (int)$data['status'] . "', `create` = NOW()");

        $question_id = $this->db->getLastId();

        foreach ($data['scrfaq_question'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "scrfaq_description SET question_id = '" . (int)$question_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) . "'");
        }

        $this->cache->delete('question');

        return $question_id;
    }

    public function editFaq($question_id, $data)
    {

        $this->db->query("UPDATE " . DB_PREFIX . "scrfaq SET sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "' WHERE id = '" . (int)$question_id . "'");

        $this->db->query("DELETE FROM " . DB_PREFIX . "scrfaq_description WHERE question_id = '" . (int)$question_id . "'");

        foreach ($data['scrfaq_question'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "scrfaq_description SET question_id = '" . (int)$question_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) . "'");
        }

        $this->cache->delete('question');
    }

    public function deleteFaq($question_id)
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "scrfaq WHERE id = '" . (int)$question_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "scrfaq_description WHERE question_id = '" . (int)$question_id . "'");
        $this->cache->delete('question');
    }

    public function createTable()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "scrfaq` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `module_id` int(11) NOT NULL DEFAULT '0',
  `create` datetime NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "scrfaq_description` (
  `question_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  UNIQUE KEY `answer` (`question_id`,`language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");
    }

    public function removeTable()
    {
        $this->db->query("DROP TABLE `" . DB_PREFIX . "scrfaq`");
        $this->db->query("DROP TABLE `" . DB_PREFIX . "scrfaq_description`");
    }
}
