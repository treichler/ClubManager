<!-- File: /app/View/Pages/home.ctp -->

<?php // This file contains PHP ?>

<!-- JavaScript for slider -->
<?php echo $this->Html->script('s3Slider.min'); ?>


<script type="text/javascript">
$(document).ready(function() {
  // initialize slider
  $('#slider').s3Slider({
    timeOut: 4200 // needs to be multiple of 6
  });

  // initialize rotator
  setupRotator();
});

function setupRotator() {
  $('.textItem:first').addClass('current').fadeIn(1000);
  if ($('.textItem').length > 1) {
    setInterval('textRotate()', 6000);
  }
}

function textRotate() {
  var current = $('#upcomingEventsContent > .current');
  if (current.next().length == 0) {
    current.removeClass('current').fadeOut(1000);
    $('.textItem:first').addClass('current').fadeIn(1000);
  } else {
    current.removeClass('current').fadeOut(1000);
    current.next().addClass('current').fadeIn(1000);
  }
}
</script>

<h1 class="align-center">Herzlich Willkommen auf unserer Homepage</h1>

<div id="slider">
  <ul id="sliderContent">
    <!-- blogs pictures -->
    <?php foreach ($blogs as $blog): ?>
    <li class="sliderImage">
      <a>
        <?php
          echo $this->Html->image(array(
            'controller' => 'blogs',
            'action'     => 'attachment', $blog['Blog']['id']
          ), array(
            'alt' => $blog['Blog']['title']
          ));
        ?>
      </a>
      <span class="top">
        <strong>
        <?php
          echo $this->Html->link($blog['Blog']['title'],
          array('controller' => 'blogs', 'action' => 'view', $blog['Blog']['id']));
        ?>
        </strong>
      </span>
    </li>
    <?php endforeach; ?>
    <?php unset($blog); ?>
    <!-- groups pictures -->
    <?php foreach ($groups as $group): ?>
    <li class="sliderImage">
      <a>
        <?php
          echo $this->Html->image(array(
            'controller' => 'groups',
            'action'     => 'attachment', $group['Group']['id']
          ), array(
            'alt' => $group['Group']['name']
          ));
        ?>
      </a>
      <span class="top">
        <strong>
        <?php
          echo $this->Html->link($group['Group']['name'],
          array('controller' => 'groups', 'action' => 'view', $group['Group']['id']));
        ?>
        </strong>
      </span>
    </li>
    <?php endforeach; ?>
    <?php unset($group); ?>
    <div class="clear sliderImage"></div>
  </ul>
</div>


<div id="upcomingEvents">
<ul id="upcomingEventsContent">
<?php foreach ($events as $event):?>
  <li class="textItem">
    <b><?php
        echo $this->Html->getDate($event['Event']['start'], array('year' => true));
        if (!$this->Html->isSameDay($event)) echo (' - ' . $this->Html->getDate($event['Event']['stop'], array('year' => true)));
    ?></b>
    <?php
        echo h($event['Event']['name']);
        $groups = [];
        foreach($event['Group'] as $group) {
          $groups[] = h($group['name']);
        }
        unset($group);
        if( !empty($groups) )
          echo ' <i>(' . implode(', ', $groups) . ')</i>';
    ?>
  </li>
<?php endforeach; ?>
<?php unset($event); ?>
</ul>
</div>


