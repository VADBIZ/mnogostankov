<?php
class ControllerExtensionModuleCategory3 extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/category3');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/module');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (!isset($this->request->get['module_id'])) {
				$this->model_setting_module->addModule('category3', $this->request->post);
			} else {
				$this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/category3', 'user_token=' . $this->session->data['user_token'], true)
		);

		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/module/category3', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('extension/module/category3', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
		}

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
		
		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
		}

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($module_info)) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}
		
		// Ссылки баннера
      
        if (isset($this->request->post['link1'])) {
			$data['link1'] = $this->request->post['link1'];
		} elseif (!empty($module_info)) {
			$data['link1'] = $module_info['link1'];
		} else {
			$data['link1'] = '';
		}
		
		if (isset($this->request->post['link2'])) {
			$data['link2'] = $this->request->post['link2'];
		} elseif (!empty($module_info)) {
			$data['link2'] = $module_info['link2'];
		} else {
			$data['link2'] = '';
		}
		
		if (isset($this->request->post['link3'])) {
			$data['link3'] = $this->request->post['link3'];
		} elseif (!empty($module_info)) {
			$data['link3'] = $module_info['link3'];
		} else {
			$data['link3'] = '';
		}
		
		// Текст на кнопке
		
		if (isset($this->request->post['button1'])) {
			$data['button1'] = $this->request->post['button1'];
		} elseif (!empty($module_info)) {
			$data['button1'] = $module_info['button1'];
		} else {
			$data['button1'] = '';
		}
		
		if (isset($this->request->post['button2'])) {
			$data['button2'] = $this->request->post['button2'];
		} elseif (!empty($module_info)) {
			$data['button2'] = $module_info['button2'];
		} else {
			$data['button2'] = '';
		}
		
		if (isset($this->request->post['button3'])) {
			$data['button3'] = $this->request->post['button3'];
		} elseif (!empty($module_info)) {
			$data['button3'] = $module_info['button3'];
		} else {
			$data['button3'] = '';
		}
		
		// Фоновые изображения баннеров
		
		if (isset($this->request->post['image1'])) {
			$data['image1'] = $this->request->post['image1'];
        } elseif (!empty($module_info)) {
			$data['image1'] = $module_info['image1'];  
		} else {
			$data['image1'] = '';
		}
		
		if (isset($this->request->post['image2'])) {
			$data['image2'] = $this->request->post['image2'];
        } elseif (!empty($module_info)) {
			$data['image2'] = $module_info['image2'];  
		} else {
			$data['image2'] = '';
		}
		
		if (isset($this->request->post['image3'])) {
			$data['image3'] = $this->request->post['image3'];
        } elseif (!empty($module_info)) {
			$data['image3'] = $module_info['image3'];  
		} else {
			$data['image3'] = '';
		}
		
		// TEXT
		
		if (isset($this->request->post['text1'])) {
			$data['text1'] = $this->request->post['text1'];
        } elseif (!empty($module_info)) {
			$data['text1'] = $module_info['text1'];  
		} else {
			$data['text1'] = '';
		}
		
		if (isset($this->request->post['text2'])) {
			$data['text2'] = $this->request->post['text2'];
        } elseif (!empty($module_info)) {
			$data['text2'] = $module_info['text2'];  
		} else {
			$data['text2'] = '';
		}
		
		if (isset($this->request->post['text3'])) {
			$data['text3'] = $this->request->post['text3'];
        } elseif (!empty($module_info)) {
			$data['text3'] = $module_info['text3'];  
		} else {
			$data['text3'] = '';
		}
		
		// TITLE
		
		if (isset($this->request->post['title1'])) {
			$data['title1'] = $this->request->post['title1'];
        } elseif (!empty($module_info)) {
			$data['title1'] = $module_info['title1'];  
		} else {
			$data['title1'] = '';
		}
		
		if (isset($this->request->post['title2'])) {
			$data['title2'] = $this->request->post['title2'];
        } elseif (!empty($module_info)) {
			$data['title2'] = $module_info['title2'];  
		} else {
			$data['title2'] = '';
		}
		
		if (isset($this->request->post['title3'])) {
			$data['title3'] = $this->request->post['title3'];
        } elseif (!empty($module_info)) {
			$data['title3'] = $module_info['title3'];  
		} else {
			$data['title3'] = '';
		}
		
		// Стили для фона
		
		if (isset($this->request->post['css1'])) {
			$data['css1'] = $this->request->post['css1'];
        } elseif (!empty($module_info)) {
			$data['css1'] = $module_info['css1'];  
		} else {
			$data['css1'] = '';
		}
		
		if (isset($this->request->post['css2'])) {
			$data['css2'] = $this->request->post['css2'];
        } elseif (!empty($module_info)) {
			$data['css2'] = $module_info['css2'];  
		} else {
			$data['css2'] = '';
		}
		
		if (isset($this->request->post['css3'])) {
			$data['css3'] = $this->request->post['css3'];
        } elseif (!empty($module_info)) {
			$data['css3'] = $module_info['css3'];  
		} else {
			$data['css3'] = '';
		}
		
		// Изображение товара(дополнительные изображения)
		
		if (isset($this->request->post['images1'])) {
			$data['images1'] = $this->request->post['images1'];
        } elseif (!empty($module_info)) {
			$data['images1'] = $module_info['images1'];  
		} else {
			$data['images1'] = '';
		}
		
		if (isset($this->request->post['images2'])) {
			$data['images2'] = $this->request->post['images2'];
        } elseif (!empty($module_info)) {
			$data['images2'] = $module_info['images2'];  
		} else {
			$data['images2'] = '';
		}
		
		if (isset($this->request->post['images3'])) {
			$data['images3'] = $this->request->post['images3'];
        } elseif (!empty($module_info)) {
			$data['images3'] = $module_info['images3'];  
		} else {
			$data['images3'] = '';
		}

		$this->load->model('tool/image');

		//FON
		
		if (isset($this->request->post['image1']) && is_file(DIR_IMAGE . $this->request->post['image1'])) {
			$data['thumb1'] = $this->model_tool_image->resize($this->request->post['image1'], 100, 100);
		} elseif (!empty($module_info) && isset($module_info['image1']) && is_file(DIR_IMAGE . $module_info['image1'])) {
			$data['thumb1'] = $this->model_tool_image->resize($module_info['image1'], 100, 100);
		} else {
			$data['thumb1'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}
		
		if (isset($this->request->post['image2']) && is_file(DIR_IMAGE . $this->request->post['image2'])) {
			$data['thumb2'] = $this->model_tool_image->resize($this->request->post['image2'], 100, 100);
		} elseif (!empty($module_info) && isset($module_info['image2']) && is_file(DIR_IMAGE . $module_info['image2'])) {
			$data['thumb2'] = $this->model_tool_image->resize($module_info['image2'], 100, 100);
		} else {
			$data['thumb2'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}
		
		if (isset($this->request->post['image3']) && is_file(DIR_IMAGE . $this->request->post['image3'])) {
			$data['thumb3'] = $this->model_tool_image->resize($this->request->post['image3'], 100, 100);
		} elseif (!empty($module_info) && isset($module_info['image3']) && is_file(DIR_IMAGE . $module_info['image3'])) {
			$data['thumb3'] = $this->model_tool_image->resize($module_info['image3'], 100, 100);
		} else {
			$data['thumb3'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}
		
		// DOP IMAGE
		
		if (isset($this->request->post['images1']) && is_file(DIR_IMAGE . $this->request->post['images1'])) {
			$data['thumbs1'] = $this->model_tool_image->resize($this->request->post['images1'], 100, 100);
		} elseif (!empty($module_info) && isset($module_info['images1']) && is_file(DIR_IMAGE . $module_info['images1'])) {
			$data['thumbs1'] = $this->model_tool_image->resize($module_info['images1'], 100, 100);
		} else {
			$data['thumbs1'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}
		
		if (isset($this->request->post['images2']) && is_file(DIR_IMAGE . $this->request->post['images2'])) {
			$data['thumbs2'] = $this->model_tool_image->resize($this->request->post['image2'], 100, 100);
		} elseif (!empty($module_info) && isset($module_info['images2']) && is_file(DIR_IMAGE . $module_info['images2'])) {
			$data['thumbs2'] = $this->model_tool_image->resize($module_info['images2'], 100, 100);
		} else {
			$data['thumbs2'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}
		
		if (isset($this->request->post['images3']) && is_file(DIR_IMAGE . $this->request->post['images3'])) {
			$data['thumbs3'] = $this->model_tool_image->resize($this->request->post['images3'], 100, 100);
		} elseif (!empty($module_info) && isset($module_info['image3']) && is_file(DIR_IMAGE . $module_info['images3'])) {
			$data['thumbs3'] = $this->model_tool_image->resize($module_info['images3'], 100, 100);
		} else {
			$data['thumbs3'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($module_info)) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/category3', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/category3')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}