<?php 
class ControllerPaymentEbanx extends Controller {
	private $error = array(); 

	public function index() {
		$this->language->load('payment/ebanx');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('ebanx', $this->request->post);				
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');
		$this->data['text_pay_mode'] = $this->language->get('text_pay_mode');
		$this->data['text_test_mode'] = $this->language->get('text_test_mode');
		
		$this->data['entry_merchant_key'] = $this->language->get('entry_merchant_key');
		$this->data['entry_test'] = $this->language->get('entry_test');
		$this->data['entry_order_status_ca'] = $this->language->get('entry_order_status_ca');		
		$this->data['entry_order_status_co'] = $this->language->get('entry_order_status_co');		
		$this->data['entry_order_status_op'] = $this->language->get('entry_order_status_op');		
		$this->data['entry_order_status_pe'] = $this->language->get('entry_order_status_pe');		
		$this->data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 		if (isset($this->error['merchant_key'])) {
			$this->data['error_merchant_key'] = $this->error['merchant_key'];
		} else {
			$this->data['error_merchant_key'] = '';
		}

 		if (isset($this->error['password'])) {
			$this->data['error_password'] = $this->error['password'];
		} else {
			$this->data['error_password'] = '';
		}

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_payment'),
			'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('payment/ebanx', 'token=' . $this->session->data['token'], 'SSL'),      		
      		'separator' => ' :: '
   		);
				
		$this->data['action'] = $this->url->link('payment/ebanx', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');
		
		if (isset($this->request->post['ebanx_merchant_key'])) {
			$this->data['ebanx_merchant_key'] = $this->request->post['ebanx_merchant_key'];
		} else {
			$this->data['ebanx_merchant_key'] = $this->config->get('ebanx_merchant_key');
		}
		
		$this->data['callback'] = HTTP_CATALOG . 'index.php?route=payment/ebanx/callback';

		if (isset($this->request->post['ebanx_mode'])) {
			$this->data['ebanx_mode'] = $this->request->post['ebanx_mode'];
		} else {
			$this->data['ebanx_mode'] = $this->config->get('ebanx_mode');
		}
		
		if (isset($this->request->post['ebanx_order_status_ca_id'])) {
			$this->data['ebanx_order_status_ca_id'] = $this->request->post['ebanx_order_status_ca_id'];
		} else {
			$this->data['ebanx_order_status_ca_id'] = $this->config->get('ebanx_order_status_ca_id'); 
		} 
		
		if (isset($this->request->post['ebanx_order_status_co_id'])) {
			$this->data['ebanx_order_status_co_id'] = $this->request->post['ebanx_order_status_co_id'];
		} else {
			$this->data['ebanx_order_status_co_id'] = $this->config->get('ebanx_order_status_co_id'); 
		} 
		
		if (isset($this->request->post['ebanx_order_status_op_id'])) {
			$this->data['ebanx_order_status_op_id'] = $this->request->post['ebanx_order_status_op_id'];
		} else {
			$this->data['ebanx_order_status_op_id'] = $this->config->get('ebanx_order_status_op_id'); 
		} 
		
		if (isset($this->request->post['ebanx_order_status_pe_id'])) {
			$this->data['ebanx_order_status_pe_id'] = $this->request->post['ebanx_order_status_pe_id'];
		} else {
			$this->data['ebanx_order_status_pe_id'] = $this->config->get('ebanx_order_status_pe_id'); 
		} 
		
		$this->load->model('localisation/order_status');
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if (isset($this->request->post['ebanx_geo_zone_id'])) {
			$this->data['ebanx_geo_zone_id'] = $this->request->post['ebanx_geo_zone_id'];
		} else {
			$this->data['ebanx_geo_zone_id'] = $this->config->get('ebanx_geo_zone_id'); 
		} 

		$this->load->model('localisation/geo_zone');
										
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($this->request->post['ebanx_status'])) {
			$this->data['ebanx_status'] = $this->request->post['ebanx_status'];
		} else {
			$this->data['ebanx_status'] = $this->config->get('ebanx_status');
		}
		
		if (isset($this->request->post['ebanx_sort_order'])) {
			$this->data['ebanx_sort_order'] = $this->request->post['ebanx_sort_order'];
		} else {
			$this->data['ebanx_sort_order'] = $this->config->get('ebanx_sort_order');
		}

		$this->template = 'payment/ebanx.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'payment/ebanx')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->request->post['ebanx_merchant_key']) {
			$this->error['merchant_key'] = $this->language->get('error_merchant_key');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
	
	public function install() {
        $this->load->model('payment/ebanx');
        $this->model_payment_ebanx->install();
    }

	public function uninstall() {
        $this->load->model('payment/ebanx');
        $this->model_payment_ebanx->uninstall();
    }

}
?>