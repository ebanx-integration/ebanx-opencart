<?php
class ModelTotalEbanxInterest extends Model {
  public function getTotal(&$total_data, &$total, &$taxes) {
    $this->language->load('total/total');

    $total_data[] = array(
      'code'       => 'ebanx_interest',
      'title'      => 'Interest',
      'text'       => $this->currency->format(max(0, $total)),
      'value'      => max(0, $total),
      'sort_order' => 8
    );
  }
}
?>