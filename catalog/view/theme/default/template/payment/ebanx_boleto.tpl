<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content"><?php echo $content_top; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>

  <h1><?php echo $heading_title; ?></h1>
  <?php echo $text_message; ?>
  <h2>Print your boleto</h2>
  <p>To print your boleto bancário, please click the button below:</p>
  <p>
    <a href="<?php echo $boleto; ?>" target="_blank"><img src="image/ebanx/ebanx-print-boleto.png" /></a>
  </p>

  <div class="buttons">
    <div class="right"><a href="<?php echo $continue; ?>" class="button"><?php echo $button_continue; ?></a></div>
  </div>

  <?php echo $content_bottom; ?></div>
<?php echo $footer; ?>