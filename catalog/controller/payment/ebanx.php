<?php

class ControllerPaymentEbanx extends Controller {

	protected function index() {
		$this->data['button_confirm'] = $this->language->get('button_confirm');

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		if ($this->config->get('ebanx_mode') == 'pay') {
			$this->data['action'] = 'https://www.ebanx.com/pay/ws/';
		} else {
			$this->data['action'] = 'https://www.ebanx.com/test/ws/';
		}

		if (!$order_info['payment_address_2']) {
			$address_ebanx = $order_info['payment_address_1'];
		} else {
			$address_ebanx = $order_info['payment_address_1'] . ', ' . $order_info['payment_address_2'];
		}


		$params = 'integration_key=' . $this->config->get('ebanx_merchant_key');
		$params .= '&name=' . $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];
		$params .= '&email=' . $order_info['email'];
		$params .= '&payment_type_code=_all';

		$params .= '&amount=' . $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);

		$params .= '&currency_code=' . $order_info['currency_code'];

		$params .= '&merchant_payment_code=' . $order_info['order_id'];

		$params .= '&cpf=';
		$params .= '&birth_date=';
		$params .= '&zipcode=' . $order_info['payment_postcode'];
		$params .= '&street_number=';
		$params .= '&phone_number=' . $order_info['telephone'];

		$ch = curl_init($this->data['action'] . 'request');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // RETURN THE CONTENTS OF THE CALL
		$json_response = curl_exec($ch);
		curl_close($ch);

		$response = json_decode($json_response);

		if ($response->status == 'SUCCESS') {
			$this->load->model('payment/ebanx');
			$this->model_payment_ebanx->setPaymentHash($order_info['order_id'], $response->payment->hash);

			$this->data['hash'] = $response->payment->hash;
			
			$this->load->model('checkout/order');
			
			$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('ebanx_order_status_op_id'));
			
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/ebanx.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/payment/ebanx.tpl';
			} else {
				$this->template = 'default/template/payment/ebanx.tpl';
			}

			$this->render();
		}
	}

	public function callback() {
		$this->language->load('payment/ebanx');
		
		$this->data['title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));

		if (!isset($this->request->server['HTTPS']) || ($this->request->server['HTTPS'] != 'on')) {
			$this->data['base'] = $this->config->get('config_url');
		} else {
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
		
		if ($this->config->get('ebanx_mode') == 'pay') {
			$this->data['action'] = 'https://www.ebanx.com/pay/ws/';
		} else {
			$this->data['action'] = 'https://www.ebanx.com/test/ws/';
		}

		$params = 'integration_key=' . $this->config->get('ebanx_merchant_key');
		$params .= '&hash=' . $this->request->get['hash'];

		$ch = curl_init($this->data['action'] . 'query');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // RETURN THE CONTENTS OF THE CALL
		$json_response = curl_exec($ch);
		curl_close($ch);

		$response = json_decode($json_response);

		

		if (isset($response->status) && $response->status == 'SUCCESS' && ($response->payment->status == 'PE' || $response->payment->status == 'CO')) {
			$this->load->model('checkout/order');

			if ($response->payment->status == 'CO')
				$this->model_checkout_order->update($this->session->data['order_id'], $this->config->get('ebanx_order_status_co_id'));

			elseif ($response->payment->status == 'PE')
				$this->model_checkout_order->update($this->session->data['order_id'], $this->config->get('ebanx_order_status_pe_id'));

			$this->data['continue'] = $this->url->link('checkout/success');

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/ebanx_success.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/payment/ebanx_success.tpl';
			} else {
				$this->template = 'default/template/payment/ebanx_success.tpl';
			}

			$this->response->setOutput($this->render());
		} else {
			$this->data['continue'] = $this->url->link('checkout/cart');

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/ebanx_failure.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/payment/ebanx_failure.tpl';
			} else {
				$this->template = 'default/template/payment/ebanx_failure.tpl';
			}

			$this->response->setOutput($this->render());
		}
	}

	public function notify() {
		if ($this->config->get('ebanx_mode') == 'pay') {
			$url = 'https://www.ebanx.com/pay/ws/';
		} else {
			$url = 'https://www.ebanx.com/test/ws/';
		}
		
		$hashs = explode(',', $this->request->post['hash_codes']);
		
		foreach ($hashs as $hash) {
			$params = 'integration_key=' . $this->config->get('ebanx_merchant_key');
			$params .= '&hash=' . $hash;

			$ch = curl_init($url . 'query');
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // RETURN THE CONTENTS OF THE CALL
			$json_response = curl_exec($ch);
			curl_close($ch);

			$response = json_decode($json_response);
			
			if (isset($response->status) && $response->status == 'SUCCESS') {
				$this->load->model('checkout/order');

				if ($response->payment->status == 'CA')
					$this->model_checkout_order->update($response->payment->merchant_payment_code, $this->config->get('ebanx_order_status_ca_id'));

				elseif ($response->payment->status == 'CO')
					$this->model_checkout_order->update($response->payment->merchant_payment_code, $this->config->get('ebanx_order_status_co_id'));

				elseif ($response->payment->status == 'OP')
					$this->model_checkout_order->update($response->payment->merchant_payment_code, $this->config->get('ebanx_order_status_op_id'));

				elseif ($response->payment->status == 'PE')
					$this->model_checkout_order->update($response->payment->merchant_payment_code, $this->config->get('ebanx_order_status_pe_id'));
			}
		}
	}

}

?>