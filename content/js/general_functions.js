//  ***********  General Functions *********

function LatLngToArrayString(ll) {
    return "["+ll.lat.toFixed(5)+", "+ll.lng.toFixed(5)+"]";
}

// Async function
function returnLayerByAttribute(tbl,fld,val, callback) {
    let arLyrs;
    // server side from the database
    let whr = fld+"='"+val+"'";
    $.ajax({
        url: "php/load_data.php",
        data: {tbl: tbl, where: whr},
        type: "POST",
        success: function (response) {
            if (response.substring(0,5)=="ERROR") {
                alert(response);
                callback(false);
            } else {
                const jsn = JSON.parse(response);
                const lyr = L.geoJSON(jsn);
                arLyrs = lyr.getLayers();
                if (arLyrs.length > 0) {
                    callback(arLyrs[0]);
                } else {
                    callback(false);
                }
            }
        },
        error: function (xhr, status, error) {
            alert("ERROR: "+error);
            callback(false);
        }
    });
}

function returnLayersByAttribute(lyr,att,val) {
    const arLayers = lyr.getLayers();
    let arMatches = [];
    for (i=0;i<arLayers.length-1;i++) {
        const ftrVal = arLayers[i].feature.properties[att];
        if (ftrVal==val) {
            arMatches.push(arLayers[i]);
        }
    }
    if (arMatches.length) {
        return arMatches;
    } else {
        return false;
    }
}

function testLayerAttribute(ar, val, att, fg, err, btn) {
    if (ar.indexOf(val)<0) {
        $(fg).addClass("has-error");
        $(err).html("**** "+att+" NOT FOUND ****");
        $(btn).attr("disabled", true);
    } else {
        $(fg).removeClass("has-error");
        $(err).html("");
        $(btn).attr("disabled", false);
    }
}

function returnRecordByID(tbl, id, callback) {
    // server side from the database
    const whr = "id='"+id+"'";
    $.ajax({
        url: "php/load_data.php",
        data: {tbl: tbl, where: whr, spatial: "NO"},
        type: "POST",
        success: function (response) {
            if (response.substring(0,5)=="ERROR") {
                alert(response);
                callback(false);
            } else {
                // jsn is an array
                const jsn = JSON.parse(response);
                if (jsn.length > 0) {
                    callback(jsn[0].properties);
                } else {
                    callback(false);
                }
            }
        },
        error: function (xhr, status, error) {
            alert("ERROR: "+error);
            callback(false);
        }
    });
}

// Function for the length of the line, in order to edit polygons
function returnLength(arLL) {
    let total = 0;
    for (let i = 1; i < arLL.length; i++) {
        // i -1 is 0, this will give us the distance between the first and the 2nd lat long, and so on
        total += arLL[i-1].distanceTo(arLL[i]);
    }
    // after it's finished running the lat longs in the array, we will have the sum of all the intervals and that's going to be our total length
    return total;
}
// This will take an array of arrays [[],[]]
function returnMultiLength(arArLL) {
    let total = 0;
    for (let i = 0; i < arArLL.length; i++) {
        total += returnLength(arArLL[i]);
    }
    return total;
}
// Calculate the closest layer
function returnClosestLayer(lyrGroup, llRef) {
    const arLyrs = lyrGroup.getLayers();
    const nearest = L.GeometryUtil.closestLayer(mymap, arLyrs, llRef);
    nearest.distance = llRef.distanceTo(nearest.latlng);
    nearest.bearing = L.GeometryUtil.bearing(llRef, nearest.latlng);
    if (nearest.bearing < 0) {
        nearest.bearing = nearest.bearing + 360;
    }
    nearest.att = nearest.layer.feature.properties;
    return nearest;
}

// Summarize points
function summarizePointsByLine(line, radius, fcPoints, prop) {
    let buf;
    buf = turf.buffer(line, radius, "kilometers");
    buf = turf.featureCollection([buf]);
    buf = turf.collect(buf, fcPoints, prop, prop+"Vals");
    return buf;
}

// Polygone intersection
function intersectPolyByPolyFC(poly, fcPoly) {
    let fgp = [];
    const bbPoly = turf.bboxPolygon(turf.bbox(poly));
    for (let i = 0; i < fcPoly.features.length; i ++) {
        const bb = turf.bboxPolygon(turf.bbox(fcPoly.features[i]));
        if (turf.intersect(bbPoly, bb)) {
            const int = turf.intersect(poly, fcPoly.features[i]);
            if (int) {
                int.properties = fcPoly.features[i].properties;
                fgp.push(int);
            }
        }
    }
    return turf.featureCollection(fgp);
}

// Intersecting Polygons with lines
function intersectLineByPolyFC(line, fcPoly) {
    let fgp = [];
    const bbLine = turf.bboxPolygon(turf.bbox(line));
    for (let i = 0; i <fcPoly.features.length; i++) {
        const bb = turf.bboxPolygon(turf.bbox(fcPoly.features[i]));
        if (turf.intersect(bbLine, bb)) {
            const slice = turf.lineSplit(line, fcPoly.features[i]);
            for (let j = 0; j < slice.features.length; j ++) {
                const curSlice = slice.features[j];
                const length = turf.lineDistance(curSlice, "kilometers");
                const pdMiddle = turf.along(curSlice, length/2, "kilometers" );
                if (turf.inside(pdMiddle, fcPoly.features[i])) {
                    curSlice.properties = fcPoly.features[i].properties;
                    fgp.push(curSlice);
                }
            }
        }
    }
    return turf.featureCollection(fgp)
}
function summarizeLineFC(fcLine, att) {
    let arUnique = [];
    let arCount = [];
    let arLength = [];
    for (let i = 0; i < fcLine.features.length; i++) {
        const curAtt = fcLine.features[i].properties[att];
        const idx = arUnique.indexOf(curAtt);
        if (idx < 0) {
            arUnique.push(curAtt);
            arCount.push(1);
            arLength.push(turf.lineDistance(fcLine.features[i]));
        } else {
            arCount[idx] = arCount[idx] + 1;
            arLength[idx] = arLength[idx] + turf.lineDistance(fcLine.features[i])
        }
    }
    return [arUnique, arCount, arLength];
}

function summarizeArray(ar) {
    let arUnique = [];
    let arCount = [];
    for (let i = 0; i < ar.length; i++) {
        const idx = arUnique.indexOf(ar[i]);
        if (idx < 0) {
            arUnique.push(ar[i]);
            arCount.push(1);
        } else {
            arCount[idx] = arCount[idx] + 1;
        }
    }
    return [arUnique, arCount];
}

function summarizePolyFC(fcPoly, att) {
    let arUnique = [];
    let arCount = [];
    let arAreas = [];
    for (let i = 0; i < fcPoly.features.length; i++) {
        const curAtt = fcPoly.features[i].properties[att];
        const idx = arUnique.indexOf(curAtt);
        if (idx < 0) {
            arUnique.push(curAtt);
            arCount.push(1);
            arAreas.push(turf.area(fcPoly.features[i]));
        } else {
            arCount[idx] = arCount[idx] + turf.area(fcPoly.features[i]);
        }
    }
    return [arUnique, arCount, arAreas];
}

function stripSpaces(str) {
    return str.replace(/\s+/g,'');
}

// Returning the current date
function returnCurrentDate() {
    const currentDate = new Date();

    let currentDay = currentDate.getDate();
    // less than 10 means only two digits.
    if (currentDay < 10 ){currentDay = "0"+currentDay}

    let currentMonth = currentDate.getMonth() + 1; // Add 1 to the month value bc it starts from 0;
    if (currentMonth < 10 ){currentMonth = "0"+currentMonth}

    let currentYear = currentDate.getFullYear(); // Here we need the full 4 digits of the year

    return currentYear+"-"+currentMonth+"-"+currentDay;
}

// Updating the db
function returnFormData(inpClass) {
    let objFormData = {};
    $("."+inpClass).each(function () {
        objFormData[this.name] = this.value;
    });
    return objFormData;
}