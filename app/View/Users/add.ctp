<!-- app/View/Users/add.ctp -->

<?php // This file contains PHP ?>

<h1>Registrieren</h1>

<p>Nach Abschluss der Registrierung bitte anmelden.</p>

<?php
  echo $this->Form->create('User', array());

  if( Configure::read("CMSystem.legitimation") )
    echo $this->Form->input('legitimation', array('label' => 'Legitimation (bei Administrator erhältlich)'));

  echo $this->Form->input('username', array('label' => 'Benutzername'));
  echo $this->Form->input('email', array('label' => 'E-Mail'));
  echo $this->Form->input('password1', array('type' => 'password', 'label' => 'Passwort'));
  echo $this->Form->input('password2', array('type' => 'password', 'label' => 'Passwort wiederholen'));

  if( Configure::read("recaptcha_settings.public_key") )
    echo "\n<div id=\"recaptcha_div\"></div>\n";

  echo $this->Form->end(__('Registrieren'));

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
?>

