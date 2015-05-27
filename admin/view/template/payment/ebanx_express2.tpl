<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-ebanx-express2" class="btn btn-primary"><i class="fa fa-save"></i> <?php echo $button_save; ?></button>
        <a href="<?php echo $cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i> <?php echo $button_cancel; ?></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i>Edit EBANX Express</h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-ebanx-express2" class="form-horizontal">
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-ebanx-express-merchant-key"><?php echo $entry_merchant_key; ?></label>
            <div class="col-sm-10">
              <input type="text" name="ebanx_express_merchant_key" value="<?php echo $ebanx_express_merchant_key; ?>" placeholder="<?php echo $ebanx_express_merchant_key; ?>" id="input-account-name" class="form-control" />
              <?php if ($error_merchant_key) { ?>
              <div class="text-danger"><?php echo $error_merchant_key; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-test"><?php echo $entry_test; ?></label>
            <div class="col-sm-10">
              <select name="ebanx_express_mode" id="input-test" class="form-control">
                <?php if ($ebanx_express_mode == 'pay'): ?>
                  <option value="pay" selected="selected"><?php echo $text_pay_mode ?></option>
                <?php else: ?>
                  <option value="pay"><?php echo $text_pay_mode ?></option>
                <?php endif ?>

                <?php if ($ebanx_express_mode == 'test'): ?>
                  <option value="test" selected="selected"><?php echo $text_test_mode ?></option>
                <?php else: ?>
                  <option value="test"><?php echo $text_test_mode ?></option>
                <?php endif ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-order-status-ca"><?php echo $entry_order_status_ca; ?></span></label>
            <div class="col-sm-10">
              <select name="ebanx_express_order_status_ca_id" id="input-order-status-ca" class="form-control">
                <?php foreach ($order_statuses as $order_status): ?>
                  <?php if ($order_status['order_status_id'] == $ebanx_express_order_status_ca_id): ?>
                    <option value="<?php echo $order_status['order_status_id'] ?>" selected="selected"><?php echo $order_status['name'] ?></option>
                  <?php else: ?>
                    <option value="<?php echo $order_status['order_status_id'] ?>"><?php echo $order_status['name'] ?></option>
                  <?php endif ?>
                <?php endforeach ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-order-status-co"><?php echo $entry_order_status_co; ?></span></label>
            <div class="col-sm-10">
              <select name="ebanx_express_order_status_co_id" id="input-order-status-co" class="form-control">
                <?php foreach ($order_statuses as $order_status): ?>
                  <?php if ($order_status['order_status_id'] == $ebanx_express_order_status_co_id): ?>
                    <option value="<?php echo $order_status['order_status_id'] ?>" selected="selected"><?php echo $order_status['name'] ?></option>
                  <?php else: ?>
                    <option value="<?php echo $order_status['order_status_id'] ?>"><?php echo $order_status['name'] ?></option>
                  <?php endif ?>
                <?php endforeach ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-order-status-pe"><?php echo $entry_order_status_pe; ?></span></label>
            <div class="col-sm-10">
              <select name="ebanx_express_order_status_pe_id" id="input-order-status-pe" class="form-control">
                <?php foreach ($order_statuses as $order_status): ?>
                  <?php if ($order_status['order_status_id'] == $ebanx_express_order_status_pe_id): ?>
                    <option value="<?php echo $order_status['order_status_id'] ?>" selected="selected"><?php echo $order_status['name'] ?></option>
                  <?php else: ?>
                    <option value="<?php echo $order_status['order_status_id'] ?>"><?php echo $order_status['name'] ?></option>
                  <?php endif ?>
                <?php endforeach ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-order-status-op"><?php echo $entry_order_status_op; ?></span></label>
            <div class="col-sm-10">
              <select name="ebanx_express_order_status_op_id" id="input-order-status-op" class="form-control">
                <?php foreach ($order_statuses as $order_status): ?>
                  <?php if ($order_status['order_status_id'] == $ebanx_express_order_status_op_id): ?>
                    <option value="<?php echo $order_status['order_status_id'] ?>" selected="selected"><?php echo $order_status['name'] ?></option>
                  <?php else: ?>
                    <option value="<?php echo $order_status['order_status_id'] ?>"><?php echo $order_status['name'] ?></option>
                  <?php endif ?>
                <?php endforeach ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-order-status-refund"><?php echo $entry_order_status_refund; ?></span></label>
            <div class="col-sm-10">
              <select name="ebanx_express_order_status_refund_id" id="input-order-status-refund" class="form-control">
                <?php foreach ($order_statuses as $order_status): ?>
                  <?php if ($order_status['order_status_id'] == $ebanx_express_order_status_refund_id): ?>
                    <option value="<?php echo $order_status['order_status_id'] ?>" selected="selected"><?php echo $order_status['name'] ?></option>
                  <?php else: ?>
                    <option value="<?php echo $order_status['order_status_id'] ?>"><?php echo $order_status['name'] ?></option>
                  <?php endif ?>
                <?php endforeach ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-order-status-chargeback"><?php echo $entry_order_status_chargeback; ?></span></label>
            <div class="col-sm-10">
              <select name="ebanx_express_order_status_chargeback_id" id="input-order-status-chargeback" class="form-control">
                <?php foreach ($order_statuses as $order_status): ?>
                  <?php if ($order_status['order_status_id'] == $ebanx_express_order_status_chargeback_id): ?>
                    <option value="<?php echo $order_status['order_status_id'] ?>" selected="selected"><?php echo $order_status['name'] ?></option>
                  <?php else: ?>
                    <option value="<?php echo $order_status['order_status_id'] ?>"><?php echo $order_status['name'] ?></option>
                  <?php endif ?>
                <?php endforeach ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-geo-zone"><?php echo $entry_geo_zone; ?></label>
            <div class="col-sm-10">
              <select name="ebanx_express_geo_zone_id" id="input-geo-zone" class="form-control">
                <option value="0"><?php echo $text_all_zones; ?></option>
                <?php foreach ($geo_zones as $geo_zone) { ?>
                <?php if ($geo_zone['geo_zone_id'] == $ebanx_express_geo_zone_id) { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-ebanx-express-installments"><?php echo $entry_enable_installments; ?></label>
            <div class="col-sm-10">
              <select name="ebanx_express_max_installments">
                <?php if ($ebanx_express_enable_installments): ?>
                  <option value="1" selected="selected"><?php echo $text_enabled ?></option>
                  <option value="0"><?php echo $text_disabled ?></option>
                <?php else: ?>
                  <option value="1"><?php echo $text_enabled ?></option>
                  <option value="0" selected="selected"><?php echo $text_disabled ?></option>
                <?php endif ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-ebanx-express-max-installments"><?php echo $entry_max_installments; ?></label>
            <div class="col-sm-10">
              
              <select name="ebanx_express_max_installments">
                <?php for ($i = 1; $i <= 12; $i++): ?>
                  <option value="<?php echo $i ?>" <?php if ($ebanx_express_max_installments == $i) echo 'selected="selected"' ?>><?php echo $i ?></option>
                <?php endfor ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-ebanx-express-installments-interest"><?php echo $entry_installments_interest; ?></label>
            <div class="col-sm-10">
              <input type="text" name="ebanx_express_installments_interest" value="<?php echo floatval($ebanx_express_installments_interest) ?>" /></td>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
            <div class="col-sm-10">
              <input type="text" name="ebanx_express_sort_order" value="<?php echo $ebanx_express_sort_order ?>" size="1" /></td>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="logs">Logs</label>
            <div class="col-sm-10">
            <a href="<?php echo $view_log ?>&token=<?php echo $_SESSION['token'] ?>" target="_blank">View log</a>
            <a href="<?php echo $clear_log ?>&token=<?php echo $_SESSION['token'] ?>" target="_blank">Clear log</a>
            </div>
          </div>



        </form>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  /**
   * Updates the direct mode payment methods via AJAX and reloads the page
   * @return {void}
   */
  $('#update-payment-methods').click(function() {
    $('html, body').css('cursor', 'wait');
    $.get('<?php echo $ebanx_express_update_payments ?>', function(r) {
      alert(r);
      window.location.reload();
    });
  });

  /**
   * Remove non numeric characters from interest rate
   */
  $('input[name=ebanx_express_installments_interest]').on('change keyup keydown', function(e) {
    var self  = $(this)
      , input =  $(this).val()
      , newInput = input.replace(/[^\d.]/g, '');

    if (input.length == newInput.length) {
      return;
    }

    self.val(newInput);
  });
});
</script>


<?php echo $footer; ?> 