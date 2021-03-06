<!-- File: /app/View/Users/create_ticket.ctp -->

<?php // This file contains PHP ?>

<h1>Passwort vergessen?</h1>

<p>
Für den Fall, dass das Passwort nicht mehr bekannt ist, kann es über diese Seite zurückgesetzt werden.
Dazu gibt man seine E-Mail Adresse im unteren Feld ein und sendet diese ab.
Sofern die E-Mail Adresse in der Datenbank exisitiert, wird ein Ticket erzeugt, welches eine Stunde gültig ist.
Dieses Ticket wird in Form eines Links an die angegebene E-Mail Adresse geschickt.
Durch öffnen des Links in einem Browser kommt man auf die Seite zur Eingabe eines neuen Passwortes.
<br/>
Sollte das Ticket innerhalb der gültigen Zeit nicht angewendet werden, so kann man durch erneutes Absenden
der E-Mail Adresse ein neues Ticket generieren, welches wieder für eine Stunde gültig ist.
</p>

<?php
  echo $this->Form->create('User', array());

  if( Configure::read("CMSystem.legitimation") )
    echo $this->Form->input('legitimation', array('label' => 'Legitimation (bei Administrator erhältlich)'));

  echo $this->Form->input('email',  array('label' => 'E-Mail'));

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
?>

