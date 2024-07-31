function styleBUOWL(json){
    var att = json.properties;
    switch (att.hist_occup){
        case 'Yes':
            return {color:'deeppink', fillColor:'yellow'};
            break;
        case 'Undetermined':
            return {color:'yellow'};
            break;
    }
}

function processBUOWL(json, lyr){
    var att = json.properties;
    lyr.bindTooltip("<h4>Habitat ID: "+att.habitat_id+"</h4>Historically Occupied: "+att.hist_occup+"<br>Status: "+att.recentstatus);
    arHabitatIDs.push(att.habitat_id.toString())
}

function refreshBUOWL(whr) {
    // filtering
    let objData;
    if (whr) {
        objData = {tbl: "dj_buowl", flds: "id, habitat_id, habitat, recentstatus, hist_occup", where: whr};
    } else {
        objData = {tbl: "dj_buowl", flds: "id, habitat_id, habitat, recentstatus, hist_occup"};
    }
    $.ajax({url: "php/load_data.php",
        data: objData,
        type: "POST",
        success: function (response){
            if (response.substring(0, 5) == "ERROR") {
                alert(response);
            } else {
                // Reset the eagle id layer, it has to be empty before we reload the data
                arHabitatIDs = [];
                jsonBUOWL = JSON.parse(response);
                if (lyrBUOWL) {
                    ctlLayers.removeLayer(lyrBUOWL);
                    lyrBUOWL.remove();
                    lyrBUOWLbuffer.remove();
                }
                lyrBUOWL = L.geoJSON(jsonBUOWL, {
                    style: styleBUOWL,
                    onEachFeature: processBUOWL
                }).addTo(mymap);
                // Layer control
                ctlLayers.addOverlay(lyrBUOWL, "Burrowing Owl Habitat")
                arHabitatIDs.sort(function (a, b) {
                    return a - b
                });
                $("#txtFindBUOWL").autocomplete({
                    source: arHabitatIDs
                });
                refreshBUOWLBuffer(whr);
            }
        },
        error: function (xhr, status, error) {
            alert("ERROR: "+error)
        }
    });
}

// getting buffer data
function refreshBUOWLBuffer(whr) {
    let objData;
    if (whr) {
        objData = {tbl: "dj_buowl", flds: "id, habitat_id, habitat, recentstatus, hist_occup", where: whr , distance: 300};// distance is for the buffer
    } else {
        objData = {tbl: "dj_buowl", flds: "id, habitat_id, habitat, recentstatus, hist_occup", distance: 300};
    }
    $.ajax({url: "php/load_data.php",
        data: objData,
        type: "POST",
        success: function (response){
            if (response.substring(0, 5) == "ERROR") {
                alert(response);
            } else {
                jsonBUOWLbuffer = JSON.parse(response);
                if (lyrBUOWLbuffer) {
                    lyrBUOWLbuffer.remove();
                }
                lyrBUOWLbuffer = L.geoJSON(jsonBUOWLbuffer, {
                    style: {
                        color: "hotpink",
                        dashArray: "5,5",
                        fillOpacity: 0
                    },
                }).addTo(mymap);
                lyrBUOWL.bringToFront();
            }
        },
        error: function (xhr, status, error) {
            alert("ERROR: "+error)
        }
    });
}

function findBUOWL(val) {
    returnLayerByAttribute("dj_buowl",'habitat_id',val,
        function (lyr) {
            if (lyr) {
                if (lyrSearch) {
                    lyrSearch.remove();
                }
                lyrSearch = L.geoJSON(lyr.toGeoJSON(), {
                    style: {
                        color: 'red',
                        weight: 10,
                        opacity: 0.5,
                        fillOpacity: 0
                    }
                }).addTo(mymap);
                mymap.fitBounds(lyr.getBounds().pad(1));
                const att = lyr.feature.properties;
                $("#buowl_habitat").val(att.habitat);
                $("#buowl_hist_occup").val(att.hist_occup);
                $("#buowl_recentstatus").val(att.recentstatus);
                $("#buowl_lastsurvey").val(att.lastsurvey);
                $("#BUOWLmetadata").html("CREATED "+att.created+" by "+att.createdby+"<br>Modified "+att.modified+" by"+att.modifiedby);
                // Turning the form on
                $("#formBUOWL").show();

                $.ajax({
                    url: "djbasin_resources/php_basin_affected_projects.php",
                    data: {tbl: "dj_buowl", distance: 300, fld: "habitat_id", id: val},
                    type: "POST",
                    success: function (response) {
                        $("#divBUOWLAffected").html(response);
                    },
                    error: function (xhr, status, error) {
                        $("#divBUOWLAffected").html("ERROR: "+error);
                    }
                });

                $("#divBUOWLError").html("");

                // Editing geometries. Leaflet Draw function doesn't handle polygons. But there are way to get around this.
                /* fgpDrawnItems.clearLayers();
                 fgpDrawnItems.addLayer(lyr);*/

                // Selecting the survey button
                $("#btnBUOWLsurveys").show();
            } else {
                $("#divBUOWLError").html("**** Habitat ID not found ****");
            }
        })
}