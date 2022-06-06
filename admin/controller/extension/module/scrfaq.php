<?php

class ControllerExtensionModuleScrfaq extends Controller
{

    private $error = array();

    public function install()
    {
        $this->load->model('setting/setting');
        $this->load->model('user/user_group');
        $this->load->model('extension/scrfaq');

        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/scrfaq');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/scrfaq');

        $this->model_extension_scrfaq->createTable();
    }

    public function index()
    {
        $this->load->language('extension/module/scrfaq');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/scrfaq');
        $this->load->model('setting/module');

        $this->getList();
    }

    protected function getList()
    {


        $module_id = !empty($this->request->get['module_id']) ? $this->request->get['module_id'] : 0;

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            if (!$module_id) {
                $module_id = $this->model_extension_scrfaq->addModule('scrfaq', $this->request->post);
                $mData = $this->request->post;
                $mData['mid'] = $module_id;
                $this->model_setting_module->editModule($module_id, $mData);
                $this->session->data['success'] = $this->language->get('text_success');
                $this->response->redirect($this->url->link('extension/module/scrfaq', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $module_id, true));
            }

            $mData = $this->request->post;
            $mData['mid'] = $module_id;
            $this->model_setting_module->editModule($module_id, $mData);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        }

        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = '';
        }

        if (isset($this->error['descr'])) {
            $data['error_descr'] = $this->error['descr'];
        } else {
            $data['error_descr'] = '';
        }

        if (isset($this->request->get['filter_status'])) {
            $filter_status = $this->request->get['filter_status'];
        } else {
            $filter_status = null;
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'f.create';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        if ($module_id && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $module_info = $this->model_setting_module->getModule($module_id);
        }

        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name'];
        } elseif (!empty($module_info)) {
            $data['name'] = $module_info['name'];
        } else {
            $data['name'] = '';
        }

        if (isset($this->request->post['descr'])) {
            $data['descr'] = $this->request->post['descr'];
        } elseif (!empty($module_info)) {
            $data['descr'] = $module_info['descr'];
        } else {
            $data['descr'] = '';
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($module_info)) {
            $data['status'] = $module_info['status'];
        } else {
            $data['status'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/scrfaq', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        if (!$module_id) {
            $data['action'] = $this->url->link('extension/module/scrfaq', 'user_token=' . $this->session->data['user_token'], true);
        } else {
            $data['action'] = $this->url->link('extension/module/scrfaq', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $module_id, true);
        }

        $data['add'] = $this->url->link('extension/module/scrfaq/add', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $module_id . $url, true);
        $data['delete'] = $this->url->link('extension/module/scrfaq/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $data['questions'] = array();
        $questions_total = 0;

        if ($module_id > 0) {
            $filter_data = array(
                'module_id' => $module_id,
                'filter_status' => $filter_status,
                'sort' => $sort,
                'order' => $order,
                'start' => ($page - 1) * $this->config->get('config_limit_admin'),
                'limit' => $this->config->get('config_limit_admin')
            );

            $questions_total = $this->model_extension_scrfaq->getTotalQuestions($filter_data);

            $results = $this->model_extension_scrfaq->getQuestions($filter_data);

            foreach ($results as $result) {
                $data['questions'][] = array(
                    'id' => $result['id'],
                    'title' => $result['title'],
                    'status' => ($result['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
                    'edit' => $this->url->link('extension/module/scrfaq/edit', 'user_token=' . $this->session->data['user_token'] . '&question_id=' . $result['id'] . '&module_id=' . $module_id . $url, true)
                );
            }
        }
        $data['user_token'] = $this->session->data['user_token'];

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array)$this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $url = '';

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_status'] = $this->url->link('extension/module/scrfaq', 'user_token=' . $this->session->data['user_token'] . '&sort=f.status' . $url, true);
        $data['sort_order'] = $this->url->link('extension/module/scrfaq', 'user_token=' . $this->session->data['user_token'] . '&sort=f.sort_order' . $url, true);

        $url = '';

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $questions_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('extension/module/scrfaq', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($questions_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($questions_total - $this->config->get('config_limit_admin'))) ? $questions_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $questions_total, ceil($questions_total / $this->config->get('config_limit_admin')));

        $data['filter_status'] = $filter_status;

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $data['isEdit'] = $module_id > 0;

        $this->response->setOutput($this->load->view('extension/module/scrfaq_list', $data));
    }

    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/module/scrfaq')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
            $this->error['name'] = $this->language->get('error_name');
        }

        return !$this->error;
    }

    public function add()
    {
        $module_id = !empty($this->request->get['module_id']) ? $this->request->get['module_id'] : 0;

        $this->load->language('extension/module/scrfaq');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/scrfaq');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_extension_scrfaq->addFaq($this->request->post, $module_id);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('extension/module/scrfaq', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $module_id . $url, true));
        }

        $this->getForm();
    }

    protected function validateForm()
    {
        if (!$this->user->hasPermission('modify', 'extension/module/scrfaq')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        foreach ($this->request->post['scrfaq_question'] as $language_id => $value) {
            if ((utf8_strlen($value['title']) < 3) || (utf8_strlen($value['title']) > 255)) {
                $this->error['title'][$language_id] = $this->language->get('error_title');
            }
        }

        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        return !$this->error;
    }

    protected function getForm()
    {
        $module_id = !empty($this->request->get['module_id']) ? $this->request->get['module_id'] : 0;

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_form'] = !isset($this->request->get['question_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['name'])) {
            $data['error_title'] = $this->error['title'];
        } else {
            $data['error_title'] = array();
        }

        $url = '';

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $url .= '&module_id=' . $module_id;

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/scrfaq', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        if (!isset($this->request->get['question_id'])) {
            $data['action'] = $this->url->link('extension/module/scrfaq/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } else {
            $data['action'] = $this->url->link('extension/module/scrfaq/edit', 'user_token=' . $this->session->data['user_token'] . '&question_id=' . $this->request->get['question_id'] . $url, true);
        }

        $data['cancel'] = $this->url->link('extension/module/scrfaq', 'user_token=' . $this->session->data['user_token'] . $url, true);

        if (isset($this->request->get['question_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $question_info = $this->model_extension_scrfaq->getQuestion($this->request->get['question_id']);
        }

        $data['user_token'] = $this->session->data['user_token'];

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        if (isset($this->request->post['scrfaq_question'])) {
            $data['scrfaq_question'] = $this->request->post['scrfaq_question'];
        } elseif (isset($this->request->get['question_id'])) {
            $data['scrfaq_question'] = $this->model_extension_scrfaq->getFaqDescriptions($this->request->get['question_id']);
        } else {
            $data['scrfaq_question'] = array();
        }

        if (isset($this->request->post['sort_order'])) {
            $data['sort_order'] = $this->request->post['sort_order'];
        } elseif (!empty($question_info)) {
            $data['sort_order'] = $question_info['sort_order'];
        } else {
            $data['sort_order'] = 1;
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($question_info)) {
            $data['status'] = $question_info['status'];
        } else {
            $data['status'] = true;
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/scrfaq_form', $data));
    }

    public function edit()
    {
        $this->load->language('extension/module/scrfaq');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/scrfaq');
        $module_id = !empty($this->request->get['module_id']) ? $this->request->get['module_id'] : 0;

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_extension_scrfaq->editFaq($this->request->get['question_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $url .= '&module_id=' . $module_id;

            $this->response->redirect($this->url->link('extension/module/scrfaq', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    public function delete()
    {
        $this->load->language('extension/module/scrfaq');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/scrfaq');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $question_id) {
                $this->model_extension_scrfaq->deleteFaq($question_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('extension/module/scrfaq', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getList();
    }

    protected function validateDelete()
    {
        if (!$this->user->hasPermission('modify', 'extension/module/scrfaq')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    public function uninstall()
    {
        $this->load->model('setting/setting');
        $this->load->model('user/user_group');
        $this->load->model('extension/scrfaq');

        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'extension/module/scrfaq');
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'extension/module/scrfaq');

        $this->model_extension_scrfaq->removeTable();

    }
}
