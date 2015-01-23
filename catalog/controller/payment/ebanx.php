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
		  , 'directMode'     => true
		));
	}

	/**
	 * Checks if it's opencart 1 or 2
	 * @return mixed
	 */
	protected function isOpencart2()
	{
		return (intval(VERSION) >= 2);
	}

	/**
	 * Save EBANX stuff to log
	 * @param  string $text Text to log
	 * @return void
	 */
	protected function _log($text)
	{
		return;
	}

	/**
	 * EBANX gateway custom fields for the checkout
	 * @return void
	 */
	public function index()
	{
		$view = array();

		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$view['button_confirm'] = $this->language->get('button_confirm');

		// Disable installments on checkout mode
		$view['enable_installments'] = false;

		// Order total with interest
		$interest    = $this->config->get('ebanx_installments_interest');
		$order_total = ($order_info['total'] * (100 + floatval($interest))) / 100.0;
		$view['order_total_interest'] = $order_total;

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

		$view['max_installments'] = $maxInstallments;

		// Form translations
		$this->language->load('payment/ebanx');
		$view['text_wait'] 				 = $this->language->get('text_wait');
		$view['entry_installments_number'] = $this->language->get('entry_installments_number');
		$view['entry_installments_cc']     = $this->language->get('entry_installments_cc');
		$view['entry_payment_method']      = $this->language->get('entry_payment_method');
		$view['entry_dob']                 = $this->language->get('entry_dob');
		$view['entry_card_name']           = $this->language->get('entry_card_name');
		$view['entry_card_number']         = $this->language->get('entry_card_number');
		$view['entry_card_type']           = $this->language->get('entry_card_type');
		$view['entry_card_exp']            = $this->language->get('entry_card_exp');
		$view['entry_ebanx_details']       = $this->language->get('entry_ebanx_details');
		$view['entry_interest']			 = $this->language->get('entry_interest');
		$view['entry_please_select']   	 = $this->language->get('entry_please_select');
		$view['entry_month'] 				 = $this->language->get('entry_month');
		$view['entry_year']  				 = $this->language->get('entry_year');

		// Currency symbol and order total for display purposes
		$view['order_total']   = $order_info['total'];
		$view['currency_code'] = $order_info['currency_code'];

		$view['ebanx_direct_cards']  = $this->config->get('ebanx_direct_cards');
		$view['ebanx_direct_boleto'] = $this->config->get('ebanx_direct_boleto');
		$view['ebanx_direct_tef']    = $this->config->get('ebanx_direct_tef');

		// Check if installments are enabled for direct mode
		$view['enable_installments'] = $this->config->get('ebanx_enable_installments');

		// Render normal or direct (Brazil) checkout page
		$template = 'ebanx';

		if ($order_info['payment_iso_code_2'] == 'BR')
		{
			$template .= '_direct';
		}
		else
		{
			$template .= '_checkout';
		}

		// Preload customer data (CPF and DOB)
		$this->load->model('customer/ebanx');
  	$info = $this->model_customer_ebanx->findByCustomerId($this->customer->getId());

  	$view['entry_tef_details']  = $this->language->get('entry_tef_details');

  	$view['ebanx_cpf'] = '';
		$view['ebanx_dob'] = '';

  	if ($info)
  	{
  		$view['ebanx_cpf'] = $info['cpf'];
  		$view['ebanx_dob'] = $info['dob'];
  	}

		// Render a custom template if it's available
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/' . $template . '.tpl'))
		{
			$template = $this->config->get('config_template') . '/template/payment/' . $template . '.tpl';
		}
		else
		{
			$template = 'default/template/payment/' . $template . '.tpl';
		}

		// Render either for OC1 or OC2
		if ($this->isOpencart2())
		{
			$this->response->setOutput($this->load->view($template, $view));
		}
		else
		{
			$this->template = $template;
			$this->data     = $view;
			$this->render();
		}
	}

	/**
	 * EBANX checkout action. Redirects to the EBANX URI.
	 * @return void
	 */
	public function checkout()
	{
		$view = array();
		$this->_setupEbanx();
		$this->load->model('checkout/order');
		$this->load->model('payment/ebanx');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$address = $order_info['payment_address_1'];
		if (!!$order_info['payment_address_2'])
		{
			$address .= ', ' . $order_info['payment_address_2'];
		}

		$params = array(
				'name' 					=> $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname']
			, 'email' 				=> $order_info['email']
			, 'amount' 				=> $order_info['total']
			, 'currency_code' => $order_info['currency_code']
			, 'address'			  => $address
			, 'zipcode' 		  => $order_info['payment_postcode']
			, 'country'  			=> strtolower($order_info['payment_iso_code_2'])
			, 'phone_number'  => $order_info['telephone']
			, 'order_number'  => $order_info['order_id']
			, 'payment_type_code' 		=> '_all'
			, 'merchant_payment_code' => $order_info['order_id']
		);

		\Ebanx\Config::setDirectMode(false);

		$response = \Ebanx\Ebanx::doRequest($params);

		if ($response->status == 'SUCCESS')
		{
			$this->_log('SUCCESS | Order: ' . $order_info['order_id'] . ', Hash: ' . $response->payment->hash);

			$this->load->model('payment/ebanx');
			$this->model_payment_ebanx->setPaymentHash($order_info['order_id'], $response->payment->hash);

			$this->load->model('checkout/order');

			$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('ebanx_order_status_op_id'));

			echo $response->redirect_url;
		}
		else
		{
			$this->_log('ERROR | Order: ' . $order_info['order_id'] . ', Error: ' . $response->status_message);
			echo $response->status_message;
		}
		exit;
	}

	/**
	 * EBANX direct checkout action. Redirects to the success URI.
	 * @return void
	 */
	public function checkoutDirect()
	{
		$view = array();
		$this->_setupEbanx();
		$this->load->model('checkout/order');
		$this->load->model('customer/ebanx');
		$this->load->model('payment/ebanx');

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

			// Save installments to total
			$this->model_payment_ebanx->updateTotalsWithInterest(array(
				  'order_id'       => $order_info['order_id']
				, 'total_text'     => $this->currency->format($params['payment']['amount_total'])
				, 'total_value'    => $params['payment']['amount_total']
				, 'interest_text'  => $this->currency->format($order_total - $order_info['total'])
				, 'interest_value' => $order_total - $order_info['total']
			));
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

    //if the method is TEF, specifying which bank was selected
    if ($this->request->post['ebanx']['method'] == 'tef')
    {
    	$params['payment']['payment_type_code'] = $this->request->post['ebanx_tef'];
    }


    // Persist the customer DOB and CPF
    $data = array(
  		  'cpf' => $params['payment']['document']
  		, 'dob' => $params['payment']['birth_date']
  	);


  	$id = $this->customer->getId();
    if ($this->model_customer_ebanx->findByCustomerId($id))
    {
    	// if customer doesn't exist, add it his details into the database
    	$this->model_customer_ebanx->update($id, $data);
    }
    else
    {
    	// if the customer data is available in the database, update his details
    	$this->model_customer_ebanx->insert($id, $data);
    }

    // Do the payment request
		$response = \Ebanx\Ebanx::doRequest($params);

		if ($response->status == 'SUCCESS')
		{
			$this->_log('SUCCESS | Order: ' . $order_info['order_id'] . ', Hash: ' . $response->payment->hash);

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
			elseif(isset($response->redirect_url))
			{
				echo $response->redirect_url;
			}
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
				$this->_log('ERROR | Order: ' . $order_info['order_id'] . ', Error: ' . $response->status_message);
				echo $response->status_message;
			}
			else
			{
				echo 'Unknown error, please contact the store owner.';
			}
		}
	}

	/**
	 * Shows the customer the boleto printing link
	 * @return void
	 */
	public function boleto()
	{
		$view = array();
		$this->_setupEbanx();

		$hash  = $this->request->get['hash'];
		$query = \Ebanx\Ebanx::doQuery(array('hash' => $hash));

		// Renders the default success page with the "Print Boleto" button
		if ($query->status == 'SUCCESS')
		{
			$this->language->load('checkout/success');

			$this->document->setTitle($this->language->get('heading_title'));

			$view['breadcrumbs'] = array();

			$view['breadcrumbs'][] = array(
				'href'      => $this->url->link('common/home'),
				'text'      => $this->language->get('text_home'),
				'separator' => false
			);

			$view['breadcrumbs'][] = array(
				'href'      => $this->url->link('checkout/cart'),
				'text'      => $this->language->get('text_basket'),
				'separator' => $this->language->get('text_separator')
			);

			$view['breadcrumbs'][] = array(
				'href'      => $this->url->link('checkout/checkout', '', 'SSL'),
				'text'      => $this->language->get('text_checkout'),
				'separator' => $this->language->get('text_separator')
			);

			$view['breadcrumbs'][] = array(
				'href'      => $this->url->link('checkout/success'),
				'text'      => $this->language->get('text_success'),
				'separator' => $this->language->get('text_separator')
			);

			$view['heading_title'] = $this->language->get('heading_title');

			if ($this->customer->isLogged())
			{
				$view['text_message'] = sprintf($this->language->get('text_customer'), $this->url->link('account/account', '', 'SSL'), $this->url->link('account/order', '', 'SSL'), $this->url->link('account/download', '', 'SSL'), $this->url->link('information/contact'));
			}
			else
			{
				$view['text_message'] = sprintf($this->language->get('text_guest'), $this->url->link('information/contact'));
			}

			$view['button_continue'] = $this->language->get('button_continue');
			$view['continue'] = $this->url->link('common/home');

			$view['boleto'] = $query->payment->boleto_url;

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/ebanx_boleto.tpl'))
			{
				$template = $this->config->get('config_template') . '/template/payment/ebanx_boleto.tpl';
			}
			else
			{
				$template = 'default/template/payment/ebanx_boleto.tpl';
			}

			// Empty the shopping cart
			$this->cart->clear();

			// Render either for OC1 or OC2
			if ($this->isOpencart2())
			{
				$view['header']         = $this->load->controller('common/header');
				$view['footer']         = $this->load->controller('common/footer');
				$view['column_left']    = $this->load->controller('common/column_left');
				$view['column_right']   = $this->load->controller('common/column_right');
				$view['content_bottom'] = $this->load->controller('common/content_bottom');
				$view['content_top']    = $this->load->controller('common/content_top');
				$this->response->setOutput($this->load->view($template, $view));
			}
			else
			{
				$this->template = $template;
				$this->children = array(
					  'common/column_left'
					, 'common/column_right'
					, 'common/content_top'
					, 'common/content_bottom'
					, 'common/footer'
					, 'common/header'
				);
				$this->data     = $view;
				$this->response->setOutput($this->render());
			}
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
		$view = array();
		$this->_setupEbanx();

		$this->language->load('payment/ebanx');

		$view['title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));

		$view['base'] = $this->config->get('config_url');
		if (isset($this->request->server['HTTPS']) && ($this->request->server['HTTPS'] == 'on'))
		{
			$view['base'] = $this->config->get('config_ssl');
		}

		// Setup translations
		$view['language'] 		 = $this->language->get('code');
		$view['direction'] 		 = $this->language->get('direction');
		$view['heading_title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
		$view['text_response'] = $this->language->get('text_response');
		$view['text_success']  = $this->language->get('text_success');
		$view['text_failure']  = $this->language->get('text_failure');
		$view['text_success_wait'] = sprintf($this->language->get('text_success_wait'), $this->url->link('checkout/success'));
		$view['text_failure_wait'] = sprintf($this->language->get('text_failure_wait'), $this->url->link('checkout/checkout', '', 'SSL'));

		$hash = isset($this->request->get['hash']) ? $this->request->get['hash'] : false;

		if ($hash && strlen($hash))
		{
			$response = \Ebanx\Ebanx::doQuery(array('hash' => $hash));

			// Update the order status, then redirect to the success page
			if (isset($response->status) && $response->status == 'SUCCESS' && ($response->payment->status == 'PE' || $response->payment->status == 'CO'))
			{
				$this->_log('CALLBACK SUCCESS | Order: ' . $this->session->data['order_id'] . ', Status: ' . $response->payment->status);

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
				// if the order fails
				$view['continue'] = $this->url->link('checkout/checkout');

				if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/ebanx_failure.tpl'))
				{
					$template = $this->config->get('config_template') . '/template/payment/ebanx_failure.tpl';
				}
				else
				{
					$template = 'default/template/payment/ebanx_failure.tpl';
				}
			}
		}
		else
		{
			$view['continue'] = $this->url->link('checkout/cart');

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/ebanx_failure.tpl'))
			{
				$template = $this->config->get('config_template') . '/template/payment/ebanx_failure.tpl';
			}
			else
			{
				$template = 'default/template/payment/ebanx_failure.tpl';
			}
		}

		// Render either for OC1 or OC2
		if ($this->isOpencart2())
		{
			$this->response->setOutput($this->load->view($template, $view));
		}
		else
		{
			$this->template = $template;
			$this->data     = $view;
			$this->response->setOutput($this->render());
		}
	}

	/**
	 * Notification action. It's called when a payment status is updated.
	 * @return void
	 */
	public function notify()
	{
		$view = array();
		$this->_setupEbanx();

		$hashes = $_REQUEST['hash_codes'];

		if ($hashes == null)
		{
			return;
		}

		$hashes = explode(',', $hashes);

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

				$this->_log('NOTIFY SUCCESS | Order: ' . $order_id . ', Status: ' . $response->payment->status);
				echo "OK: {$hash} changed to {$response->payment->status}\n";
			}
			else
			{
				echo "NOK: {$hash}\n";
			}
		}
	}
}
