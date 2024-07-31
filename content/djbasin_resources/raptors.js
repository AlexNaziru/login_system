function returnRaptorMarker(json, latlng){
    let radRaptor;
    let optRaptor;
    let att = json.properties;
    arRaptorIDs.push(att.nest_id.toString());
    switch (att.recentspecies) {
        case 'Red-tail Hawk':
            radRaptor = 533;
            break;
        case 'Swainsons Hawk':
            radRaptor = 400;
            break;
        default:
            radRaptor = 804;
            break;
    }
    switch (att.recentstatus) {
        case 'ACTIVE NEST':
            optRaptor = {radius:radRaptor, color:'deeppink', fillColor:"cyan", fillOpacity:0.5};
            break;
        case 'INACTIVE NEST':
            optRaptor = {radius:radRaptor, color:'cyan', fillColor:'cyan', fillOpacity:0.5};
            break;
        case 'FLEDGED NEST':
            optRaptor = {radius:radRaptor, color:'deeppink', fillColor:"cyan", fillOpacity:0.5, dashArray:"2,8"};
            break;
    }
    return L.circle(latlng, optRaptor).bindPopup("<h4>Raptor Nest: "+att.nest_id+"</h4>Status: "+att.recentstatus+"<br>Species: "+att.recentspecies+"<br>Last Survey: "+att.lastsurvey);
}

function findRaptor(val) {
    let radRaptor;
    returnLayerByAttribute("dj_raptor",'nest_id',val,
        function (lyr) {
            if (lyr) {
                if (lyrSearch) {
                    lyrSearch.remove();
                }
                const att = lyr.feature.properties;
                switch (att.recentspecies) {
                    case 'Red-tail Hawk':
                        radRaptor = 533;
                        break;
                    case 'Swainsons Hawk':
                        radRaptor = 400;
                        break;
                    default:
                        radRaptor = 804;
                        break;
                }
                lyrSearch = L.circle(lyr.getLatLng(), {
                    radius: radRaptor,
                    color: 'red',
                    weight: 10,
                    opacity: 0.5,
                    fillOpacity: 0
                }).addTo(mymap);
                mymap.setView(lyr.getLatLng(), 14);
                $("#raptor_recentspecies").val(att.recentspecies);
                $("#raptor_recentstatus").val(att.recentstatus);
                $("#raptor_lastsurvey").val(att.lastsurvey);
                $("#raptorMetadata").html("CREATED "+att.created+" by "+att.createdby+"<br>Modified "+att.modified+" by"+att.modifiedby);
                $("#formRaptor").show();
                $("#divRaptorError").html("");

                $.ajax({
                    url: "djbasin_resources/php_basin_affected_projects.php",
                    data: {tbl: "dj_raptor", distance: radRaptor, fld: "nest_id", id:val},//meters
                    type: "POST",
                    success: function (response) {
                        $("#divRaptorAffected").html(response);
                    },
                    error: function (xhr, status, error) {
                        $("#divRaptorAffected").html("ERROR: "+error);
                    }
                });

                // Selecting the survey button
                $("#btnRaptorSurveys").show();
            } else {
                $("#divRaptorError").html("**** Raptor Nest ID not found ****");
            }
        })
}

function refreshRaptors(whr) {
    let objData;
    if (whr) {
        objData = {tbl: "dj_raptor", flds: "id, nest_id, recentstatus, recentspecies, lastsurvey", where: whr}
    } else {
        objData = {tbl: "dj_raptor", flds: "id, nest_id, recentstatus, recentspecies, lastsurvey"}
    }
    $.ajax({
        url: "php/load_data.php",
        data: objData,
        type: "POST",
        success: function (response) {
            if (response.substring(0, 5) == "ERROR") {
                alert(response);
            } else {
                // Reset the eagle id layer, it has to be empty before we reload the data
                arRaptorIDs = [];
                jsonRaptor = JSON.parse(response);
                if (lyrMarkerCluster) {
                    ctlLayers.removeLayer(lyrMarkerCluster);
                    lyrMarkerCluster.remove();
                    lyrRaptorNests.remove();
                }
                lyrRaptorNests = L.geoJSON(jsonRaptor, {
                    pointToLayer: returnRaptorMarker
                });

                arRaptorIDs.sort(function (a, b) {
                    return a - b
                });
                $("#txtFindRaptor").autocomplete({
                    source: arRaptorIDs
                });
                lyrMarkerCluster = L.markerClusterGroup();
                lyrMarkerCluster.addLayer(lyrRaptorNests);
                lyrMarkerCluster.addTo(mymap);
                // Layer control
                ctlLayers.addOverlay(lyrMarkerCluster, "Raptor Nests")
            }
        },
        error: function (xhr, status, error) {
            alert("ERROR: " + error)
        }
    });
}