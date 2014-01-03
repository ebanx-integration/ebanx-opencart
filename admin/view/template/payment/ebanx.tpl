<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb): ?>
      <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href'] ?>"><?php echo $breadcrumb['text'] ?></a>
    <?php endforeach ?>
  </div>

  <?php if ($error_warning): ?>
    <div class="warning"><?php echo $error_warning ?></div>
  <?php endif ?>

  <div class="box">
    <div class="heading">
      <h1><img src="view/image/payment.png" alt="" /> <?php echo $heading_title ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save ?></a><a href="<?php echo $cancel ?>" class="button"><?php echo $button_cancel ?></a></div>
    </div>

    <div class="content">
      <form action="<?php echo $action ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
          <tr>
            <td><span class="required">*</span> <?php echo $entry_merchant_key ?></td>
            <td><input type="text" name="ebanx_merchant_key" value="<?php echo $ebanx_merchant_key ?>" size="110" />
              <?php if ($error_merchant_key): ?>
              <span class="error"><?php echo $error_merchant_key ?></span>
              <?php endif ?></td>
          </tr>

          <tr>
            <td><?php echo $entry_test ?></td>
            <td><select name="ebanx_mode">
                <?php if ($ebanx_mode == 'pay'): ?>
                  <option value="pay" selected="selected"><?php echo $text_pay_mode ?></option>
                <?php else: ?>
                  <option value="pay"><?php echo $text_pay_mode ?></option>
                <?php endif ?>

                <?php if ($ebanx_mode == 'test'): ?>
                  <option value="test" selected="selected"><?php echo $text_test_mode ?></option>
                <?php else: ?>
                  <option value="test"><?php echo $text_test_mode ?></option>
                <?php endif ?>
              </select>
            </td>
          </tr>

          <tr>
            <td><?php echo $entry_order_status_ca ?></td>
            <td><select name="ebanx_order_status_ca_id">
                <?php foreach ($order_statuses as $order_status): ?>
                  <?php if ($order_status['order_status_id'] == $ebanx_order_status_ca_id): ?>
                    <option value="<?php echo $order_status['order_status_id'] ?>" selected="selected"><?php echo $order_status['name'] ?></option>
                  <?php else: ?>
                    <option value="<?php echo $order_status['order_status_id'] ?>"><?php echo $order_status['name'] ?></option>
                  <?php endif ?>
                <?php endforeach ?>
              </select>
            </td>
          </tr>

          <tr>
            <td><?php echo $entry_order_status_co ?></td>
            <td><select name="ebanx_order_status_co_id">
                <?php foreach ($order_statuses as $order_status): ?>
                  <?php if ($order_status['order_status_id'] == $ebanx_order_status_co_id): ?>
                    <option value="<?php echo $order_status['order_status_id'] ?>" selected="selected"><?php echo $order_status['name'] ?></option>
                  <?php else: ?>
                    <option value="<?php echo $order_status['order_status_id'] ?>"><?php echo $order_status['name'] ?></option>
                  <?php endif ?>
                <?php endforeach ?>
              </select></td>
          </tr>

          <tr>
            <td><?php echo $entry_order_status_op ?></td>
            <td><select name="ebanx_order_status_op_id">
                <?php foreach ($order_statuses as $order_status): ?>
                  <?php if ($order_status['order_status_id'] == $ebanx_order_status_op_id): ?>
                    <option value="<?php echo $order_status['order_status_id'] ?>" selected="selected"><?php echo $order_status['name'] ?></option>
                  <?php else: ?>
                    <option value="<?php echo $order_status['order_status_id'] ?>"><?php echo $order_status['name'] ?></option>
                  <?php endif ?>
                <?php endforeach ?>
              </select></td>
          </tr>

          <tr>
            <td><?php echo $entry_order_status_pe ?></td>
            <td><select name="ebanx_order_status_pe_id">
                <?php foreach ($order_statuses as $order_status): ?>
                  <?php if ($order_status['order_status_id'] == $ebanx_order_status_pe_id): ?>
                    <option value="<?php echo $order_status['order_status_id'] ?>" selected="selected"><?php echo $order_status['name'] ?></option>
                  <?php else: ?>
                    <option value="<?php echo $order_status['order_status_id'] ?>"><?php echo $order_status['name'] ?></option>
                  <?php endif ?>
                <?php endforeach ?>
              </select>
            </td>
          </tr>

          <tr>
            <td><?php echo $entry_geo_zone ?></td>
            <td><select name="ebanx_geo_zone_id">
                <option value="0"><?php echo $text_all_zones ?></option>
                <?php foreach ($geo_zones as $geo_zone): ?>
                  <?php if ($geo_zone['geo_zone_id'] == $ebanx_geo_zone_id): ?>
                    <option value="<?php echo $geo_zone['geo_zone_id'] ?>" selected="selected"><?php echo $geo_zone['name'] ?></option>
                  <?php else: ?>
                    <option value="<?php echo $geo_zone['geo_zone_id'] ?>"><?php echo $geo_zone['name'] ?></option>
                  <?php endif ?>
                <?php endforeach ?>
              </select>
            </td>
          </tr>

          <tr>
            <td><?php echo $entry_enable_installments ?></td>
            <td><select name="ebanx_enable_installments">
                <?php if ($ebanx_enable_installments): ?>
                  <option value="1" selected="selected"><?php echo $text_enabled ?></option>
                  <option value="0"><?php echo $text_disabled ?></option>
                <?php else: ?>
                  <option value="1"><?php echo $text_enabled ?></option>
                  <option value="0" selected="selected"><?php echo $text_disabled ?></option>
                <?php endif ?>
              </select>
            </td>
          </tr>

          <tr>
            <td><?php echo $entry_max_installments ?></td>
            <td>
              <select name="ebanx_max_installments">
                <?php for ($i = 1; $i <= 6; $i++): ?>
                  <option value="<?php echo $i ?>" <?php if ($ebanx_max_installments == $i) echo 'selected="selected"' ?>><?php echo $i ?></option>
                <? endfor ?>
              </select>
            </td>
          </tr>

          <tr>
            <td><?php echo $entry_installments_interest ?></td>
            <td><input type="text" name="ebanx_installments_interest" value="<?php echo floatval($ebanx_installments_interest) ?>" /></td>
          </tr>

          <tr>
            <td><?php echo $entry_status ?></td>
            <td><select name="ebanx_status">
                <?php if ($ebanx_status): ?>
                  <option value="1" selected="selected"><?php echo $text_enabled ?></option>
                  <option value="0"><?php echo $text_disabled ?></option>
                <?php else: ?>
                  <option value="1"><?php echo $text_enabled ?></option>
                  <option value="0" selected="selected"><?php echo $text_disabled ?></option>
                <?php endif ?>
              </select>
            </td>
          </tr>

          <tr>
            <td><?php echo $entry_sort_order ?></td>
            <td><input type="text" name="ebanx_sort_order" value="<?php echo $ebanx_sort_order ?>" size="1" /></td>
          </tr>
        </table>
      </form>
    </div>
  </div>
</div>
<?php echo $footer ?>