
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <title>StreetView</title>
    <style type="text/css">
    	html, body{
    		width: 100%;
    		height: 100%;
    		overflow: hidden;
    		margin:0;
    		padding: 0;
    	}
    	#street-view{
    		width: 100%;
    		height: 100%;
    	}
    </style>
</head>

<body>
<div id="street-view"></div>

<script type="text/javascript">

var ROTATE_DEGS_PER_SEC = 12.0;
var ROTATE_DELAY_SECS = 1.5;
var ROTATE_FPS = 100.0; 
var LIMIT_TIME = 9;
var panoLastTouchTimestamp = Date.now();
var masterAnimate = true;
var panoHeadingAngle = 0;

function initPano() {
  setStreetViewPanorama('street-view', 0.0, false);
}

function setStreetViewPanorama(divId, headingAngle, animate) {
  var fenway = {lat: <?php echo $coords['lat']; ?>, lng: <?php echo $coords['lng']; ?>};
  var panoramaOptions = {
    position: fenway,
    pov: {
	    heading:180,
	    pitch: 3,
	   },
    addressControl: false,
    imageDateControl: true,
    zoomControl: false,
    panControl: false,
    fullscreenControl: false,
  };

  var panoDiv = document.getElementById(divId);
  var pano = new google.maps.StreetViewPanorama(panoDiv, panoramaOptions);
  var cycle;
  panoLastTouchTimestamp = Date.now();

  cycle = window.setInterval(function() {
      if (masterAnimate == false) {
        return;
      }
      if (Date.now() < panoLastTouchTimestamp + ROTATE_DELAY_SECS * 1000) {
        return;
      }

      var pov = pano.getPov();
      pov.heading += ROTATE_DEGS_PER_SEC / ROTATE_FPS;
      panoHeadingAngle = pov.heading;
      pano.setPov(pov);
    }, 1000 / ROTATE_FPS);

    pano.addListener('pov_changed', function() {
      var pov = pano.getPov();
      if (panoHeadingAngle != pov.heading) {
        panoLastTouchTimestamp = Date.now();
      }
    });

  if(LIMIT_TIME != 0){
    setTimeout(function(){
      window.clearInterval(cycle);
    },(ROTATE_DELAY_SECS+LIMIT_TIME)*1000);
  }
}

function handleClick(checkbox) {
  masterAnimate = checkbox.checked;
  panoLastTouchTimestamp = 0;
}
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAQGNCgXhTrE7TJROgFSOftaosTVUtqXY8&callback=initPano"></script>
</body>
</html>
