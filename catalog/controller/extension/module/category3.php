<?php
class ControllerExtensionModuleCategory3 extends Controller {
	public function index($setting) {
        static $module = 0;
		$this->load->language('extension/module/category3');

		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$server = $this->config->get('config_ssl') . 'image/';
		} else {
			$server = $this->config->get('config_url') . 'image/';
		}
      
        if($setting['image1']) {
            $data['image1'] = $server.$setting['image1'];
        } else {
            $data['image1'] = false;
        }

		if($setting['images1']) {
            $data['images1'] = $server.$setting['images1'];
        } else {
            $data['images1'] = false;
        }
		
		if($setting['image2']) {
            $data['image2'] = $server.$setting['image2'];
        } else {
            $data['image2'] = false;
        }

		if($setting['images2']) {
            $data['images2'] = $server.$setting['images2'];
        } else {
            $data['images2'] = false;
        }
		
		if($setting['image3']) {
            $data['image3'] = $server.$setting['image3'];
        } else {
            $data['image3'] = false;
        }

		if($setting['images3']) {
            $data['images3'] = $server.$setting['images3'];
        } else {
            $data['images3'] = false;
        }
		
		if($setting['css1']) {
            $data['css1'] = $setting['css1'];
        } else {
            $data['css1'] = '';
        }
		
		if($setting['css2']) {
            $data['css2'] = $setting['css2'];
        } else {
            $data['css2'] = '';
        }
		
		if($setting['css3']) {
            $data['css3'] = $setting['css3'];
        } else {
            $data['css3'] = '';
        }
		
		$data['title1'] = $setting['title1'];
		$data['link1'] = $setting['link1'];
		$data['button1'] = $setting['button1'];
		$data['text1'] = $setting['text1'];
		
		$data['title2'] = $setting['title2'];
		$data['link2'] = $setting['link2'];
		$data['button2'] = $setting['button2'];
		$data['text2'] = $setting['text2'];
		
		$data['title3'] = $setting['title3'];
		$data['link3'] = $setting['link3'];
		$data['button3'] = $setting['button3'];
		$data['text3'] = $setting['text3'];

		return $this->load->view('extension/module/category3', $data);
	}
}