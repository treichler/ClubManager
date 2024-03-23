<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

// $cakeDescription = __d('cake_dev', 'CakePHP: the rapid development php framework');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>	
  <?php echo $this->Html->charset(); ?>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <?php if (!empty($open_graph_meta_data)): ?>
  <meta property="og:url" content="<?php echo $open_graph_meta_data['url']; ?>" />
  <meta property="og:title" content="<?php echo $open_graph_meta_data['title']; ?>" />
  <meta property="og:description" content="<?php echo $open_graph_meta_data['description']; ?>" />
  <meta property="og:image" content="<?php echo $open_graph_meta_data['image']; ?>" />
  <meta property="og:type" content="<?php echo $open_graph_meta_data['type']; ?>" />
  <?php endif; ?>
  <title>
    <?php echo Configure::read('club.name') ?> :: <?php echo $title_for_layout; ?>
  </title>
  <?php
    echo $this->Html->meta('icon');
    $css_link = $this->Html->webroot('css/style.css');
    if (Configure::read('CMSystem.stylesheet_version_number')) {
      $css_link .= '?v=' . Configure::read('CMSystem.stylesheet_version_number');
    }
    echo '<link rel="stylesheet" type="text/css" href="' . $css_link . '" />';
    echo $this->Html->script('jquery-3.6.0.min');
    echo $this->fetch('meta');
    echo $this->fetch('css');
    echo $this->fetch('script');
  ?>
</head>

<body>

<script type="text/javascript">
$(document).ready(function() {
  initTinyNav();

  // workaround for flexbox wrap
  ( function( $, window, document, undefined ) {
    'use strict';

    var s = document.body || document.documentElement, s = s.style;
    if( s.webkitFlexWrap == '' || s.msFlexWrap == '' || s.flexWrap == '' ) return true;

    var $list       = $( '.gallery' ),
        $items      = $list.find( '.galleryItem' ),
        setHeights  = function() {

      $items.css('height', 'auto');

      var perRow = Math.floor( $list.width() / $items.width() );
      if( perRow == null || perRow < 2 ) return true;

      for( var i = 0, j = $items.length; i < j; i += perRow ) {
        var maxHeight   = 0,
            $row        = $items.slice( i, i + perRow );

        $row.each( function() {
          var itemHeight = parseInt( $( this ).outerHeight() );
          if ( itemHeight > maxHeight ) maxHeight = itemHeight;
        });
        $row.css( 'height', maxHeight );
        $row.css( 'float', 'left' );
      }
      $list.css('display', 'table');
    };

    setHeights();
    $( window ).on( 'resize', setHeights );
    $list.find( 'img' ).on( 'load', setHeights );

  })( jQuery, window, document );

});


// initialize option-list powered side-navigation
function initTinyNav() {
  var $mainNav    = $('#main-nav').children('ul'),
      optionsList = '<option value="" selected>Navigate...</option>';

  // get all links of the main menu and add them to the option-list
  $mainNav.find('li').each(function() {
    var $this   = $(this),
        $anchor = $this.children('a'),
        depth   = $this.parents('ul').length - 1,
        indent  = '';

    if( depth ) {
      while( depth > 0 ) {
        indent += ' - ';
        depth--;
      }
    }
    optionsList += '<option value="' + $anchor.attr('href') + '">' + indent + ' ' + $anchor.text() + '</option>';
  }).end().after('<select class="responsive-nav">' + optionsList + '</select>');

  // on change of option-list call the coresponding link
  $('.responsive-nav').on('change', function() {
    window.location = $(this).val();
  });
}
</script>

<header id="header" class="container clearfix">
  <a href="<?php echo Router::url(array('controller' => '/'), true); ?>" id="logo"><div></div></a>

  <nav id="main-nav">
    <ul>
<!-- blogs -->
      <li <?php echo $this->params['controller'] == 'blogs' ? 'class="current"' : ''; ?>>
        <?php echo $this->Html->link('Aktuelles', array('controller' => 'blogs', 'action' => 'index'), array('escape' => false, 'data-description' => 'Blog')); ?>
<!--
        <ul>
          <li <?php echo ($this->params['controller'] == 'blogs' && $this->params['action'] != 'news') ? 'class="current"' : ''; ?>>
            <?php echo $this->Html->link('Blog', array('controller' => 'blogs', 'action' => 'index')); ?>
          </li>
          <li <?php echo ($this->params['controller'] == 'galleries') ? 'class="current"' : ''; ?>>
            <?php echo $this->Html->link('Galerie', array('controller' => 'galleries', 'action' => 'index')); ?>
          </li>
        </ul>
-->
      </li>

<!-- events / availabilities -->
      <li <?php echo ($this->params['controller'] == 'events' ||
                      $this->params['controller'] == 'availabilities') ? 'class="current"' : ''; ?>>
        <?php echo $this->Html->link('Termine', array('controller' => 'events', 'action' => 'news'), array('escape' => false, 'data-description' => 'Wann, wo')); ?>
      <?php if ($this->Html->isMember($this_user) || $this->Html->hasAvailability($this_user)): ?>
        <ul>
        <?php if ($this->Html->isMember($this_user)): ?>
          <li <?php echo ($this->params['controller'] == 'events' && $this->params['action'] == 'index') ? 'class="current"' : ''; ?>>
            <?php echo $this->Html->link('Alle Termine', array('controller' => 'events', 'action' => 'index')); ?>
          </li>
        <?php endif; ?>
        <?php if ($this->Html->hasAvailability($this_user)): ?>
          <li <?php echo $this->params['controller'] == 'availabilities' ? 'class="current"' : ''; ?>>
            <?php echo $this->Html->link('Eigene Termine', array('controller' => 'availabilities', 'action' => 'index')); ?>
          </li>
        <?php endif; ?>
        </ul>
      <?php endif; ?>
      </li>

<!-- galleries -->
      <li <?php echo $this->params['controller'] == 'galleries' ? 'class="current"' : ''; ?>>
        <?php echo $this->Html->link('Galerie', array('controller' => 'galleries', 'action' => 'index'), array('escape' => false, 'data-description' => 'Bilder')); ?>
      </li>

<!-- groups -->
      <li <?php echo ($this->params['controller'] == 'groups' && ($this->params['action'] == 'index' || $this->params['action'] == 'view')) ? 'class="current"' : ''; ?>>
        <?php echo $this->Html->link('Verein', array('controller' => 'groups', 'action' => 'index'), array('escape' => false, 'data-description' => 'Gruppen')); ?>
        <ul>
          <?php foreach($public_groups as $group): ?>
          <li <?php echo ($this->params['controller'] == 'groups' && $this->params['action'] == 'view' && isset($this->params['pass'][0]) && $this->params['pass'][0] == $group['Group']['id']) ? 'class="current"' : ''; ?>>
            <?php
              echo $this->Html->link($group['Group']['name'],
              array('controller' => 'groups', 'action' => 'view', $group['Group']['id']));
            ?>
          </li>
          <?php endforeach; ?>
          <?php unset($group); ?>
        </ul>
      </li>

<!-- administrative stuff -->
      <?php
      if ($this->Html->hasPrivileg($this_user, array('Administrator')) ||
          $this->Html->isMember($this_user) ||
          $this->Html->hasPrivileg($this_user, array(
            'Contact export', 'Contact email', 'Contact sms',
            'Resource create', 'Resource modify', 'Resource delete',
            'Music book',
            'Music database',
            'Profile create', 'Profile modify', 'Profile delete',
            'File download', 'File upload', 'File modify', 'File delete'
          ))):
      ?>
      <li <?php echo (($this->params['controller'] == 'pages' && $title_for_layout == 'Administration') ||
                      ($this->params['controller'] == 'contacts' && $this->params['action'] != 'contact') ||
                      $this->params['controller'] == 'resources' ||
                      $this->params['controller'] == 'categories' ||
                      $this->params['controller'] == 'repositories' ||
                      ($this->params['controller'] == 'groups' && !($this->params['action'] == 'index' || $this->params['action'] == 'view')) ||
                      $this->params['controller'] == 'memberships' ||
                      $this->params['controller'] == 'statistics' ||
                      $this->params['controller'] == 'privilegs' ||
                      ($this->params['controller'] == 'users' && $this->params['action'] == 'index') ||
                      ($this->params['controller'] == 'contactpeople' && $this->params['action'] != 'index') ||
                      $this->params['controller'] == 'profiles' ||
                      $this->params['controller'] == 'customers' ||
                      $this->params['controller'] == 'locations' ||
                      $this->params['controller'] == 'publishers' ||
                      $this->params['controller'] == 'musicsheets' ||
                      $this->params['controller'] == 'books' ||
                      $this->params['controller'] == 'kinds' ||
                      $this->params['controller'] == 'uploads') ? 'class="current"' : ''; ?>>
        <?php echo $this->Html->link('Verwaltung', array('controller' => 'pages', 'action' => 'administration'), array('escape' => false, 'data-description' => 'Administratives')); ?>
        <ul>
          <?php if ($this->Html->hasPrivileg($this_user, array('Administrator', 'Contact export', 'Contact email', 'Contact sms'))): ?>
          <li <?php echo ($this->params['controller'] == 'contacts' && $this->params['action'] != 'contact') ? 'class="current"' : ''; ?>>
            <?php echo $this->Html->link('Kontakt', array('controller' => 'contacts', 'action' => 'index')); ?>
            <ul>
              <?php // if ($this->Html->hasPrivileg($this_user, array('Administrator'))): ?>
              <li <?php echo ($this->params['controller'] == 'contacts' && $this->params['action'] == 'protocol') ? 'class="current"' : ''; ?>>
                <?php echo $this->Html->link('Protokoll', array('controller' => 'contacts', 'action' => 'protocol')); ?>
              </li>
              <?php // endif; ?>
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
          <li <?php echo ($this->params['controller'] == 'resources' || $this->params['controller'] == 'categories' || $this->params['controller'] == 'repositories') ? 'class="current"' : ''; ?>>
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
          <li <?php echo ($this->params['controller'] == 'statistics') ? 'class="current"' : ''; ?>>
            <?php echo $this->Html->link('Statistik', array('controller' => 'statistics', 'action' => 'index')); ?>
          </li>
          <?php endif; ?>

          <?php if ($this->Html->hasPrivileg($this_user, array('Administrator'))): ?>
          <li <?php echo (($this->params['controller'] == 'groups' && !($this->params['action'] == 'index' || $this->params['action'] == 'view') || $this->params['controller'] == 'kinds')) ? 'class="current"' : ''; ?>>
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
          <li <?php echo ($this->params['controller'] == 'contactpeople' && ($this->params['action'] != 'index')) ? 'class="current"' : ''; ?>>
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

          <?php // if ($this->Html->hasPrivileg($this_user, array('File download', 'File upload', 'File modify', 'File delete'))): ?>
          <?php if ($this->Html->isMember($this_user)): ?>
          <li <?php echo ($this->params['controller'] == 'books') ? 'class="current"' : ''; ?>>
            <?php echo $this->Html->link('Musikmappen', array('controller' => 'books', 'action' => 'index')); ?>
          </li>
          <li <?php echo ($this->params['controller'] == 'uploads') ? 'class="current"' : ''; ?>>
            <?php echo $this->Html->link('Dateien', array('controller' => 'uploads', 'action' => 'index')); ?>
          </li>
          <?php endif; ?>

        </ul>
      </li>
      <?php endif; ?>

<!-- authorisation stuff -->
      <li <?php echo ($this->params['controller'] == 'users' && !($this->params['action'] == 'index')) ? 'class="current"' : ''; ?>>
      <?php if (!empty($this_user['User']['id'])): ?>
        <?php echo $this->Html->link('Abmelden', array('controller' => 'users', 'action' => 'logout'), array('escape' => false, 'data-description' => $this_user['User']['name'])); ?>
        <ul>
          <li>
            <?php echo $this->Html->link('Logout', array('controller' => 'users', 'action' => 'logout')); ?>
          </li>
          <li <?php echo ($this->params['controller'] == 'users' && $this->params['action'] == 'view') ? 'class="current"' : ''; ?>>
            <?php echo $this->Html->link($this_user['User']['name'], array('controller' => 'users', 'action' => 'view')); ?>
          </li>
        </ul>
      <?php else: ?>
        <?php echo $this->Html->link('Anmelden', array('controller' => 'users', 'action' => 'login'), array('escape' => false, 'data-description' => 'Registrieren')); ?>
        <ul>
          <li>
            <?php echo $this->Html->link('Login', array('controller' => 'users', 'action' => 'login')); ?>
          </li>
          <li <?php echo ($this->params['controller'] == 'users' && $this->params['action'] == 'add') ? 'class="current"' : ''; ?>>
            <?php echo $this->Html->link('Registrieren', array('controller' => 'users', 'action' => 'add')); ?>
          </li>
          <li <?php echo ($this->params['controller'] == 'users' && $this->params['action'] == 'create_ticket') ? 'class="current"' : ''; ?>>
            <?php echo $this->Html->link('Passwort vergessen?', array('controller' => 'users', 'action' => 'create_ticket')); ?>
          </li>
        </ul>
      <?php endif; ?>
      </li>
    </ul>
  </nav>
</header>


<section id="content" class="container clearfix">

<?php
  $flash = $this->Session->flash();
  if ($flash):
?>
  <?php echo $flash; ?>
<?php endif; ?>
<?php echo $this->fetch('content'); ?>

</section>


<footer id="footer" class="clearfix">
  <div class="container">
    <div class="three-fourth">

      <nav id="footer-nav">
        <ul id="menu-footer-navigation" class="menu">
          <li><?php echo $this->Html->link('Startseite', "/"); ?></li>
          <li><?php echo $this->Html->link('Kontakt', array('controller' => 'contactpeople', 'action' => 'index')); ?></li>
<?php if( Configure::read("recaptcha_settings.public_key") ): ?>
          <li><?php echo $this->Html->link('Kontaktformular', array('controller' => 'contacts', 'action' => 'contact')); ?></li>
<?php endif; ?>
        </ul>
      </nav>

      <div class="contact-info">
        <ul>
          <li class="address">
            <?php if (Configure::read('club.building')) echo (Configure::read('club.building').', ') ?>
            <?php echo Configure::read('club.street') ?> |
            <?php echo Configure::read('club.postal_code') . ' ' .Configure::read('club.town') ?>
          </li>
        <?php if (Configure::read('club.email')): ?>
          <li class="email"><a href="mailto:<?php echo Configure::read('club.email') ?>"><?php echo Configure::read('club.email') ?></a></li>
        <?php endif; ?>
        <?php foreach ($footer_contactpeople as $contactperson):
          $phone = '';
          if ($contactperson['Profile']['phone_office'])
            $phone = $contactperson['Profile']['phone_office'];
          if ($contactperson['Profile']['phone_private'])
            $phone = $contactperson['Profile']['phone_private'];
          if ($contactperson['Profile']['phone_mobile'])
            $phone = $contactperson['Profile']['phone_mobile'];
          if (!$phone)
            continue;
          $functions = [];
          foreach ($contactperson['Profile']['Membership']['Group'] as $group) {
            if ($group['Kind']['is_official'])
              $functions[] = $group['name'];
          }
          $name = $contactperson['Profile']['first_name'] . ' ' . $contactperson['Profile']['last_name'];
        ?>
          <li class="phone">
            <?php echo implode($functions, ', ') . ' ' . $name ?>
            <a href="tel:<?php echo $phone ?>"><?php echo $phone ?></a>
          </li>
        <?php endforeach; ?>
        <?php unset($contactperson); ?>
        <?php unset($functions); ?>
        </ul>
      </div>
    </div>

    <div class="one-fourth last">
      <div id="footer-nav">
        <ul id="menu-footer-navigation"><li>Bleiben Sie in Verbindung!</li></ul>
      </div>
      <ul class="social-links">
<?php if ($link = Configure::read('club.facebook')): ?>
        <li class="facebook"><a href="https://www.facebook.com/pages/<?php echo $link; ?>" target="_blank">Facebook</a></li>
<?php endif; ?>
<?php if ($link = Configure::read('club.twitter')): ?>
        <li class="twitter"><a href="https://twitter.com/<?php echo $link; ?>" target="_blank">Twitter</a></li>
<?php endif; ?>
<?php if ($link = Configure::read('club.youtube')): ?>
        <li class="youtube"><a href="http://youtube.com/channel/<?php echo $link; ?>" target="_blank">Youtube</a></li>
<?php endif; ?>
        <li class="rss"><?php echo $this->Html->link('RSS', array('controller' => 'blogs', 'action' => 'index.rss')); ?></li>
      </ul>
    </div>
  </div>
</footer>

<!-- lower footer -->
<footer id="footer-bottom" class="clearfix">
  <div class="container">
    <ul>
      <li>Copyright &copy; <?php
        $start_year = Configure::read('CMSystem.start_year');
        echo $start_year . (date('Y') > $start_year ? ('-' . date('Y')) : '') ?></li>
      <li><?php echo $this->Html->link(Configure::read('club.name'), "/"); ?></li>
      <li><a href="https://github.com/treichler/ClubManager">Powerd by ClubManager</a></li>
    </ul>
  </div>
</footer>
<div class="debug"><?php echo $this->element('sql_dump'); ?></div>

<?php echo $this->Js->writeBuffer(); ?>

</body>

</html>
