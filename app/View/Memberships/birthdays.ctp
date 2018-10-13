<!-- File: /app/View/Memberships/birthdays.ctp -->

<?php // This file contains PHP
  echo $this->Html->script('d3.min');
?>

<h1>Geburtstagsliste</h1>

<p><?php echo $this->Html->link('Geburtstagsliste herunterladen', array(
  'controller' => 'memberships', 'action' => 'birthdays', 'ext' => 'pdf')); ?></p>

<table>
<?php
$month = $last_month = 0;
$ages = [];
foreach ($memberships as $membership):
  $today = new DateTime();
  $birthday = new DateTime($membership['Profile']['birthday']);
  $interval = $birthday->diff($today);
  if ($membership['Profile']['birthday']) {
    $ages[] = $interval->format('%Y') * 1.0;
  }
?>

  <?php $month = ($membership['Profile']['birthday'] ? $birthday->format('m') : -1); ?>
  <?php if ($last_month != $month): ?>
  <tr>
    <th colspan=3><?php
      $last_month = $month;
      if ($membership['Profile']['birthday'])
        echo $this->Html->months[$month];
      else
        echo 'Keine Angabe';
    ?></th>
  </tr>
  <?php endif; ?>

  <tr>
    <td><?php if ($membership['Profile']['birthday']) echo $this->Html->getDate($membership['Profile']['birthday'], array('year' => true, 'day' => false)); ?></td>
    <td><?php echo h($membership['Profile']['first_name'] . ' ' . $membership['Profile']['last_name']); ?></td>
    <td><?php if ($membership['Profile']['birthday']) echo $interval->format('%Y Jahre'); ?></td>
  </tr>
<?php endforeach; ?>
<?php unset($membership) ?>
<?php unset($months) ?>
</table>


<h4>Altersverteilung</h4>

<div id="histogram"></div>


<script>
var ages = <?php echo json_encode($ages); ?>;
var ages_max = Math.max.apply(Math, ages);
var ages_min = Math.min.apply(Math, ages);
var ages_length = ages.length;
$('#histogram').before("<ul><li>Minimum: " + ages_min + " Jahre</li><li>Maximum: " + ages_max + " Jahre</li><li>Anzahl der Bewertungen: " + ages_length + "</li></ul>");

// A formatter for counts.
var formatCount = d3.format(",.0f");

var margin = {top: 10, right: 30, bottom: 30, left: 30},
    width = parseFloat($('#histogram').css('width')) - margin.left - margin.right,
    height = parseFloat($('#histogram').css('height')) - margin.top - margin.bottom;

var x = d3.scale.linear()
    .domain([0, 100])
    .range([0, width]);

// Generate a histogram using twenty uniformly-spaced bins.
var data = d3.layout.histogram()
    .bins(x.ticks(20))
    (ages);

var y = d3.scale.linear()
    .domain([0, d3.max(data, function(d) { return d.y; })])
    .range([height, 0]);

var xAxis = d3.svg.axis()
    .scale(x)
    .orient("bottom");

//var svg = d3.select("body").append("svg")
var svg = d3.select("#histogram").append("svg")
    .attr("width", width + margin.left + margin.right)
    .attr("height", height + margin.top + margin.bottom)
  .append("g")
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

var bar = svg.selectAll(".bar")
    .data(data)
  .enter().append("g")
    .attr("class", "bar")
    .attr("transform", function(d) { return "translate(" + x(d.x) + "," + y(d.y) + ")"; });

bar.append("rect")
    .attr("x", 1)
    .attr("width", x(data[0].dx) - 1)
    .attr("height", function(d) { return height - y(d.y); });

bar.append("text")
    .attr("dy", ".75em")
    .attr("y", 6)
    .attr("x", x(data[0].dx) / 2)
    .attr("text-anchor", "middle")
    .text(function(d) { return formatCount(d.y); });

svg.append("g")
    .attr("class", "x axis")
    .attr("transform", "translate(0," + height + ")")
    .call(xAxis);
</script>

