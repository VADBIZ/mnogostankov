<?php
class ControllerCommonMenuvh extends Controller {
	public function load_mob_menu(){
			$this->response->setOutput($this->index($load_mmenu = true));
	}

	public function index($load_mmenu = false) {
		$megamenu_setting = $this->config->get('megamenu_setting');

		if($megamenu_setting['status']=='1'){
			$detect = new Mobile_Detect();
			$data['deviceType'] = ($detect->isMobile() ? ($detect->isTablet() ? 'isMobile' : 'isTablet') : 'computer');

			$this->load->language('common/header');
			$data['text_category'] = $this->language->get('text_category');
			$this->load->model('module/nsmenu');
			$data['hmenu_type'] = $megamenu_setting['horizontal_menu_width'];
			$data['type_mobile_menu'] = $megamenu_setting['type_mobile_menu'];
			$data['main_menu_mask'] = $megamenu_setting['main_menu_mask'];
			$data['config_main_menu_selection'] = $megamenu_setting['main_menu_selection'];
			$data['config_fixed_panel_top'] = $megamenu_setting['fixed_panel_top'];
			$data['lang_id'] = $this->config->get('config_language_id');
			$data['items']=array();
			$data['additional']=array();
			$menu_items_cache = $this->cache->get('mmheader11.' . (int)$this->config->get('config_language_id').'.'. (int)$this->config->get('config_store_id'));

				if (!empty($menu_items_cache)) {
					$data['items'] = $menu_items_cache;
					$config_menu_item = $this->model_module_nsmenu->getItemsMenu();
					if(!empty($config_menu_item)) {
						$menu_items = $config_menu_item;
					} else {
						$menu_items = array();
					}

					foreach($menu_items as $datamenu){
						if($datamenu['additional_menu']=='additional' && $datamenu['status'] !='0')	{
							$data['additional'][] = 'additional';
						}
					}

					$data['megamenu_status']=true;
				} else {
					$config_menu_item = $this->model_module_nsmenu->getItemsMenu();

					if(!empty($config_menu_item)) {
						$menu_items = $config_menu_item;
					} else {
						$menu_items = array();
					}

					foreach($menu_items as $datamenu){
						if($datamenu['additional_menu']=="additional" && $datamenu['status'] !='0')	{
							$data['additional'][] = 'additional';
						}
						if($datamenu['menu_type']=="link" && $datamenu['status'] !='0')	{
							$data['items'][]=$this->model_module_nsmenu->MegaMenuTypeLink($datamenu);
						}
						if($datamenu['menu_type']=="information" && $datamenu['status'] !='0')	{
							$data['items'][]=$this->model_module_nsmenu->MegaMenuTypeInformation($datamenu);
						}
						if($datamenu['menu_type']=="manufacturer" && $datamenu['status'] !='0')	{
							$data['items'][]=$this->model_module_nsmenu->MegaMenuTypeManufacturer($datamenu);
						}
						if($datamenu['menu_type']=="product" && $datamenu['status'] !='0'){
							$data['items'][]=$this->model_module_nsmenu->MegaMenuTypeProduct($datamenu);
						}
						if($datamenu['menu_type']=="category" && $datamenu['status'] !='0')	{
							$data['items'][] = $this->model_module_nsmenu->MegaMenuTypeCategory($datamenu);
						}
						if($datamenu['menu_type']=="html" && $datamenu['status'] !='0')	{
							$data['items'][]=$this->model_module_nsmenu->MegaMenuTypeHtml($datamenu);
						}
						if($datamenu['menu_type']=="freelink" && $datamenu['status'] !='0')	{
							$data['items'][]=$this->model_module_nsmenu->MegaMenuTypeFreeLink($datamenu);
						}
					}

					$menu_items_cache = $data['items'];
					$this->cache->set('mmheader.' . (int)$this->config->get('config_language_id') . '.'. (int)$this->config->get('config_store_id'), $menu_items_cache);
					$data['megamenu_status']=true;

				}
			} else {
				$data['megamenu_status']=false;
			}
        $data['search'] = $this->load->controller('common/search');
        $data['cart'] = $this->load->controller('common/cart');

		if (isset($load_mmenu) && ($load_mmenu== 1)) {
			return $this->load->view('common/mob_menu_left', $data);
		}
		if ($data['config_main_menu_selection'] == '1') {
			return $this->load->view('common/menuvh', $data);
		}
		if ($data['config_main_menu_selection'] == '0') {
			return $this->load->view('common/menuh', $data);
		}
	}
}
