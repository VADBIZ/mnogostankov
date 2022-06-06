<?php
class ControllerExtensionModuleBanners extends Controller {
	public function index($setting) {
		static $module = 0;

		$this->load->model('design/banner');
		$this->load->model('tool/image');
//
//		$data['banners'] = array();
//
//		$results = $this->model_design_banner->getBanner($setting['banner_id']);
//
//		foreach ($results as $result) {
//			if (is_file(DIR_IMAGE . $result['image'])) {
//				$data['banners'][] = array(
//					'title' => $result['title'],
//					'link'  => $result['link'],
//					'image' => $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height'])
//				);
//			}
//		}

		$data['module'] = $module++;
		
		$image = $this->model_tool_image->resize($this->config->get('config_index_banner'), $setting['width'], $setting['height']);
		$data['ib_image'] = $image;
		$data['ib_title'] = $this->config->get('config_ib_title');
		$data['ib_text'] = html_entity_decode($this->config->get('config_ib_text'), ENT_QUOTES, 'UTF-8');
		
		$data['ib_button'] = $this->config->get('config_ib_button');

		return $this->load->view('extension/module/banners', $data);
	}
}