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
  opacity: 1;
}

ul.ebanx-tef-info {
  list-style: none;
  overflow: hidden;
  padding-left: 0 !important;
}
ul.ebanx-tef-info li {
  float: left;
  margin: 0 10px 0 0;
  overflow: hidden;
}
ul.ebanx-tef-info li input {
  float: left;
  margin: 0 !important;
  display: none;
}
ul.ebanx-tef-info li label {
  float: left;
}
ul.ebanx-tef-info li label img {
  opacity: 1;
}
ul.ebanx-tef-info li label img:hover,
ul.ebanx-tef-info li label img.active {
  opacity: 0.5;
}



.ebanx-cc-info {
  display: none;
}

.ebanx-tef-info {
  display: none;
}

#ebanx-error {
  display: none;
}

.tef {
  display: none;
}
#button-confirm {
  cursor: pointer;
}
</style>


<div class="alert alert-danger" id="ebanx-error">
</div>

<div class="form-group required">
<form method="post" id="payment">
  <!--<?php //if ($enable_installments): ?>-->
    <h2><?php echo $entry_ebanx_details ?></h2>
    <div class="content" id="payment">
      <table class="form">
        <tbody>
          <tr>
            <td>CPF</td>
            <td><input type="text" size="14" name="ebanx[cpf]" id="ebanx_cpf" value="<?php echo $ebanx_cpf ?>" /></td>
          </tr>

          <tr>
            <td><?php echo $entry_dob ?></td>
            <td><input type="text" size="10" name="ebanx[dob]" id="ebanx_dob" value="<?php echo $ebanx_dob ?>" /></td>
          </tr>

          <tr class="ebanx-cc-info">
            <td><?php echo $entry_card_name ?></td>
            <td>
              <input type="text" name="ebanx[cc_name]" value="" size="20" />
            </td>
          </tr>

          <tr class="ebanx-cc-info">
            <td><?php echo $entry_card_number ?></td>
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
            <td><?php echo $entry_card_type ?></td>
            <td>
              <select id="ebanx_cc_type" name="ebanx[cc_type]" autocomplete="off">
                <option value="" selected="selected"><?php echo $entry_please_select ?></option>
                <option value="aura">Aura</option>
                <option value="amex">American Express</option>
                <option value="diners">Diners</option>
                <option value="discover">Discover</option>
                <option value="hipercard">Hipercard</option>
                <option value="elo">Elo</option>
                <option value="mastercard">MasterCard</option>
                <option value="visa">Visa</option>
              </select>
            </td>
          </tr>

          <tr class="ebanx-cc-info">
            <td><?php echo $entry_card_exp ?></td>
            <td>
              <select id="ebanx_cc_exp_month" name="ebanx[cc_exp][month]" autocomplete="off">
                <option value="" selected="selected"><?php echo $entry_month ?></option>
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
                  <option value="" selected="selected"><?php echo $entry_year ?></option>
                  <?php for ($i = date('Y'); $i < date('Y') + 15; $i++): ?>
                    <option value="<?php echo $i ?>"><?php echo $i ?></option>
                  <?php endfor ?>
                </select>
            </td>
          </tr>

          <tr class="ebanx-cc-info">
            <?php if ($enable_installments): ?>
              <td><?php echo $entry_installments_number ?></td>
              <td>
                <select name="ebanx[installments]" id="ebanx_installments_number">
                  <option value="1">1x de <?php echo $total_view ?></option>
                    <?php for ($i = 2; $i <= $max_installments; $i++): ?>
                      <option value="<?php echo $i ?>"><?php echo $i ?>x de <?php echo sprintf("%.2f", $order_total_interest / floatval($i)) ?></option>
                    <?php endfor ?>
              </select>
            </td>
            <?php endif ?>
          </tr>
        </tbody>
      </table>
    </div>
  <!--<?php //endif ?>-->

  <div class="buttons">
    <div class="right">
      <img src="image/ebanx/ebanx-checkout.png" id="button-confirm" />
    </div>
  </div>
</form>
</div>

<script>
    /**
     * Hack to show installments interest in the totals
     * @return {void}
     */
    var updateTotals = function() {
      var total    = '<?php echo $total_view ?>'
        , interest = '<?php echo $interest_view ?>'
        , totalWithInterest = '<?php echo $totalWithInterest ?>';

      if (interest.replace(/\D/, '') == '0') {
        return;
      }

      var installments = $('#ebanx_installments_number');

      if ($(installments && installments.val() > 1)) {
        if (!$('#ebanx-discount').length) {
          var interestHtml = '<tr id="ebanx-discount"><td colspan="4" class="price"><b><?php echo $entry_interest ?>:</b></td><td class="total">' + interest + '</td></tr>';
          $(interestHtml).insertBefore($('.checkout-product tfoot tr:last-child'));
          $('.checkout-product tfoot tr:last-child').children('td:last-child').html(totalWithInterest);
        }
      } else {
        $('#ebanx-discount').remove();
        $('.checkout-product tfoot tr:last-child').children('td:last-child').html(total);
      }
    };

    $('#ebanx_installments_number').change(updateTotals);

    /**
     * Shows an error message and focuses on the element with errors
     * @param  {string} message The error message
     * @param  {selector} elm   The selector of the element
     * @return {boolean}
     */
    var showError = function(message, elm) {
      $('#ebanx-error').text(message).show();

      if (elm) {
        elm.focus();
      }

      return false;
    };

    /**
     * Hides the error message and clears its text
     * @return {[type]} [description]
     */
    var hideError = function() {
      $('#ebanx-error').text('').hide();
    };

    /**
     * Validates the CPF number
     * @param  {string} cpf The CPF number
     * @return {boolean}
     */
    var validCpf = function(cpf) {
      // Allows only numbers, dots and dashes
      if (cpf.match(/[a-z]/gi)) {
        return false;
      }

      var digits = cpf.replace(/[\D]/g, '')
        , dv1, dv2, sum, mod;

      if (digits.length == 11) {
        d = digits.split('');

        sum = d[0] * 10 + d[1] * 9 + d[2] * 8 + d[3] * 7 + d[4] * 6 + d[5] * 5 + d[6] * 4 + d[7] * 3 + d[8] * 2;
        mod = sum % 11;
        dv1 = (11 - mod < 10 ? 11 - mod : 0);

        sum = d[0] * 11 + d[1] * 10 + d[2] * 9 + d[3] * 8 + d[4] * 7 + d[5] * 6 + d[6] * 5 + d[7] * 4 + d[8] * 3 + dv1 * 2;
        mod = sum % 11;
        dv2 = (11 - mod < 10 ? 11 - mod : 0);

        return dv1 == d[9] && dv2 == d[10];
      }

      return false;
    };

    /**
     * Validates the credit card number using the Luhn algorithm
     * @param  {string} value The credit card number
     * @return {boolean}
     */
    var validCreditCard = function(value) {
      // Non numeric characters are not allowed
      if (value.match(/\D/)) {
        return false;
      }

      value = value.replace(/\D/g, '');

      var nCheck = 0
        , nDigit = 0
        , bEven  = false;

      for (var n = value.length - 1; n >= 0; n--) {
        var cDigit = value.charAt(n)
          , nDigit = parseInt(cDigit, 10);

        if (bEven) {
          if ((nDigit *= 2) > 9) {
            nDigit -= 9;
          }
        }

        nCheck += nDigit;
        bEven  = !bEven;
      }

      return (nCheck % 10) == 0 && nCheck > 0;
    };

    /**
     * Validates the EBANX input fields
     * @return {boolean}
     */
    var validateEbanx = function() {
      hideError();

      var cpf = $('#ebanx_cpf')
        , dob = $('#ebanx_dob');

      if (!validCpf(cpf.val())) {
        return showError('CPF is invalid.', cpf);
      }

      if (dob.val().length != 10) {
        return showError('Date of Birth is invalid.', dob);
      }

      // If payment is via credit card, validate its fields
      //if ($('#ebanx_method_cc').is(':checked')) {
        var ccName   = $("input[name='ebanx[cc_name]']")
          , ccNumber = $("input[name='ebanx[cc_number]']")
          , ccCVV    = $("input[name='ebanx[cc_cvv]']")
          , ccType   = $("select[name='ebanx[cc_type]']")
          , ccExpMonth     = $("select[name='ebanx[cc_exp][month]']")
          , ccExpYear      = $("select[name='ebanx[cc_exp][year]']")
          , ccInstallments = $("select[name='ebanx[installments]']");

        if (ccName.val().length == 0) {
          return showError('Name on Card must not be empty.', ccName);
        }

        if (!validCreditCard(ccNumber.val())) {
          return showError('The credit card Number is not valid.', ccNumber)
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

        if (ccExpYear.val().length == 0 || ccExpYear.val() < 2014 || ccExpYear.val() > 2050) {
          return showError('The credit card expiration year is incorrect.', ccExpYear);
        }

      return true;
    };

    /**
     * Updates the credit card issuer depending on its number
     */
    $("input[name='ebanx[cc_number]']").on('input keydown change', function() {
      var ccNumber = $(this).val()
        , ccType   = $("select[name='ebanx[cc_type]']")
        , ccInstallments = $("select[name='ebanx[installments]']").closest('tr');

      function toggleType(type) {
        ccType.val(type);
      }

      ccInstallments.show();
      if (ccNumber.match(/^4[0-9]{12}(?:[0-9]{3})?$/)) {
        toggleType('visa');
      } else if (ccNumber.match(/^5[1-5][0-9]{14}$/)) {
        toggleType('mastercard');
      } else if (ccNumber.match(/^3[47][0-9]{13}$/)) {
        toggleType('amex');
      } else if (ccNumber.match(/^3(?:0[0-5]|[68][0-9])[0-9]{11}$/)) {
        toggleType('diners');
      } else if (ccNumber.match(/^6(?:011|5[0-9]{2})[0-9]{12}$/)) {
        toggleType('discover');
        // No installments for Discover
        ccInstallments.hide();
      } else if (ccNumber.match(/^(636368|438935|504175|451416|636297|5067|4576|4011)/)) {
        toggleType('elo');
      } else if (ccNumber.match(/^50[0-9]{14,17}$/)) {
        toggleType('aura');
      } else {
        toggleType('');
      }
    });

    /**
     * Binds the click event to the confirmation button. Applies validation to
     * input fields.
     * @param  {event} e
     * @return {void}
     */
    $('#button-confirm').bind('click', function(e) {
      e.preventDefault();

      if (validateEbanx() == false) {
        return;
      }

      $.ajax({
          url: 'index.php?route=payment/ebanx_express/checkoutDirect'
        , type: 'post'
        , data: $('#payment select, #payment input[type=text], #payment input[type=radio]:checked')
        , beforeSend: function() {
            $('#payment .warning').remove();
            $('#button-confirm').fadeToggle();
            $('#payment').before('<div class="attention"><img src="catalog/view/theme/default/image/loading.gif" alt="" /></div>');
          }
        , complete: function() {
            $('#button-confirm').fadeToggle();
            $('#payment').parent().find('.attention').remove();
          }
        , success: function(response) {
            // If the response is a URL, redirect to it
            if (response.match(/^http/)) {
              window.location = response;
            // Otherwise display an error message
            } else {
              $('.buttons').before('<div class="alert alert-danger warning">' + response + '</div>');
              $('#payment .attention').remove();
            }
          }
      });
    });

    $('#ebanx_dob').datetimepicker({
        format: 'DD/MM/YYYY'
      , changeMonth: true
      , changeYear: true
      , yearRange: '<?php echo date('Y') - 100 ?>:<?php echo date('Y') - 16 ?>'
    });

    /**
     * Show/hide credit card fields
     * @return {[type]} [description]
     */

    $('.ebanx-cc-info').show();
    updateTotals();

    /**
     * Toggles the payment method image active
     * @return {void}
     */
    $('ul.payment-methods li img').click(function() {
      var self = $(this);

      $('ul.payment-methods li img').removeClass('active');
      self.addClass('active');
    });

    $('ul.ebanx-tef-info li img').click(function() {
      var self = $(this);

      $('ul.ebanx-tef-info li img').removeClass('active');
      self.addClass('active');
    });


     /**
     * Hides the installments field for Discover cards
     * @return {void}
     */
    $('#ebanx_cc_type').change(function() {
      var installments = $('#ebanx_installments_number').closest('tr');

      if ($(this).val() == 'discover') {
        installments.hide();
      } else {
        installments.show();
      }
    });
</script>