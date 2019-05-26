<!-- File: /app/View/Contacts/email.ctp -->

<?php // This file contains PHP ?>

<h1>Kontaktieren Sie uns</h1>

<?php
  if ($contactpeople_available) {
    echo $this->Form->create('Contact', array());
    echo $this->Form->input('email', array('label' => 'Ihre E-Mail Addresse (damit wir antworten können)'));
    echo $this->Form->input('subject', array('label' => 'Betreff'));
    echo $this->Form->input('text', array('rows' => '7', 'label' => 'Text'));

    if( Configure::read("recaptcha_settings.public_key") )
      echo "\n<div id=\"recaptcha_div\"></div>\n";

    echo $this->Form->end('Senden');

    if( Configure::read("recaptcha_settings.public_key") ) {
      echo "\n";
      echo $this->Html->script('https://www.google.com/recaptcha/api/js/recaptcha_ajax.js');
      echo "\n" .
        "<script type=\"text/javascript\">\n" .
        "  $(document).ready(function(){\n" .
        "    Recaptcha.create('" . Configure::read("recaptcha_settings.public_key") . "', 'recaptcha_div', {\n" .
        "      theme: 'red',\n" .
        "      callback: Recaptcha.focus_response_field});\n" .
        "    }\n" .
        "  );\n" .
        "</script>";
    }
  }
?>

