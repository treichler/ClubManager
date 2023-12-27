<!-- File: /app/View/Codes/index.ctp -->

<?php // This file contains PHP ?>

<?php echo $this->Html->script('html5-qrcode.min'); ?>




<h1>QR Code Reader</h1>

<p>ZVR: <?php echo Configure::read('club.id'); ?></p>

<div style="width: 400px" id="reader"></div>

<div id="nameContainer"></div>

<div id="checkContainer"></div>

<div id="feedbackContainer"></div>


<script type="text/javascript">
var club_id = "<?php echo Configure::read('club.id'); ?>";

var musicsheetId = null;

var events = null;

var last_qr = null;

var container = document.getElementById( "checkContainer" );
var nameContainer = document.getElementById( "nameContainer" );
var feedbackContainer = document.getElementById( "feedbackContainer" );

function TrackMusicsheet(event_id, musicsheet_id)
{
  alert("EventId: " + event_id + ", MusicsheetId: " + musicsheet_id);
  feedbackContainer.innerHTML = "<p>ok...</p>";
}

function fetchEvents() {
  if( events != null )
    return;
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200)
    {
      if( events == null )
      {

        events = JSON.parse( this.responseText );

        var selectEventDiv = document.createElement("div");
        selectEventDiv.className = "input select";
        container.appendChild(selectEventDiv);
        var selectEvent = document.createElement("select");
        selectEvent.id = "selectEvent";
        selectEventDiv.appendChild(selectEvent);
        for( var i = 0; i < events.length; i++ )
        {
          var option = document.createElement("option");
          option.value = events[i].Event.id;
          option.text = events[i].Event.name;
          selectEvent.appendChild(option);
        }

        var trackLinkDiv = document.createElement("div");
        trackLinkDiv.className = "submit";
        container.appendChild(trackLinkDiv);
        var trackLink = document.createElement("input");
        trackLink.setAttribute("type", "button");
        trackLink.value = "Track Music";
        trackLink.onclick = function() {
          var selectEvent = document.getElementById("selectEvent");
          TrackMusicsheet(selectEvent.value, musicsheetId);
        };
        trackLinkDiv.appendChild(trackLink);
      }
    }
  };
  xhttp.open("GET", "events.json", true);
  xhttp.send();
}

function viewMusicsheet(id) {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if( this.readyState == 4 )
    {
      if( this.status == 200 )
      {
//        document.getElementById("demo").innerHTML = this.responseText;
        json = JSON.parse( this.responseText );
        if( json.Musicsheet )
        {
//          alert( json.Musicsheet.title );
          nameContainer.innerHTML = "<p>" + json.Musicsheet.title + "</p>";
          musicsheetId = json.Musicsheet.id;
        }
        else
        {
          musicsheetId = null;
          nameContainer.innerHTML = "<p>undefined...</p>";
        }
      }
      else
      {
        last_qr = null;
      }
    }
  };
  xhttp.open("GET", "musicsheets/view/" + id + ".json", true);
  xhttp.send();
}

function verifyQrCode(raw) {
  if( last_qr != raw )
  {
    last_qr = raw;

    code = JSON.parse( raw );
    if( code.ZVR && code.ZVR == club_id )
    {
      if( code.musicsheet )
      {
//        alert( "musicsheet: " + code.musicsheet );
        viewMusicsheet( code.musicsheet );
        fetchEvents();
      }
    }
    else
      alert( "Wrong organization" );
  }
}


function onScanSuccess(qrCodeMessage) {
  // handle on success condition with the decoded message
  verifyQrCode(qrCodeMessage);
}

var html5QrcodeScanner = new Html5QrcodeScanner(
  "reader",
  {
    fps: 10,
    qrbox: 250,
    formatsToSupport: [ Html5QrcodeSupportedFormats.QR_CODE ],
    supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA]
  });
html5QrcodeScanner.render(onScanSuccess);

</script>

