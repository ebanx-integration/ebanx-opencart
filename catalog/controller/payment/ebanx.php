<?php

require_once DIR_SYSTEM . 'library/ebanx-php/src/autoload.php';

class ControllerPaymentEbanx extends Controller
{
	protected function _setupEbanx()
	{
		// Set EBANX configs
		\Ebanx\Config::set(array(
		    'integrationKey' => $this->config->get('ebanx_merchant_key')
		  , 'testMode'       => ($this->config->get('ebanx_mode') == 'test')
		));
	}

	public function index()
	{
		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$this->data['button_confirm'] = $this->language->get('button_confirm');

		$this->data['enable_installments'] = $this->config->get('ebanx_enable_installments');
		$this->data['max_installments']    = $this->config->get('ebanx_max_installments');

		// Order total with interest
		$interest    = $this->config->get('ebanx_installments_interest');
		$order_total = ($order_info['total'] * (100 + floatval($interest))) / 100.0;
		$this->data['order_total_interest'] = $order_total;

		// Form translations
		$this->language->load('payment/ebanx');
		$this->data['entry_installments_number'] = $this->language->get('entry_installments_number');
		$this->data['entry_installments_cc']     = $this->language->get('entry_installments_cc');
		$this->data['text_wait'] = $this->language->get('text_wait');

		// Currency symbol and order total for display purposes
		$this->data['order_total']   = $order_info['total'];
		$this->data['currency_code'] = $order_info['currency_code'];

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/ebanx.tpl'))
		{
			$this->template = $this->config->get('config_template') . '/template/payment/ebanx.tpl';
		}
		else
		{
			$this->template = 'default/template/payment/ebanx.tpl';
		}

		$this->render();
	}

	public function checkout()
	{
		$this->_setupEbanx();

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$address = $order_info['payment_address_1'];
		if (!!$order_info['payment_address_2'])
		{
			$address .= ', ' . $order_info['payment_address_2'];
		}

		$params = array(
				'name' 					=> $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname']
			, 'email' 				=> $order_info['email']
			, 'amount' 				=> $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false)
			, 'currency_code' => $order_info['currency_code']
			, 'address'			  => $address
			, 'zipcode' 		  => $order_info['payment_postcode']
			, 'phone_number'  => $order_info['telephone']
			, 'payment_type_code' 		=> '_all'
			, 'merchant_payment_code' => $order_info['order_id']
		);

		// Installments
		if (isset($this->request->post['instalments']) && $this->request->post['instalments'] > 1)
		{
			$params['instalments']       = $this->request->post['instalments'];
			$params['payment_type_code'] = $this->request->post['payment_type_code'];

			$interest    			= $this->config->get('ebanx_installments_interest');
			$order_total 			= ($order_info['total'] * (100 + floatval($interest))) / 100.0;
			$params['amount'] = $this->currency->format($order_total, $order_info['currency_code'], $order_info['currency_value'], false);
		}

		$response = \Ebanx\Ebanx::doRequest($params);

		if ($response->status == 'SUCCESS')
		{
			$this->load->model('payment/ebanx');
			$this->model_payment_ebanx->setPaymentHash($order_info['order_id'], $response->payment->hash);

			$this->load->model('checkout/order');

			$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('ebanx_order_status_op_id'));

			echo $response->redirect_url;
			die();
		}
	}

	public function callback()
	{
		$this->_setupEbanx();

		$this->language->load('payment/ebanx');

		$this->data['title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));

		$this->data['base'] = $this->config->get('config_url');
		if (isset($this->request->server['HTTPS']) && ($this->request->server['HTTPS'] == 'on'))
		{
			$this->data['base'] = $this->config->get('config_ssl');
		}

		$this->data['language'] = $this->language->get('code');
		$this->data['direction'] = $this->language->get('direction');

		$this->data['heading_title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));

		$this->data['text_response'] = $this->language->get('text_response');
		$this->data['text_success'] = $this->language->get('text_success');
		$this->data['text_success_wait'] = sprintf($this->language->get('text_success_wait'), $this->url->link('checkout/success'));
		$this->data['text_failure'] = $this->language->get('text_failure');
		$this->data['text_failure_wait'] = sprintf($this->language->get('text_failure_wait'), $this->url->link('checkout/checkout', '', 'SSL'));

		$response = \Ebanx\Ebanx::doQuery(array('hash' => $this->request->get['hash']));

		if (isset($response->status) && $response->status == 'SUCCESS' && ($response->payment->status == 'PE' || $response->payment->status == 'CO'))
		{
			$this->load->model('checkout/order');

			if ($response->payment->status == 'CO')
			{
				$this->model_checkout_order->update($this->session->data['order_id'], $this->config->get('ebanx_order_status_co_id'));
			}
			elseif ($response->payment->status == 'PE')
			{
				$this->model_checkout_order->update($this->session->data['order_id'], $this->config->get('ebanx_order_status_pe_id'));
			}

			$this->data['continue'] = $this->url->link('checkout/success');

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/ebanx_success.tpl'))
			{
				$this->template = $this->config->get('config_template') . '/template/payment/ebanx_success.tpl';
			}
			else
			{
				$this->template = 'default/template/payment/ebanx_success.tpl';
			}

			$this->response->setOutput($this->render());
		}
		else
		{
			$this->data['continue'] = $this->url->link('checkout/cart');

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/ebanx_failure.tpl'))
			{
				$this->template = $this->config->get('config_template') . '/template/payment/ebanx_failure.tpl';
			}
			else
			{
				$this->template = 'default/template/payment/ebanx_failure.tpl';
			}

			$this->response->setOutput($this->render());
		}
	}

	public function notify()
	{
		$this->_setupEbanx();

		$hashes = explode(',', $this->request->post['hash_codes']);

		foreach ($hashes as $hash)
		{
			$response = \Ebanx\Ebanx::doQuery(array('hash' => $hash));

			if (isset($response->status) && $response->status == 'SUCCESS')
			{
				$this->load->model('checkout/order');

				$order_id = str_replace('_', '', $response->payment->merchant_payment_code);
				$status = $this->config->get('ebanx_order_status_' . strtolower($response->payment->status) . '_id');
				$this->model_checkout_order->update($order_id, $status);
			}
		}
	}
}