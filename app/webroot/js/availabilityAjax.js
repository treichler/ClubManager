// do the ajax request to change the availability
function isAvailable(id,cls,path,val) {
  $.ajax({
    url: path + cls + '/' + id,
    type: 'POST',
    dataType: 'html',
    data: 'id=' + id + '&' + cls + '=' + val,

//      dataType: 'json',
//      async: false,
//      data: '{"Calendar" : {"command" : "create"}}',
    success: function(raw_data, textStatus, jqXHR){
      var data = jQuery.parseJSON(raw_data);
      if (data.state === 'alert')
        alert(data.message);
      else
        $('#row_' + id).effect("highlight", {"color" : "#ffff99"}, 1000);
      if (cls === 'info')
        $('#Availability' + id + cls).attr('value', data.response);
      if (data.response === 'true')
        setCheckboxToVal(id,cls,true);
      if (data.response === 'false')
        setCheckboxToVal(id,cls,false);
    },
    error: function(){
      alert("Übertragungsfehler");
        if (val)
          setCheckboxToVal(id,cls,false);
        else
          setCheckboxToVal(id,cls,true);
    }
  });
}

// set the document according to the state of availability
function setCheckboxToVal(id,cls,val) {
  if (cls === 'availabilities_checked')
    var input = $('#availabilitiesCheckedId');
  else if (cls === 'tracks_checked')
    var input = $('#tracksCheckedId');
  else
    var input = $('#Availability' + id + cls);
  if (input.attr('type') == 'checkbox')
    input.prop('checked', val);
}

