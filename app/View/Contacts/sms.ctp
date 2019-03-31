<!-- File: /app/View/Contacts/sms.ctp -->

<?php // This file contains PHP ?>

<h1>SMS</h1>

<p><?php echo $credit; ?></p>

<p><?php if (isset($request)) echo $request; ?></p>

<div id="smsInfo">161 Zeichen, 2 SMS</div>

<?php
  echo $this->Form->create('Contact', array());
  echo $this->Form->input('text', array('rows' => '3', 'label' => 'Text'));
  echo $this->element('contact_form');
  echo $this->Form->end('Senden');
?>

<script type="text/javascript">
$(document).ready(function(){
  function updateSmsInfo(){
    var input = $("#ContactText").val();
    var special_chars = input.match(new RegExp("[~\\[\\]{}\\|\\^\\r\\n]", 'g'));
    var chars_count = (special_chars ? special_chars.length : 0) + input.length;
    var info_text = chars_count + " Zeichen, " + Math.ceil(chars_count / 160) + " SMS";
    $("#smsInfo").text(info_text);
  }

  updateSmsInfo();

  $('#ContactText').keyup(function(){
    updateSmsInfo();
  });
});
</script>

