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
 * Model for the customer_ebanx table
 */
class ModelCustomerEbanx extends Model
{
  public function findByCustomerId($id)
  {
    $sql = "SELECT cpf, dob FROM " . DB_PREFIX . "customer_ebanx WHERE customer_id = " . $id;
    $query = $this->db->query($sql);

    if ($query->num_rows > 0)
    {
      return $query->row;
    }

    return false;
  }

  /**
   * Add customer data (CPF and DOB)
   * @param  array $data
   * @return void
   */
  public function insert($data)
  {
    $sql  = "INSERT INTO " . DB_PREFIX . "customer_ebanx (customer_id, cpf, dob) VALUES (";
    $sql .= $data['customer_id'] . ',' . $data['cpf'] . ',' . $data['dob'] . ')';
    $this->db->query($sql);
  }

  public function update($id, $data)
  {
    $sql  = "UPDATE " . DB_PREFIX . "customer_ebanx ";
    $sql .= "SET cpf = '" . $data['cpf'] . "', dob = '" . $data['dob'] . "' ";
    $sql .= "WHERE customer_id = " . $id ";";
    $this->db->query($sql);
  }
}