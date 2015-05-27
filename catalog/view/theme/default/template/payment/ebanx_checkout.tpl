<style>
#button-confirm {
  cursor: pointer;
}
</style>

<script>
    /**
     * Binds the click event to the confirmation button. Applies validation to
     * input fields.
     * @param  {event} e
     * @return {void}
     */
    $('#button-confirm').bind('click', function(e) {
      e.preventDefault();

      $.ajax({
          url: 'index.php?route=payment/ebanx/checkout'
        , type: 'post'
        , beforeSend: function() {
            $('.payment > .warning').remove();
            $('#button-confirm').fadeToggle();
            $('#payment').before('<div class="attention"><img src="catalog/view/theme/default/image/loading.gif" alt="" /></div>');
          }
        , complete: function() {
            $('#button-confirm').fadeToggle();
            $('.payment > .attention').remove();
          }
        , success: function(response) {
            // If the response is a URL, redirect to it
            if (response.match(/^http/)) {
              window.location = response;
            // Otherwise display an error message
            } else {
              $('.buttons').before('<div class="warning">' + response + '</div>');
            }
          }
      });
    });
</script>

<form method="post" id="payment">
   <div class="buttons">
    <div class="right">
      <img src="image/ebanx/ebanx-checkout.png" id="button-confirm" />
    </div>
  </div>
</form>