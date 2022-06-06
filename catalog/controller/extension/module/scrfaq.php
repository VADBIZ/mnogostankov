<?php

class ControllerExtensionModuleScrfaq extends Controller
{
    public function index($setting)
    {
        $this->load->model('extension/module/scrfaq');
        $this->load->language('extension/module/scrfaq');

        $this->document->addScript('catalog/view/javascript/collapse.js');

        $data['module_id'] = $setting['mid'];
        $data['scrfaq_title'] = $setting['name'];
        $data['scrfaq_descr'] = @$setting['descr'];
        $data['answers'] = $this->model_extension_module_scrfaq->getAnswer($setting['mid']);
        foreach ($data['answers'] as &$ans) {
            $ans['description'] = html_entity_decode($ans['description']);
        }
        return $this->load->view('extension/module/scrfaq', $data);
    }
}