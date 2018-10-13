<!-- File: /app/View/Statistics/availabilities.ctp -->

<?php // This file contains PHP ?>

<?php echo $this->Html->script('jquery.tablesorter.min'); ?>

<h1>Anwesenheit <?php echo $year ?></h1>

<p>
  <label for="dataSelectGroup">Gruppe</label>
  <select id="dataSelectGroup"></select>
</p>

<table id="dataTable" class="tablesorter">
</table>

<!--
<h2>Histogram</h2>

<ul>
  <li><a>Alle</a></li>
  <li><a>Gruppe 1</a></li>
  <li><a>Gruppe 2</a></li>
  <li><a>Gruppe n</a></li>
</ul>
-->

<script type="text/javascript">

var _matrix = <?php echo $matrix; ?>;

$(document).ready(function() {

  // prepare select entries
  s = $('#dataSelectGroup');
  option = document.createElement("option");
  option.setAttribute('value', 'all');
  option.innerHTML = 'Alle Gruppen';
  s.append(option.outerHTML);
  for (p in _matrix['names']['groups']) {
    option = document.createElement("option");
    option.setAttribute('value', p);
    option.innerHTML = _matrix['names']['groups'][p];
    s.append(option.outerHTML);
  }

  // prepare data
  for (p in _matrix['names']['profiles']) {
    if (_matrix['data'][p] == undefined) {
      continue;
    }
    _matrix['data'][p]['all'] = [];
    for (g in _matrix['names']['groups']) {
      if (_matrix['data'][p][g] == undefined) {
        continue;
      }
      for (m in _matrix['names']['modes']) {

        if (_matrix['data'][p][g][m] == undefined) {
          _matrix['data'][p][g][m] = 0;
        }
        if (_matrix['data'][p]['all'][m] == undefined) {
          _matrix['data'][p]['all'][m] = 0;
        }
        _matrix['data'][p]['all'][m] += _matrix['data'][p][g][m];
      }
    }
  }

  updateDataTable('all');

  // call the tablesorter plugin
  $("table").tablesorter({
    // sort on the first column, order asc
    // XXX Continuous presort of table leads to growing table in combination with updateDataTable().
    //     So this is called only once here, after document ready.
    sortList: [[0,0]]
  });
});


function updateDataTable(g) {
  // clear table's content
  $('#dataTable').html('');

  // create table's head
  tr = document.createElement("tr");
  tr.innerHTML = '<th>Vor&shy;name</th><th>Familien&shy;name</tr>';
  for (i in _matrix['names']['modes']) {
    th = document.createElement("th");
    th.innerHTML = _matrix['names']['modes'][i];
    tr.innerHTML += th.outerHTML
  }
  tr.innerHTML += '<th>&sum;</th>';
  thead = document.createElement("thead");
  thead.innerHTML = tr.outerHTML;
  $('#dataTable').append(thead);

  // create table's content
//  g = '2';
  count = 0;
  max = [];
  max_all = 0;
  s = [];
  tbody = document.createElement("tbody");
  for (p in _matrix['names']['profiles']) {
    if (_matrix['data'][p] == undefined || _matrix['data'][p][g] == undefined) {
      continue;
    }
    count ++;
    tr = document.createElement("tr");
    td = document.createElement("td");
    td.innerHTML = _matrix['names']['profiles'][p]['first_name'];
    tr.innerHTML = td.outerHTML;
    td = document.createElement("td");
    td.innerHTML = _matrix['names']['profiles'][p]['last_name'];
    tr.innerHTML += td.outerHTML;
    sum = 0;
    for (m in _matrix['names']['modes']) {
      sum += _matrix['data'][p][g][m];
      if (s[m] == undefined) {
        s[m] = 0;
      }
      s[m] += _matrix['data'][p][g][m];
      if (max[m] == undefined) {
        max[m] = 0;
      }
      if (max[m] < _matrix['data'][p][g][m]) {
        max[m] = _matrix['data'][p][g][m];
      }
      td = document.createElement("td");
      td.innerHTML = _matrix['data'][p][g][m];
      tr.innerHTML += td.outerHTML
    }
    if (max_all < sum) {
      max_all = sum;
    }
    tr.innerHTML += '<td>' + sum + '</td>';
    tbody.innerHTML += tr.outerHTML;
  }
  $('#dataTable').append(tbody);

  tfoot = document.createElement("tfoot");
  // sum
  tr = document.createElement("tr");
  tr.innerHTML = '<th colspan="2">Summe</th>';
  sum = 0;
  for (m in _matrix['names']['modes']) {
    sum += s[m];
    td = document.createElement("td");
    td.innerHTML = s[m];
    tr.innerHTML += td.outerHTML;
  }
  tr.innerHTML += '<td>' + sum + '</td>';
  tfoot.innerHTML += tr.outerHTML;

  if (count > 0) {
    // average
    tr = document.createElement("tr");
    tr.innerHTML = '<th colspan="2">Durchschnitt</th>';
    sum = 0;
    for (m in _matrix['names']['modes']) {
      sum += s[m];
      td = document.createElement("td");
      td.innerHTML = (Math.round(s[m] * 100 / count) / 100);
      tr.innerHTML += td.outerHTML;
    }
    tr.innerHTML += '<td>' + (Math.round(sum * 100 / count) / 100) + '</td>';
    tfoot.innerHTML += tr.outerHTML;

    // maximum
    tr = document.createElement("tr");
    tr.innerHTML = '<th colspan="2">Maximum</th>';
    for (m in _matrix['names']['modes']) {
      td = document.createElement("td");
      td.innerHTML = max[m];
      tr.innerHTML += td.outerHTML;
    }
    tr.innerHTML += '<td>' + max_all + '</td>';
    tfoot.innerHTML += tr.outerHTML;
  }
  $('#dataTable').append(tfoot);
}


$('#dataSelectGroup').click(function(event) {
  updateDataTable(event.currentTarget.value);

  // call the tablesorter plugin
  $("table").tablesorter();
});

</script>


