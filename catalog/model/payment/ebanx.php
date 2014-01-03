<?php

class ModelPaymentEbanx extends Model
{
 	public function getMethod($address, $total)
  {
		$this->language->load('payment/ebanx');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('ebanx_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if (!$this->config->get('ebanx_geo_zone_id') || $query->num_rows)
    {
			$status = true;
		}
    else
    {
			$status = false;
		}

		$method_data = array();

		if ($status)
    {
      $method_data = array(
          'code'       => 'ebanx'
        ,	'title'      => $this->language->get('text_title')
				, 'sort_order' => $this->config->get('ebanx_sort_order')
      );
    }

    return $method_data;
  }

	public function setPaymentHash($orderId, $paymentHash)
  {
		$this->db->query("INSERT INTO " . DB_PREFIX . "order_ebanx VALUES($orderId, '$paymentHash')");
	}
}