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

/**
 * Model for the order_ebanx table
 */
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
        , 'terms'      => ''
				, 'sort_order' => $this->config->get('ebanx_sort_order')
      );
    }

    return $method_data;
  }

  /**
   * Add an order hash to the EBANX table
   * @param int    $orderId
   * @param string $paymentHash
   * @return  void
   */
	public function setPaymentHash($orderId, $paymentHash)
  {
		$this->db->query("INSERT INTO " . DB_PREFIX . "order_ebanx VALUES($orderId, '$paymentHash')");
	}

  public function updateTotalsWithInterest($data)
  {
    $sql = "UPDATE `" . DB_PREFIX . "order_total` SET `text` = '" . $data['total_text'] . "', `value` = '" . $data['total_value'] . "'
            WHERE `order_id` = '" . $data['order_id'] . "' AND `code` = 'total'";
    $this->db->query($sql);

    // Insert interest rate if it was not inserted yet
    $sql = "SELECT order_total_id FROM `" . DB_PREFIX . "order_total` WHERE `order_id` = '" . $data['order_id'] . "' AND code = 'ebanx_interest'";
    $result = $this->db->query($sql);

    if ($result->num_rows == 0)
    {
      $sql = "INSERT INTO `" . DB_PREFIX . "order_total` (`order_id`, `code`, `title`, `text`, `value`, `sort_order`)
              VALUES ('" . $data['order_id'] . "', 'ebanx_interest', 'Interest', '" . $data['interest_text'] . "', '" . $data['interest_value'] . "', '8')";
      $this->db->query($sql);
    }

    $sql = "UPDATE `" . DB_PREFIX . "order` SET `total` = '" . $data['total_value']. "' WHERE `order_id` = " . $data['order_id'];
    $this->db->query($sql);
  }
}