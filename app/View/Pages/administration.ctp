<!-- File: /app/View/Pages/member.ctp -->

<?php // This file contains PHP ?>

<h1>Verwaltung</h1>

<p>
Du hast Zugriff auf folgende Seiten:
</p>

<ul>
  <?php if ($this->Html->hasPrivileg($this_user, array('Contact export', 'Contact email', 'Contact sms'))): ?>
  <li <?php echo ($this->params['controller'] == 'contacts') ? 'class="current"' : ''; ?>>
    <?php echo $this->Html->link('Kontakt', array('controller' => 'contacts', 'action' => 'index')); ?>
    <ul>
      <li <?php echo ($this->params['controller'] == 'contacts' && $this->params['action'] == 'protocol') ? 'class="current"' : ''; ?>>
        <?php echo $this->Html->link('Protokoll', array('controller' => 'contacts', 'action' => 'protocol')); ?>
      </li>
      <?php if ($this->Html->hasPrivileg($this_user, array('Contact email'))): ?>
      <li <?php echo ($this->params['controller'] == 'contacts' && $this->params['action'] == 'email') ? 'class="current"' : ''; ?>>
        <?php echo $this->Html->link('E-Mail', array('controller' => 'contacts', 'action' => 'email')); ?>
      </li>
      <?php endif; ?>
      <?php if ($this->Html->hasPrivileg($this_user, array('Contact sms'))): ?>
      <li <?php echo ($this->params['controller'] == 'contacts' && $this->params['action'] == 'sms') ? 'class="current"' : ''; ?>>
        <?php echo $this->Html->link('SMS', array('controller' => 'contacts', 'action' => 'sms')); ?>
      </li>
      <?php endif; ?>
      <?php if ($this->Html->hasPrivileg($this_user, array('Contact export'))): ?>
      <li <?php echo ($this->params['controller'] == 'contacts' && $this->params['action'] == 'export') ? 'class="current"' : ''; ?>>
        <?php echo $this->Html->link('Exportieren', array('controller' => 'contacts', 'action' => 'export')); ?>
      </li>
      <?php endif; ?>
    </ul>
  </li>
  <?php endif; ?>
  <?php if ($this->Html->hasPrivileg($this_user, array('Resource create', 'Resource modify', 'Resource delete'))): ?>
  <li <?php echo ($this->params['controller'] == 'resources') ? 'class="current"' : ''; ?>>
    <?php echo $this->Html->link('Ressourcen', array('controller' => 'resources', 'action' => 'index')); ?>
    <ul>
      <li <?php echo ($this->params['controller'] == 'categories') ? 'class="current"' : ''; ?>>
        <?php echo $this->Html->link('Kategorien', array('controller' => 'categories', 'action' => 'index')); ?>
      </li>
      <li <?php echo ($this->params['controller'] == 'repositories') ? 'class="current"' : ''; ?>>
        <?php echo $this->Html->link('Aufbewahrungsplätze', array('controller' => 'repositories', 'action' => 'index')); ?>
      </li>
    </ul>
  </li>
  <?php endif; ?>
  <?php if ($this->Html->isMember($this_user) || $this->Html->hasPrivileg($this_user, array('Administrator'))): ?>
  <li <?php echo ($this->params['controller'] == 'memberships') ? 'class="current"' : ''; ?>>
    <?php echo $this->Html->link('Mitglieder', array('controller' => 'memberships', 'action' => 'index')); ?>
    <?php if ($this->Html->isMember($this_user)): ?>
    <ul>
      <li <?php echo ($this->params['controller'] == 'memberships' && $this->params['action'] == 'contacts') ? 'class="current"' : ''; ?>>
        <?php echo $this->Html->link('Kontaktliste', array('controller' => 'memberships', 'action' => 'contacts')); ?>
      </li>
      <li <?php echo ($this->params['controller'] == 'memberships' && $this->params['action'] == 'birthdays') ? 'class="current"' : ''; ?>>
        <?php echo $this->Html->link('Geburtstagsliste', array('controller' => 'memberships', 'action' => 'birthdays')); ?>
      </li>
    </ul>
    <?php endif; ?>
  </li>
  <?php endif; ?>
  <li <?php echo ($this->params['controller'] == 'statistics') ? 'class="current"' : ''; ?>>
    <?php echo $this->Html->link('Statistik', array('controller' => 'statistics', 'action' => 'index')); ?>
  </li>
  <?php if ($this->Html->hasPrivileg($this_user, array('Administrator'))): ?>
  <li <?php echo ($this->params['controller'] == 'groups' && !($this->params['action'] == 'index' || $this->params['action'] == 'view')) ? 'class="current"' : ''; ?>>
    <?php echo $this->Html->link('Gruppen', array('controller' => 'groups', 'action' => 'organize')); ?>
    <ul>
      <li <?php echo ($this->params['controller'] == 'kinds') ? 'class="current"' : ''; ?>>
        <?php echo $this->Html->link('Art der Gruppen', array('controller' => 'kinds', 'action' => 'index')); ?>
      </li>
    </ul>
  </li>
  <li <?php echo ($this->params['controller'] == 'privilegs') ? 'class="current"' : ''; ?>>
    <?php echo $this->Html->link('Privilegien', array('controller' => 'privilegs', 'action' => 'index')); ?>
  </li>
  <li <?php echo ($this->params['controller'] == 'users' && ($this->params['action'] == 'index')) ? 'class="current"' : ''; ?>>
    <?php echo $this->Html->link('Benutzer', array('controller' => 'users', 'action' => 'index')); ?>
  </li>
  <li <?php echo ($this->params['controller'] == 'contactpeople' && ($this->params['action'] == 'organize')) ? 'class="current"' : ''; ?>>
    <?php echo $this->Html->link('Kontaktpersonen', array('controller' => 'contactpeople', 'action' => 'organize')); ?>
  </li>
  <?php endif; ?>
  <?php if ($this->Html->hasPrivileg($this_user, array('Profile create', 'Profile modify', 'Profile delete'))): ?>
  <li <?php echo ($this->params['controller'] == 'profiles') ? 'class="current"' : ''; ?>>
    <?php echo $this->Html->link('Profile', array('controller' => 'profiles', 'action' => 'index')); ?>
  </li>
  <?php endif; ?>
  <?php if ($this->Html->hasPrivileg($this_user, array('Customer create', 'Customer modify', 'Customer delete'))): ?>
  <li <?php echo ($this->params['controller'] == 'customers') ? 'class="current"' : ''; ?>>
    <?php echo $this->Html->link('Kunden', array('controller' => 'customers', 'action' => 'index')); ?>
  </li>
  <?php endif; ?>
  <?php if ($this->Html->hasPrivileg($this_user, array('Location create', 'Location modify', 'Location delete'))): ?>
  <li <?php echo ($this->params['controller'] == 'locations') ? 'class="current"' : ''; ?>>
    <?php echo $this->Html->link('Orte', array('controller' => 'locations', 'action' => 'index')); ?>
  </li>
  <?php endif; ?>
  <?php if ($this->Html->hasPrivileg($this_user, array('Music database'))): ?>
  <li <?php echo ($this->params['controller'] == 'publishers') ? 'class="current"' : ''; ?>>
    <?php echo $this->Html->link('Verlage', array('controller' => 'publishers', 'action' => 'index')); ?>
  </li>
  <li <?php echo ($this->params['controller'] == 'musicsheets') ? 'class="current"' : ''; ?>>
    <?php echo $this->Html->link('Musikstücke', array('controller' => 'musicsheets', 'action' => 'index')); ?>
  </li>
  <?php endif; ?>
  <?php if ($this->Html->hasPrivileg($this_user, array('Music book'))): ?>
  <li <?php echo ($this->params['controller'] == 'books') ? 'class="current"' : ''; ?>>
    <?php echo $this->Html->link('Musikmappen', array('controller' => 'books', 'action' => 'index')); ?>
  </li>
  <?php endif; ?>
  <?php if ($this->Html->hasPrivileg($this_user, array('File download', 'File upload', 'File modify', 'File delete'))): ?>
  <li <?php echo ($this->params['controller'] == 'uploads') ? 'class="current"' : ''; ?>>
    <?php echo $this->Html->link('Dateien', array('controller' => 'uploads', 'action' => 'index')); ?>
  </li>
  <?php endif; ?>
</ul>


