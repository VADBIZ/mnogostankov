<?php
class ControllerExtensionModuleMegamenuvhsheme extends Controller {
	public function index($setting) {
		$items_shmenu = array();
		$this->load->model('extension/module/megamenuvhsheme');
		$this->load->language('extension/module/megamenuvhsheme');
		$data['heading_title'] = $this->language->get('heading_title');	
		$data['menu_sheme_type'] = $this->model_extension_module_megamenuvhsheme->getTypeMenuSheme($setting['mm_sheme_id']);
		$data['sheme_menu_mask'] = $setting['sheme_menu_mask'];
		
		if($setting['status']=='1'){
			
			$data['lang_id'] = $this->config->get('config_language_id');
			$data['items']=array();
			$data['additional']=array();
			$menu_items_cache = $this->cache->get('mmsheme.' . $setting['mm_sheme_id'] . (int)$this->config->get('config_language_id').'.'. (int)$this->config->get('config_store_id'));
			
				if (!empty($menu_items_cache)) {
					$data['items'] = $menu_items_cache;
					$data['megamenu_status']=true;		
				} else {
					if(isset($setting['mm_sheme_id']) && ($setting['mm_sheme_id'] !='0')){
						$items_shmenu = $this->model_extension_module_megamenuvhsheme->getItemsMenuSheme($setting['mm_sheme_id']);
					}
					if(!empty($items_shmenu)) {
						$menu_items = $items_shmenu;
					} else {
						$menu_items = array();
					}
					
					foreach($menu_items as $datamenu){
						if($datamenu['menu_type']=="link" && $datamenu['status'] !='0')	{
							$data['items'][]=$this->model_extension_module_megamenuvhsheme->MegaMenuTypeLink($datamenu);
						}
						if($datamenu['menu_type']=="information" && $datamenu['status'] !='0')	{
							$data['items'][]=$this->model_extension_module_megamenuvhsheme->MegaMenuTypeInformation($datamenu);
						}
						if($datamenu['menu_type']=="manufacturer" && $datamenu['status'] !='0')	{
							$data['items'][]=$this->model_extension_module_megamenuvhsheme->MegaMenuTypeManufacturer($datamenu);
						}
						if($datamenu['menu_type']=="product" && $datamenu['status'] !='0'){
							$data['items'][]=$this->model_extension_module_megamenuvhsheme->MegaMenuTypeProduct($datamenu);
						}
						if($datamenu['menu_type']=="category" && $datamenu['status'] !='0')	{
							$data['items'][] = $this->model_extension_module_megamenuvhsheme->MegaMenuTypeCategory($datamenu);
						}
						if($datamenu['menu_type']=="html" && $datamenu['status'] !='0')	{
							$data['items'][]=$this->model_extension_module_megamenuvhsheme->MegaMenuTypeHtml($datamenu);
						}
						if($datamenu['menu_type']=="freelink" && $datamenu['status'] !='0')	{
							$data['items'][]=$this->model_extension_module_megamenuvhsheme->MegaMenuTypeFreeLink($datamenu);
						}
					}
					
					$menu_items_cache = $data['items'];	
					$this->cache->set('mmsheme.' . $setting['mm_sheme_id'] . (int)$this->config->get('config_language_id') . '.'. (int)$this->config->get('config_store_id'), $menu_items_cache);		
					$data['megamenu_status'] = true;
				}		
			} else { 
				$data['megamenu_status'] = false;
			}

		return $this->load->view('extension/module/megamenuvhsheme', $data);
	}
}
