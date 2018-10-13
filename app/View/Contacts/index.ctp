<!-- File: /app/View/Contacts/index.ctp -->

<?php // This file contains PHP ?>

<h1>Contacts</h1>

<ul>
  <li><?php echo $this->Html->link('E-Mail', array('controller' => 'contacts', 'action' => 'email')); ?></li>
  <li><?php echo $this->Html->link('SMS', array('controller' => 'contacts', 'action' => 'sms')); ?></li>
  <li><?php echo $this->Html->link('Export', array('controller' => 'contacts', 'action' => 'export')); ?></li>
</ul>

