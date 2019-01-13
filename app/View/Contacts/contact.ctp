<!-- File: /app/View/Contacts/email.ctp -->

<?php // This file contains PHP ?>

<h1>Kontaktieren Sie uns</h1>

<?php
  if ($contactpeople_available) {
    echo $this->Form->create('Contact', array('url' => 'contact'));
    echo $this->Form->input('email', array('label' => 'Ihre E-Mail Addresse (damit wir antworten können)'));
    echo $this->Form->input('subject', array('label' => 'Betreff'));
    echo $this->Form->input('text', array('rows' => '7', 'label' => 'Text'));
    echo '<div id="recaptcha_div"></div>';
    echo $this->Form->end('Senden');
    echo $this->Html->script('https://www.google.com/recaptcha/api/js/recaptcha_ajax.js');
  }
?>

<script type="text/javascript">
$(document).ready(function(){
    Recaptcha.create("<?php echo Configure::read("recatpch_settings.public_key")?>", 'recaptcha_div', {
    theme: "red",
    callback: Recaptcha.focus_response_field});
});
</script>

