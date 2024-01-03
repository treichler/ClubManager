<!-- File: /app/View/Codes/index.ctp -->

<?php // This file contains PHP ?>

<?php echo $this->Html->script('html5-qrcode.min'); ?>

<h1>QR Code Reader</h1>

<div id="QRreader"></div>

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

function DateToStr( date ) {
  days = ["So", "Mo", "Di", "Mi", "Do", "Fr", "Sa"];
  day = days[ date.getUTCDay() ];
  d = date.getUTCDate();
  M = date.getUTCDate() + 1;
  Y = date.getUTCFullYear();
  h = date.getUTCHours();
  m = date.getUTCMinutes();
  return day + ", " + (d > 9 ? d : '0' + d) + "." + (M > 9 ? M : '0' + M) + "." + Y + " " +
         (h > 9 ? h : '0' + h) + ":" + (m > 9 ? m : '0' + m);
}

function TrackMusicsheet(event_id, musicsheet_id)
{
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && (this.status == 200 || this.status == 401))
    {
      response = JSON.parse(this.responseText);
      if( response.state == true )
      {
        feedbackContainer.innerHTML = "<p><i>" + response.data.Musicsheet.title + "</i> wurde eingetragen</p>";
        var trackListLink = document.createElement("a");
        trackListLink.setAttribute('href', "<?php echo Router::url(array('controller' => 'tracks'), true); ?>?event_id=" + event_id);
        trackListLink.innerHTML = "Gespielte Musikstücke";
        feedbackContainer.append(trackListLink);
      }
      else if( response.state == false )
        feedbackContainer.innerHTML = "<p>" + response.message + "</p>";
      else
        feedbackContainer.innerHTML = "";
    }
  };
  xhttp.open("POST", "<?php echo Router::url(array('controller' => 'tracks', 'action' => 'add'), true); ?>", true);
  xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
  xhttp.setRequestHeader("Accept", "text/plain");
  xhttp.setRequestHeader("X-Requested-With", "XMLHttpRequest");
  xhttp.send("data%5BTrack%5D%5Bevent_id%5D=" + event_id + "&data%5BTrack%5D%5Bmusicsheet_id%5D=" + musicsheet_id); 
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
        // 'now' milliseconds as if it was UTC
        now = new Date();
        var now_ms = now.getTime()-(now.getTimezoneOffset()*60000);
        var min_time_diff = Number.MAX_SAFE_INTEGER;

        events = JSON.parse( this.responseText );

        var selectEventDiv = document.createElement("div");
        selectEventDiv.className = "input select";
        container.appendChild(selectEventDiv);
        var selectEventLabel = document.createElement("label");
        selectEventLabel.innerHTML = "Veranstaltung auswählen";
//        selectEventLabel.attributes.for = "selectEvent";
        selectEventDiv.appendChild(selectEventLabel);
        var selectEvent = document.createElement("select");
        selectEvent.id = "selectEvent";
        selectEventDiv.appendChild(selectEvent);
        for( var i = 0; i < events.length; i++ )
        {
          if( events[i].Event.tracks_checked == false )
          {
            // parse start timestamp as UTC
            start = new Date(events[i].Event.start + 'Z');

            var option = document.createElement("option");
            option.value = events[i].Event.id;
            option.text = events[i].Event.name + " -- " + DateToStr(start);
            selectEvent.appendChild(option);

            // find closest (past) event
            if( start.getTime() < now_ms )
            {
              time_diff = now_ms - start.getTime();
              if( time_diff < min_time_diff )
              {
                time_diff = min_time_diff;
                option.selected = true;
              }
            }
          }
        }

        var trackLinkDiv = document.createElement("div");
        trackLinkDiv.className = "submit";
        container.appendChild(trackLinkDiv);
        var trackLink = document.createElement("input");
        trackLink.setAttribute("type", "submit");
        trackLink.value = "Track Music";
        trackLink.onclick = function() {
          var selectEvent = document.getElementById("selectEvent");
          TrackMusicsheet(selectEvent.value, musicsheetId);
        };
        trackLinkDiv.appendChild(trackLink);
      }
    }
  };
  xhttp.open("GET", "<?php echo Router::url(array('controller' => 'events'), true); ?>.json", true);
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
          nameContainer.innerHTML = "<h4>" + json.Musicsheet.title + "</h4>";
          musicsheetId = json.Musicsheet.id;
        }
        else
        {
          musicsheetId = null;
          nameContainer.innerHTML = "<h4>undefined...</h4>";
        }
      }
      else
      {
        last_qr = null;
      }
    }
  };
  xhttp.open("GET", "<?php echo Router::url(array('controller' => 'musicsheets', 'action' => 'view'), true); ?>/" + id + ".json", true);
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

// Square QR box with edge size in percentage of the smaller edge of the viewfinder.
let qrboxFunction = function(viewfinderWidth, viewfinderHeight) {
  let minEdgePercentage = 0.8; // 80%
  let minEdgeSize = Math.min(viewfinderWidth, viewfinderHeight);
  let qrboxSize = Math.floor(minEdgeSize * minEdgePercentage);
  return {
    width: qrboxSize,
    height: qrboxSize
  };
}

var html5QrcodeScanner = new Html5QrcodeScanner(
  "QRreader",
  {
    fps: 10,
    qrbox: 200,
//    qrbox: qrboxFunction,
    formatsToSupport: [ Html5QrcodeSupportedFormats.QR_CODE ],
    supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA]
  });
html5QrcodeScanner.render(onScanSuccess);

</script>

