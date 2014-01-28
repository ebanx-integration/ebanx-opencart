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
 * Installation model
 */
class ModelPaymentEbanx extends Model
{
    /**
     * Install the EBANX table
     * @return void
     */
	public function install()
    {
        // Create table to store orders EBANX hash
		$this->db->query("
            CREATE TABLE `" . DB_PREFIX . "order_ebanx` (
                `order_id` int(11) NOT NULL,
                `ebanx_hash` varchar(255) NOT NULL,
                PRIMARY KEY `order_id` (`order_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
        ");

        // Create table to store customer data (CPF, DOB)
        $this->db->query("
            CREATE TABLE `" . DB_PREFIX . "customer_ebanx` (
                `customer_id` int(11)     NOT NULL,
                `cpf`         varchar(32) NOT NULL,
                `dob`         varchar(16) NOT NULL,
                PRIMARY KEY `customer_id` (`customer_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
        ");
	}

    /**
     * Uninstall the EBANX table
     * @return void
     */
	public function uninstall()
    {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "order_ebanx`;");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "customer_ebanx`;");
	}

    public function updateMethodCards($value)
    {
        $this->_updateSetting('ebanx_direct_cards', $value);
    }

    public function updateMethodTef($value)
    {
        $this->_updateSetting('ebanx_direct_tef', $value);
    }

    public function updateMethodBoleto($value)
    {
        $this->_updateSetting('ebanx_direct_boleto', $value);
    }

    public function updateSettings($arr)
    {
        foreach ($arr as $key => $value)
        {
            $this->_updateSetting($key, $value);
        }
    }

    protected function _updateSetting($key, $value)
    {
        $sql = "DELETE FROM `" . DB_PREFIX . "setting` WHERE `key` = '$key'";
        $this->db->query($sql);

        $sql = "INSERT INTO `" . DB_PREFIX . "setting` (
                  `group`, `key`, `value`
                ) VALUES (
                  'ebanx', '$key', '$value'
                )";

        $this->db->query($sql);

    }
}