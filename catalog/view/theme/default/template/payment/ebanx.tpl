<style>
#ebanx_installments_cards {
  display: none;
}
</style>

<script>
    /**
     * Hack to show installments interest in the totals
     * @return {void}
     */
    var updateTotals = function() {
      var total    = '<?php echo $this->currency->format($order_total) ?>'
        , interest = '<?php echo $this->currency->format($order_total_interest - $order_total) ?>'
        , totalWithInterest = '<?php echo $this->currency->format($order_total_interest) ?>';

      if (interest.replace(/\D/, '') == '0') {
        return;
      }

      var installments = $('#ebanx_installments_number');

      if (installments && installments.val() > 1) {
        if (!$('#ebanx-discount').length) {
          var interestHtml = '<tr id="ebanx-discount"><td colspan="4" class="price"><b>Interest:</b></td><td class="total">' + interest + '</td></tr>';
          $(interestHtml).insertBefore($('.checkout-product tfoot tr:last-child'));
          $('.checkout-product tfoot tr:last-child').children('td:last-child').html(totalWithInterest);
        }
      } else {
        $('#ebanx-discount').remove();
        $('.checkout-product tfoot tr:last-child').children('td:last-child').html(total);
      }
    };

    var ebanxInstallmentsCards = document.getElementById('ebanx_installments_cards')
      , installmentsNumber = document.getElementById('ebanx_installments_number')
      , installmentsCard   = document.getElementById('ebanx_installments_card');

    function toggleInstallmentsCards() {
        if (installmentsNumber.value == 1) {
            ebanxInstallmentsCards.style.display = 'none';
        } else {
            ebanxInstallmentsCards.style.display = 'table-row';
        }
    }

    $('#ebanx_installments_number').change(function() {
      updateTotals();
      toggleInstallmentsCards();
    });

    $('#button-confirm').bind('click', function(e) {
      e.preventDefault(); // Prevent default event (posts the form)

      $.ajax({
        url: 'index.php?route=payment/ebanx/checkout',
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
</script>

<form method="post" id="payment">
  <?php if ($enable_installments): ?>
    <h2>Ebanx Details </h2>
    <div class="content" id="payment">
      <table class="form">
        <tbody>
          <tr>
            <td><?php echo $entry_installments_number ?></td>
            <td>
              <select name="instalments" id="ebanx_installments_number">
                <option value="1">1x de <?php echo $this->currency->format($order_total) ?></option>

                <?php for ($i = 2; $i <= $max_installments; $i++): ?>
                  <option value="<?php echo $i ?>"><?php echo $i ?>x de <?php echo $this->currency->format($order_total_interest / floatval($i)) ?></option>
                <?php endfor ?>
              </select>
            </td>
          </tr>
          <tr id="ebanx_installments_cards">
            <td><?php echo $entry_installments_cc ?></td>
            <td>
              <select name="payment_type_code" id="ebanx_installments_card">
                <option value="visa">Visa</option>
                <option value="mastercard">Mastercard</option>
              </select>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  <?php endif ?>

  <div class="buttons">
    <div class="right">
      <input type="submit" value="<?php echo $button_confirm; ?>" id="button-confirm" class="button" />
    </div>
  </div>
</form>