<style>
ul.payment-methods {
  list-style: none;
  overflow: hidden;
  padding-left: 0 !important;
}
ul.payment-methods li {
  float: left;
  margin: 0 10px 0 0;
  overflow: hidden;
}
ul.payment-methods li input {
  float: left;
  margin: 0 !important;
  display: none;
}
ul.payment-methods li label {
  float: left;
}
ul.payment-methods li label img {
  opacity: 0.5;
}
ul.payment-methods li label img:hover,
ul.payment-methods li label img.active {
  opacity: 1.0;
}
.ebanx-cc-info {
  display: none;
}
#ebanx-error {
  display: none;
}
</style>

<script>
    var showError = function(message, elm) {
      $('#ebanx-error').text(message).show();
      elm.focus();
      return false;
    };

    var hideError = function() {
      $('#ebanx-error').text('').hide();
    };

    var validCpf = function(cpf) {
      return true;
    };

    var validateEbanx = function() {
      hideError();

      var cpf = $('#ebanx_cpf')
        , dob = $('#ebanx_dob');

      if (cpf.val().length < 11 || !validCpf(cpf.val())) {
        return showError('CPF is invalid.', cpf);
      }

      if (dob.val().length != 10) {
        return showError('Date of birth is invalid.', dob);
      }

      // If payment is via credit card, validate its fields
      if ($('#ebanx_method_cc').is(':checked')) {
        var ccName   = $("input[name='ebanx[cc_name]']")
          , ccNumber = $("input[name='ebanx[cc_number]'']")
          , ccCVV    = $("input[name='ebanx[cc_cvv]']")
          , ccType   = $("input[name='ebanx[cc_type]']")
          , ccExpMonth     = $("input[name='ebanx[cc_exp][month]']")
          , ccExpYear      = $("input[name='ebanx[cc_exp][year]'']")
          , ccInstallments = $("input[name='ebanx[installments]']");

        if (ccName.val().length == 0) {
          return showError('Name on Card must not be empty.', ccName);
        }

        if (ccNumber.length < 12 || ccNumber.length > 19) {
          return showError('Credit Card Number is incorrect.')
        }

        if (ccCVV.val().length < 3 || ccCVV.val().length > 4) {
          return showError('CVV is incorrect.', ccCVV);
        }

        if (ccType.val().length == 0) {
          return showError('You must select a credit card.', ccType);
        }

        if (ccExpMonth.val().length == 0 || ccExpMonth.val() < 1 || ccExpMonth.val() > 12) {
          return showError('The credit card expiration month is incorrect.', ccExpMonth);
        }

        if (ccExpYear.val().length == 0 || ccExpYear.val() < 1910 || ccExpYear.val() > 2010) {
          return showError('The credit card expiration year is incorrect.', ccExpYear);
        }

        // No installments for Discover cards
        if (ccType.val() != 'discover') {
          var installments = parseInt(ccInstallments.val());

          if (installments.length == 0 || installments < 1 || installments > 6) {
            return showError('The number of installments is incorrect.');
          }
        }
      }

      return true;
    };

    $('#button-confirm').bind('click', function(e) {
      if (validateEbanx() == false) {
        e.preventDefault();
        return;
      }

      $.ajax({
        url: 'index.php?route=payment/ebanx/checkoutDirect',
        type: 'post',
        data: $('#payment select'),
        beforeSend: function() {
          $('#button-confirm').attr('disabled', true);
          $('#payment').before('<div class="attention"><img src="catalog/view/theme/default/image/loading.gif" alt="" /> <?php echo $text_wait; ?></div>');
        },
        complete: function() {
          $('#button-confirm').attr('disabled', false);
          $('.attention').remove();
        },
        success: function(response) {
          if (response['error']) {
            alert(response['error']);
          } else {
            window.location = response;
          }
        }
      });
    });

    $('#ebanx_dob').datepicker({
        dateFormat: 'dd/mm/yy'
      , changeMonth: true
      , changeYear: true
      , yearRange: 'c-100:c-16'
    });

    $('#ebanx_method_boleto').click(function() {
      $('.ebanx-cc-info').hide();
    });

    $('#ebanx_method_cc').click(function() {
      $('.ebanx-cc-info').show();
    });

    $('ul.payment-methods li img').click(function() {
      var self = $(this);

      $('ul.payment-methods li img').removeClass('active');
      self.addClass('active');
    })

    $('#ebanx_cc_type').change(function() {
      var installments = $('#ebanx_installments_number').closest('tr');

      if ($(this).val() == 'discover') {
        installments.hide();
      } else {
        installments.show();
      }
    });
</script>

<div class="warning" id="ebanx-error">
</div>

<form method="post" id="payment">
  <?php if ($enable_installments): ?>
    <h2>Ebanx Details</h2>
    <div class="content" id="payment">
      <table class="form">
        <tbody>
          <tr>
            <td>CPF</td>
            <td><input type="text" size="14" name="ebanx[cpf]" id="ebanx_cpf" /></td>
          </tr>

          <tr>
            <td>Date of Birth</td>
            <td><input type="text" size="10" name="ebanx[dob]" id="ebanx_dob" /></td>
          </tr>

          <tr>
            <td>Payment Method</td>
            <td>
              <ul class="payment-methods">
                <?php if ($ebanx_direct_cards == 1): ?>
                <li>
                  <input type="radio" name="ebanx[method]" value="creditcard" id="ebanx_method_cc" />
                  <label for="ebanx_method_cc"><img src="image/ebanx/ebanx-creditcards.png" width="264" height="63"></label>
                </li>
                <? endif ?>

                <li>
                  <input type="radio" name="ebanx[method]" value="boleto" id="ebanx_method_boleto" checked="checked" />
                  <label for="ebanx_method_boleto"><img src="image/ebanx/ebanx-boleto.png" width="264" height="63"  class="active"></label>
                </li>
              </ul>
            </td>
          </tr>

          <tr class="ebanx-cc-info">
            <td>Name on Card</td>
            <td>
              <input type="text" name="ebanx[cc_name]" value="" size="20" />
            </td>
          </tr>

          <tr class="ebanx-cc-info">
            <td>Credit Card Number</td>
            <td>
              <input type="text" name="ebanx[cc_number]" value="" size="20" />
            </td>
          </tr>

          <tr class="ebanx-cc-info">
            <td>CVV</td>
            <td>
              <input type="text" name="ebanx[cc_cvv]" value="" size="4" />
            </td>
          </tr>

          <tr class="ebanx-cc-info">
            <td>Credit Card Type</td>
            <td>
              <select id="ebanx_cc_type" name="ebanx[cc_type]" autocomplete="off">
                <option value="" selected="selected">Please select</option>
                <option value="aura">Aura</option>
                <option value="amex">American Express</option>
                <option value="diners">Diners</option>
                <option value="discover">Discover</option>
                <option value="elo">Elo</option>
                <option value="mastercard">MasterCard</option>
                <option value="visa">Visa</option>
              </select>
            </td>
          </tr>

          <tr class="ebanx-cc-info">
            <td>Expiration Date</td>
            <td>
              <select id="ebanx_cc_exp_month" name="ebanx[cc_exp][month]" autocomplete="off">
                <option value="" selected="selected">Month</option>
                <option value="1">01 - January</option>
                <option value="2">02 - February</option>
                <option value="3">03 - March</option>
                <option value="4">04 - April</option>
                <option value="5">05 - May</option>
                <option value="6">06 - June</option>
                <option value="7">07 - July</option>
                <option value="8">08 - August</option>
                <option value="9">09 - September</option>
                <option value="10">10 - October</option>
                <option value="11">11 - November</option>
                <option value="12">12 - December</option>
              </select>

              <select id="ebanx_cc_exp_year" name="ebanx[cc_exp][year]" autocomplete="off">
                <option value="" selected="selected">Year</option>
                <?php for ($i = date('Y'); $i < date('Y') + 15; $i++): ?>
                  <option value="<?php echo $i ?>"><?php echo $i ?></option>
                <?php endfor ?>
              </select>
            </td>
          </tr>

          <tr class="ebanx-cc-info">
            <td><?php echo $entry_installments_number ?></td>
            <td>
              <select name="ebanx[installments]" id="ebanx_installments_number">
                <option value="1">1x de <?php echo $this->currency->format($order_total) ?></option>

                <?php for ($i = 2; $i <= $max_installments; $i++): ?>
                  <option value="<?php echo $i ?>"><?php echo $i ?>x de <?php echo $this->currency->format($order_total_interest / floatval($i)) ?></option>
                <?php endfor ?>
              </select>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  <? endif ?>

  <div class="buttons">
    <div class="right">
      <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="button" />
    </div>
  </div>
</form>

