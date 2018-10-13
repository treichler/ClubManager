<!-- File: /app/View/Elements/contact_form.ctp -->

<?php // This file holds the common form element to select the profiles

//  echo $this->Form->input('title');
//  echo $this->Form->input('body', array('rows' => '3'));

  echo $this->Form->input('group', array('label' => 'Gruppe', 'type' => 'select', 'empty'=>true));
//  echo $this->Form->input('Group.Group', array('empty'=>true));
/*
  echo $this->Form->input('Group',array(
    'label' => __('Groups',true),
    'type' => 'select',
    'multiple' => 'checkbox',
    'selected' => $this->Html->value('Group.Group'),
  ));
*/

  // HABTM field: checkboxes
  echo $this->Form->input('Profile',array(
    'label' => 'Namen',
    'type' => 'select',
    'multiple' => 'checkbox',
//    'options' => $tags,
    'selected' => $this->Html->value('Profile.Profile'),
  ));

?>

<?php
  $profile_name = null;
  if (!empty($this_user['Profile']['id']))
    $profile_name = $this_user['Profile']['first_name'] . ' ' . $this_user['Profile']['last_name'];
  if (!empty($this_user['User']['id']))
    $name = $profile_name ? $profile_name : $this_user['User']['username'];
?>

<script type="text/javascript">
var memberships = <?php echo json_encode($memberships); ?>;
var states = <?php echo json_encode($states); ?>;
var is_available = [];
var current_user = "<?php echo $name ?>";

$(document).ready(function(){
  // get available state's ids
  for (var s in states) {
    if (states[s].State.is_available) {
      is_available.push(states[s].State.id);
    }
  }
//  alert(is_available.join(", "));
//  alert(current_user);
});

$("select").change( function () {
  // get selected group id
  var group = $(this).find(":selected").val();

  // clear all checkboxes
  $("input[type=checkbox]").prop('checked', false);

  // select checkboxes according to profiles
  var profiles = [];
  if (group) {
    profiles = getProfiles(group);
    for (var p in profiles) {
      $("#ProfileProfile" + profiles[p]).prop('checked', true);
    }
  }


//  alert(getProfiles($(this).find(":selected").val()).join(", "));

//  $('.myCheckbox').prop('checked', true);

/*
  var temp = [];
  $("input[type=checkbox]").each(function() {
//    temp.push($this.val());
    temp.push('huhu');
  });
//  alert(temp.join(", "));
*/

});


function getProfiles(group_id) {
  var result = new Array();
  for (var m in memberships) {
    for (var g in memberships[m].Group) {
      if (memberships[m].Group[g].id == group_id) {
        for (i in is_available) {
          if (memberships[m].State.is_available == is_available[i]) {
            result.push(memberships[m].Profile.id);
            break;
          }
        }
      }
    }
  }
  return result;
}


</script>

