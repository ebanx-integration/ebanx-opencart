<?php

/**
 * Copyright (c) 2013, EBANX Tecnologia da Informação Ltda.
 *  All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * Redistributions of source code must retain the above copyright notice, this
 * list of conditions and the following disclaimer.
 *
 * Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation
 * and/or other materials provided with the distribution.
 *
 * Neither the name of EBANX nor the names of its
 * contributors may be used to endorse or promote products derived from
 * this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */


require_once DIR_SYSTEM . 'library/ebanx-php/src/autoload.php';

/**
 * The payment actions controller
 */
class ControllerPaymentEbanx extends Controller
{
	/**
	 * Initialize the EBANX settings before usage
	 * @return void
	 */
	protected function _setupEbanx()
	{
		\Ebanx\Config::set(array(
		    'integrationKey' => $this->config->get('ebanx_merchant_key')
		  , 'testMode'       => ($this->config->get('ebanx_mode') == 'test')
		  , 'directMode'		 => ($this->config->get('ebanx_direct') == 1)
		));
	}

	/**
	 * EBANX gateway custom fields for the checkout
	 * @return void
	 */
	public function index()
	{
		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$this->data['button_confirm'] = $this->language->get('button_confirm');

		$this->data['enable_installments'] = $this->config->get('ebanx_enable_installments');

		// Order total with interest
		$interest    = $this->config->get('ebanx_installments_interest');
		$order_total = ($order_info['total'] * (100 + floatval($interest))) / 100.0;
		$this->data['order_total_interest'] = $order_total;

		// Enforce minimum installment value (R$20)
		$maxInstallments = $this->config->get('ebanx_max_installments');
		$currencyCode    = strtoupper($order_info['currency_code']);

		switch ($currencyCode)
    {
      case 'USD':
        $totalReal = $order_total * 2.5;
        break;
      case 'EUR':
        $totalReal = $order_total * 3.4;
        break;
      case 'BRL':
      default:
        $totalReal = $order_total;
        break;
    }

    if (($totalReal / 20) < $maxInstallments)
    {
	    $maxInstallments = floor($totalReal / 20);
    }

		$this->data['max_installments'] = $maxInstallments;

		// Form translations
		$this->language->load('payment/ebanx');
		$this->data['entry_installments_number'] = $this->language->get('entry_installments_number');
		$this->data['entry_installments_cc']     = $this->language->get('entry_installments_cc');
		$this->data['text_wait'] = $this->language->get('text_wait');

		// Currency symbol and order total for display purposes
		$this->data['order_total']   = $order_info['total'];
		$this->data['currency_code'] = $order_info['currency_code'];

		$this->data['ebanx_direct_cards'] = $this->config->get('ebanx_direct_cards');

		// Render normal or direct checkout page
		$template = 'ebanx';
		if ($this->config->get('ebanx_direct') == 1)
		{
			$template .= '_direct';

			// Preload customer data (CPF and DOB)
			$this->load->model('customer/ebanx');
    	$info = $this->model_customer_ebanx->findByCustomerId($this->customer->getId());

    	$this->data['ebanx_cpf'] = '';
  		$this->data['ebanx_dob'] = '';

    	if ($info)
    	{
    		$this->data['ebanx_cpf'] = $info['cpf'];
    		$this->data['ebanx_dob'] = $info['dob'];
    	}
		}

		// Render a custom template if it's available
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/' . $template . '.tpl'))
		{
			$this->template = $this->config->get('config_template') . '/template/payment/' . $template . '.tpl';
		}
		else
		{
			$this->template = 'default/template/payment/' . $template . '.tpl';
		}

		$this->render();
	}

	/**
	 * EBANX checkout action. Redirects to the EBANX URI.
	 * @return void
	 */
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

			// Add interest to the order total
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

	/**
	 * EBANX direct checkout action. Redirects to the success URI.
	 * @return void
	 */
	public function checkoutDirect()
	{
		$this->_setupEbanx();
		$this->load->model('checkout/order');
		$this->load->model('customer/ebanx');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		// Get full address
		$address = $order_info['payment_address_1'];
		if (!!$order_info['payment_address_2'])
		{
			$address .= ', ' . $order_info['payment_address_2'];
		}

		$params = array(
	      'mode'      => 'full'
	    , 'operation' => 'request'
	    , 'payment'   => array(
	          'name'              => $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname']
	        , 'document'          => preg_replace('/\D/', '', $this->request->post['ebanx']['cpf'])
	        , 'birth_date'        => $this->request->post['ebanx']['dob']
	        , 'email'             => $order_info['email']
	        , 'phone_number'      => $order_info['telephone']
	        , 'currency_code'     => $this->config->get('config_currency')
	        , 'amount_total'      => $order_info['total']
	        , 'payment_type_code' => $this->request->post['ebanx']['method']
	        , 'merchant_payment_code' => $order_info['order_id']
	        , 'zipcode'           => $order_info['payment_postcode']
	        , 'address'           => $address
	        , 'street_number'     => preg_replace('/[\D]/', '', $address)
	        , 'city'              => $order_info['payment_city']
	        , 'state'             => $order_info['payment_zone_code']
	        , 'country'           => 'br'
	    )
	  );

		// Add installments to order
		if ($this->request->post['ebanx']['method'] == 'creditcard' &&
			  isset($this->request->post['ebanx']['installments']) && intval($this->request->post['ebanx']['installments']) > 1 &&
			  $this->request->post['ebanx']['cc_type'] != 'discover')
		{
			$params['payment']['instalments']       = $this->request->post['ebanx']['installments'];
			$params['payment']['payment_type_code'] = $this->request->post['ebanx']['cc_type'];

			// Add interest to the order total
			$interest    = $this->config->get('ebanx_installments_interest');
			$order_total = ($order_info['total'] * (100 + floatval($interest))) / 100.0;
			$params['payment']['amount_total'] = number_format($order_total, 2, '.', '');
		}

    // Add credit card fields if the method is credit card
    if ($this->request->post['ebanx']['method'] == 'creditcard')
    {
        $params['payment']['payment_type_code'] = $this->request->post['ebanx']['cc_type'];
        $params['payment']['creditcard'] = array(
            'card_name'     => $this->request->post['ebanx']['cc_name']
          , 'card_number'   => $this->request->post['ebanx']['cc_number']
          , 'card_cvv'      => $this->request->post['ebanx']['cc_cvv']
          , 'card_due_date' => str_pad($this->request->post['ebanx']['cc_exp']['month'], 2, '0', STR_PAD_LEFT)
          										 . '/' . $this->request->post['ebanx']['cc_exp']['year']
        );
    }

    // Persist the customer DOB and CPF
    $data = array(
  		  'cpf' => $params['payment']['document']
  		, 'dob' => $params['payment']['birth_date']
  	);

  	$id = $this->customer->getId();
    if ($this->model_customer_ebanx->findByCustomerId($id))
    {
    	$this->model_customer_ebanx->update($id, $data);
    }
    else
    {
    	$this->model_customer_ebanx->insert($id, $data);
    }

    // Do the payment request
		$response = \Ebanx\Ebanx::doRequest($params);

		if ($response->status == 'SUCCESS')
		{
			$this->load->model('payment/ebanx');
			$this->model_payment_ebanx->setPaymentHash($order_info['order_id'], $response->payment->hash);

			$this->load->model('checkout/order');

			$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('ebanx_order_status_op_id'));

			// If payment method is boleto, redirect to boleto page
			if ($response->payment->payment_type_code == 'boleto')
			{
				echo $this->_getBaseUrl() . 'index.php?route=payment/ebanx/boleto/&hash=' . $response->payment->hash;
			}
			// Else, redirect to callback page
			else
			{
				echo $this->_getBaseUrl() . 'index.php?route=payment/ebanx/callback/&hash=' . $response->payment->hash;
			}
		}
		else
		{
			// Display the EBANX error message or the default one
			if (isset($response->status_message))
			{
				echo $response->status_message;
			}
			else
			{
				echo 'Unknown error, please contact the store owner.';
			}
		}

		die();
	}

	/**
	 * Shows the customer the boleto printing link
	 * @return void
	 */
	public function boleto()
	{
		$this->_setupEbanx();

		$hash  = $this->request->get['hash'];
		$query = \Ebanx\Ebanx::doQuery(array('hash' => $hash));

		// Renders the default success page with the "Print Boleto" button
		if ($query->status == 'SUCCESS')
		{
			$this->language->load('checkout/success');

			$this->document->setTitle($this->language->get('heading_title'));

			$this->data['breadcrumbs'] = array();

			$this->data['breadcrumbs'][] = array(
				'href'      => $this->url->link('common/home'),
				'text'      => $this->language->get('text_home'),
				'separator' => false
			);

			$this->data['breadcrumbs'][] = array(
				'href'      => $this->url->link('checkout/cart'),
				'text'      => $this->language->get('text_basket'),
				'separator' => $this->language->get('text_separator')
			);

			$this->data['breadcrumbs'][] = array(
				'href'      => $this->url->link('checkout/checkout', '', 'SSL'),
				'text'      => $this->language->get('text_checkout'),
				'separator' => $this->language->get('text_separator')
			);

			$this->data['breadcrumbs'][] = array(
				'href'      => $this->url->link('checkout/success'),
				'text'      => $this->language->get('text_success'),
				'separator' => $this->language->get('text_separator')
			);

			$this->data['heading_title'] = $this->language->get('heading_title');

			if ($this->customer->isLogged())
			{
				$this->data['text_message'] = sprintf($this->language->get('text_customer'), $this->url->link('account/account', '', 'SSL'), $this->url->link('account/order', '', 'SSL'), $this->url->link('account/download', '', 'SSL'), $this->url->link('information/contact'));
			}
			else
			{
				$this->data['text_message'] = sprintf($this->language->get('text_guest'), $this->url->link('information/contact'));
			}

			$this->data['button_continue'] = $this->language->get('button_continue');
			$this->data['continue'] = $this->url->link('common/home');

			$this->data['boleto'] = $query->payment->boleto_url;

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/ebanx_boleto.tpl'))
			{
				$this->template = $this->config->get('config_template') . '/template/payment/ebanx_boleto.tpl';
			}
			else
			{
				$this->template = 'default/template/payment/ebanx_boleto.tpl';
			}

			$this->children = array(
				  'common/column_left'
				, 'common/column_right'
				, 'common/content_top'
				, 'common/content_bottom'
				, 'common/footer'
				, 'common/header'
			);

			$this->response->setOutput($this->render());
		}
		else
		{
			$this->redirect($this->url->link('checkout/success'));
		}
	}

	/**
	 * Gets the store base URL
	 * @return string
	 */
	protected function _getBaseUrl()
	{
		return $this->config->get('config_url');
	}

	/**
	 * Callback action. It's called when returning from EBANX.
	 * @return void
	 */
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

		// Setup translations
		$this->data['language'] 		 = $this->language->get('code');
		$this->data['direction'] 		 = $this->language->get('direction');
		$this->data['heading_title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
		$this->data['text_response'] = $this->language->get('text_response');
		$this->data['text_success']  = $this->language->get('text_success');
		$this->data['text_failure']  = $this->language->get('text_failure');
		$this->data['text_success_wait'] = sprintf($this->language->get('text_success_wait'), $this->url->link('checkout/success'));
		$this->data['text_failure_wait'] = sprintf($this->language->get('text_failure_wait'), $this->url->link('checkout/checkout', '', 'SSL'));

		$response = \Ebanx\Ebanx::doQuery(array('hash' => $this->request->get['hash']));

		// Update the order status, then redirect to the success page
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

			$this->redirect($this->url->link('checkout/success'));
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

	/**
	 * Notification action. It's called when a payment status is updated.
	 * @return void
	 */
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

				// Update the order status according to the settings
				$order_id = str_replace('_', '', $response->payment->merchant_payment_code);
				$status = $this->config->get('ebanx_order_status_' . strtolower($response->payment->status) . '_id');
				$this->model_checkout_order->update($order_id, $status);
			}
		}
	}
}