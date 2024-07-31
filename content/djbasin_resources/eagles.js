function returnEagleMarker(json, latlng){
    const att = json.properties;
    if (att.status=='ACTIVE NEST') {
        var clrNest = 'deeppink';
    } else {
        var clrNest = 'chartreuse';
    }
    arEagleIDs.push(att.nest_id.toString());
    return L.circle(latlng, {radius:804, color:clrNest,fillColor:'chartreuse', fillOpacity:0.5}).bindTooltip("<h4>Eagle Nest: "+att.nest_id+"</h4>Status: "+att.status);
}

// whr parameter means where. The where clause is for filterin sql. From tbl select x where ...
function refreshEagles(whr) {
    /* Radio button */
    let objData;
    if (whr) {
        objData = {tbl: "dj_eagle", flds: "id, status, nest_id", where: whr};
    } else {
        // if there is no where clause, returning everything from the database
        objData = {tbl: "dj_eagle", flds: "id, status, nest_id"};
    }
    $.ajax({url: "php/load_data.php",
        data: objData,
        type: "POST",
        success: function (response){
            // Handling db errors. If we do not get json back
            if (response.substring(0,5) == "ERROR") {
                alert(response);
            } else {
                // Reset the eagle id layer, it has to be empty before we reload the data
                arEagleIDs = [];
                jsonEagles = JSON.parse(response);
                if (lyrEagleNests) {
                    ctlLayers.removeLayer(lyrEagleNests);
                    lyrEagleNests.remove();
                }
                lyrEagleNests = L.geoJSON(jsonEagles, {pointToLayer:returnEagleMarker}).addTo(mymap);
                // Layer control
                ctlLayers.addOverlay(lyrEagleNests, "Eagle Nests")
                arEagleIDs.sort(function(a,b){return a-b});
                $("#txtFindEagle").autocomplete({
                    source:arEagleIDs
                });
            }
        },
        error: function (xhr, status, error) {
            alert("ERROR: "+error)
        }
    });
}

function findEagle(val) {
    returnLayerByAttribute("dj_eagle",'nest_id',val,
        function (lyr) {
            if (lyr) {
                if (lyrSearch) {
                    lyrSearch.remove();
                }
                lyrSearch = L.circle(lyr.getLatLng(), {
                    radius: 800,
                    color: 'red',
                    weight: 10,
                    opacity: 0.5,
                    fillOpacity: 0
                }).addTo(mymap);
                mymap.setView(lyr.getLatLng(), 14);
                const att = lyr.feature.properties;
                $("#eagle_status").val(att.status);
                $("#eagle_lastsurvey").val(att.lastsurvey);
                $("#eagleMetadata").html("CREATED "+att.created+" by "+att.createdby+"<br>Modified "+att.modified+" by"+att.modifiedby);
                $("#formEagle").show();

                $.ajax({
                    url: "djbasin_resources/php_basin_affected_projects.php",
                    data: {tbl: "dj_eagle", distance: 804, fld: "nest_id", id:val},//meters
                    type: "POST",
                    success: function (response) {
                        $("#divEagleAffected").html(response);
                    },
                    error: function (xhr, status, error) {
                        $("#divEagleAffected").html("ERROR: "+error);
                    }
                });

                $("#divEagleError").html("");

                // Selecting the survey button
                $("#btnEagleSurveys").show();
            } else {
                $("#divEagleError").html("**** Eagle Nest ID not found ****");
            }
        })
}