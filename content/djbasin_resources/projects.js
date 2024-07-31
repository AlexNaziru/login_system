function styleClientLinears(json) {
    const att = json.properties;
    switch (att.type) {
        case 'Pipeline':
            return {color:'peru'};
            break;
        case 'Flowline':
            return {color:'navy'};
            break;
        case 'Flowline, est.':
            return {color:'navy', dashArray:"5,5"};
            break;
        case 'Electric Line':
            return {color:'darkgreen'};
            break;
        case 'Access Road - Confirmed':
            return {color:'darkred'};
            break;
        case 'Access Road - Estimated':
            return {color:'darkred', dashArray:"5,5"};
            break;
        case 'Extraction':
            return {color:'indigo'};
            break;
        default:
            return {color:'darkgoldenrod'}
    }
}

function processClientLinears(json, lyr) {
    let att;
    att = json.properties;
    lyr.bindTooltip("<h4>Linear Project: "+att.project+"</h4>Type: "+att.type+"<br>ROW Width: "+att.row_width
        +"<br>Length: "+returnMultiLength(lyr.getLatLngs()).toFixed(0));
    arProjectIDs.push(att.project.toString());

}

function refreshLinears(whr) {
    let objData;
    if (whr) {
        objData = {tbl: "dj_linear_projects", flds: "id, type, row_width, project", where: whr};
    } else {
        objData = {tbl: "dj_linear_projects", flds: "id, type, row_width, project"};
    }
    $.ajax({url: "php/load_data.php",
        data: objData,
        type: "POST",
        success: function (response){
            if (response.substring(0, 5) == "ERROR") {
                alert(response);
            } else {
                // Reset the eagle id layer, it has to be empty before we reload the data
                arProjectIDs = [];
                try {
                    jsonLinears = JSON.parse(response);
                } catch (e) {
                    console.error("Error parsing JSON:", e);
                    console.error("Response:", response);
                    return; // Exit the function if JSON parsing fails
                }

                if (lyrClientLines) {
                    ctlLayers.removeLayer(lyrClientLines);
                    lyrClientLines.remove();
                    lyrClientLinesBuffer.remove();
                }
                lyrClientLinesBuffer = L.featureGroup();
                // Here we recreat it using json
                lyrClientLines = L.geoJSON(jsonLinears, {
                    style: styleClientLinears, onEachFeature: processClientLinears
                }).addTo(mymap);
                // Layer control
                ctlLayers.addOverlay(lyrClientLines, "Linear Projects")
                arProjectIDs.sort(function (a, b) {
                    return a - b
                });
                $("#txtFindProject").autocomplete({
                    source: arProjectIDs
                });
                refreshLinearsBuffers(whr);
            }
        },
        error: function (xhr, status, error) {
            alert("ERROR: "+error)
        }
    });
}

function refreshLinearsBuffers(whr) {
    let objData;
    if (whr) {
        objData = {tbl: "dj_linear_projects", flds: "id, type, row_width, project", distance: "row_width", where: whr};
    } else {
        objData = {tbl: "dj_linear_projects", flds: "id, type, row_width, project", distance: "row_width"};
    }
    $.ajax({url: "php/load_data.php",
        data: objData,
        type: "POST",
        success: function (response){
            if (response.substring(0, 5) == "ERROR") {
                alert(response);
            } else {
                try {
                    jsonLinearBuffers = JSON.parse(response);
                } catch (e) {
                    console.error("Error parsing JSON:", e);
                    console.error("Response:", response);
                    return; // Exit the function if JSON parsing fails
                }

                if (lyrClientLinesBuffer) {
                    lyrClientLinesBuffer.remove();
                }
                lyrClientLinesBuffer = L.geoJSON(jsonLinearBuffers, {
                    style: {
                        color: "grey",
                        dashArray: "5,5",
                        fillOpacity: 0
                    },
                }).addTo(mymap);
                lyrClientLines.bringToFront();
            }
        },
        error: function (xhr, status, error) {
            alert("ERROR: "+error)
        }
    });
}

function findProject(val) {
    returnLayerByAttribute("dj_linear_projects",'project',val,
        function (lyr) {
            if (lyr) {
                if (lyrSearch) {
                    lyrSearch.remove();
                }
                lyrSearch = L.geoJSON(lyr.toGeoJSON(), {style:{color:'red', weight:10, opacity:0.5}}).addTo(mymap);
                mymap.fitBounds(lyr.getBounds().pad(1));
                const att = lyr.feature.properties;
                $("#linear_type").val(att.type);
                $("#linear_row_width").val(att.row_width);
                $("#projectMetadata").html("CREATED "+att.created+" by "+att.createdby+"<br>Modified "+att.modified+" by"+att.modifiedby);
                $("#formProject").show();

                $.ajax({
                    url:'djbasin_resources/php_affected_constraints.php',
                    data:{id:val},
                    type:'POST',
                    success:function(response){
                        $("#divProjectAffected").html(response);
                    },
                    error:function(xhr, status, error){
                        $("#divProjectAffected").html("ERROR: "+error);
                    }
                });
            } else {
                $("#divProjectError").html("**** Project ID not found ****");
            }
        }
    );
}