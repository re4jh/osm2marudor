function onMarkerClick(e, values) {
  var popup = e.target.getPopup();
  popup.setContent("<b>" + values.name + ":</b><br/><span class='loading'>&#9203;</span>")

  $.getJSON("marudor_cache/hafasDepartureStationBoard.php?station=" + values.id, function(json) {
    popup_content = "<b>" + values.name + "</b>";
    departure_data = json;
    popup_content += '<ul class="public_transport">';
    max = 100;
    if (departure_data.length < max) {
      max = departure_data.length;
    }
    for (i = 0; i < max; i++) {
      var dep_date = new Date(departure_data[i].departure.scheduledTime);
      hours = "0" + dep_date.getHours();
      minutes = "0" + dep_date.getMinutes();
      fTime = hours.substr(-2) + ':' + minutes.substr(-2);
      cssclass = "time_ok";
      delaytxt = '';
      if (departure_data[i].departure.delay) {
        cssclass = "time_nok";
        delaytxt = '<span class="delay">&#8986; +' + departure_data[i].departure.delay + '</span>';
      }

      platformtxt = '';
      if (departure_data[i].departure.platform) {
        platformtxt = '<span class="platform">&#9648; ' + departure_data[i].departure.platform + '</span>';
      }

      popup_content += '<li class="' + cssclass + '">';
      popup_content += '<span class="time">' + fTime + '</span>: ';
      if (departure_data[i].train.line) {
        popup_content += '<span class="line">[' + departure_data[i].train.line + ']</span> ';
      }
      popup_content += departure_data[i].finalDestination;
      popup_content += delaytxt;
      popup_content += platformtxt;
      popup_content += '</li>';
    }
    popup_content += '</ul>';
    popup.setContent(popup_content);
  });
}

function onBikeMarkerClick(e, values) {
  var popup = e.target.getPopup();
  popup.setContent("<b>" + values.name + ":</b><br/><span class='loading'>&#9203;</span>")

  $.getJSON("bike_cache/tws.php?area=" + values.id, function(jsontws) {
    popup_content = "<b>" + values.name + ":</b>";
    station_data = jsontws;
    if (station_data.sharingAvailability && station_data.sharingAvailability.vehicles && station_data.sharingAvailability.vehicles.length > 0) {
      popup_content += '<ul class="bikesharing">';
      max = 100;
      if (station_data.sharingAvailability.vehicles.length < max) {
        max = station_data.sharingAvailability.vehicles.length;
      }
      for (i = 0; i < max; i++) {
        var bike_data = station_data.sharingAvailability.vehicles[i];
        type = '';
        if (bike_data.vehicleType = 'bike') {
          type += '&#128690;';
        }

        chargingstate = '?';
        if (bike_data.stateOfCharge && $.isNumeric(bike_data.stateOfCharge)) {
          chargingstate = '&#128267; ' + bike_data.stateOfCharge + ' %';
        }
        popup_content += '<li>' + type + '<span class="chargingstate">' + chargingstate + '</span></li>';
      }
      popup_content += '</ul>';
    }
    popup.setContent(popup_content);

  });
}

function locateUser(mymap) {
  mymap.locate({setView: true,maxZoom: 16});
}
