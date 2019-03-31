<!-- app/View/Users/add.ctp -->

<?php // This file contains PHP ?>

<h1>Registrieren</h1>

<p>Registrierte Benutzer haben mehr M&ouml;glichkeiten...</p>

<p>Nach Abschluss der Registrierung bitte anmelden.</p>

<?php
  echo $this->Form->create('User', array());
  echo $this->Form->input('username', array('label' => 'Benutzername'));
  echo $this->Form->input('email', array('label' => 'E-Mail'));
  echo $this->Form->input('password1', array('type' => 'password', 'label' => 'Passwort'));
  echo $this->Form->input('password2', array('type' => 'password', 'label' => 'Passwort wiederholen'));
?>
<div id="recaptcha_div"></div>
<?php
  echo $this->Form->end(__('Registrieren'));
  echo $this->Html->script('https://www.google.com/recaptcha/api/js/recaptcha_ajax.js'); 
?>


<script type="text/javascript">
$(document).ready(function(){
    Recaptcha.create("<?php echo Configure::read("recatpch_settings.public_key")?>", 'recaptcha_div', {
    theme: "red",
    callback: Recaptcha.focus_response_field});
});
</script>

