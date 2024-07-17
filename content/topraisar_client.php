<?php include("../includes/init.php");?>
<?php
//If they are logged in, they can see this page
if (logged_in()) {
    $username = $_SESSION["username"];
    error_log("Logged in user: " . $username);
    // Checking to see if the user is a member of the group
    if (!verify_user_group($pdo, $username, "Topraisar Farmers")) {
        set_msg("User '{$username}' does not have permission to view this page");
        error_log("User '{$username}' does not have permission to view this page");
        redirect("../index.php");
    }
} else {
    set_msg("Please log in and try again!");
    error_log("User is not logged in.");
    // If they are not logged in, they won't
    redirect("../index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Webmap 201</title>
        <link rel="stylesheet" href="src/leaflet.css">
        <link rel="stylesheet" href="src/css/bootstrap.css">
        <link rel="stylesheet" href="src/plugins/L.Control.MousePosition.css">
        <link rel="stylesheet" href="src/plugins/L.Control.Sidebar.css">
        <link rel="stylesheet" href="src/plugins/Leaflet.PolylineMeasure.css">
        <link rel="stylesheet" href="src/plugins/easy-button.css">
        <link rel="stylesheet" href="src/plugins/leaflet-styleeditor/css/Leaflet.StyleEditor.css">
        <link rel="stylesheet" href="src/css/font-awesome.min.css">
        <link rel="stylesheet" href="src/plugins/leaflet.awesome-markers.css">
        <link rel="stylesheet" href="src/plugins/leaflet-mapkey/MapkeyIcons.css">
        <link rel="stylesheet" href="src/plugins/leaflet-mapkey/L.Icon.Mapkey.css">
        <link rel="stylesheet" href="src/plugins/MarkerCluster.css">
        <link rel="stylesheet" href="src/plugins/MarkerCluster.Default.css">
        <link rel="stylesheet" href="src/jquery-ui.min.css">
        <link rel="stylesheet" href="src/plugins/leaflet-legend.css">
        
        <script src="src/leaflet-src.js"></script>
        <script src="src/jquery-3.2.0.min.js"></script>
        <script src="src/plugins/L.Control.MousePosition.js"></script>
        <script src="src/plugins/L.Control.Sidebar.js"></script>
        <script src="src/plugins/Leaflet.PolylineMeasure.js"></script>
        <script src="src/plugins/easy-button.js"></script>
        <script src="src/plugins/leaflet-providers.js"></script>
        <script src="src/plugins/leaflet-styleeditor/javascript/Leaflet.StyleEditor.js"></script>
        <script src="src/plugins/leaflet-styleeditor/javascript/Leaflet.StyleForms.js"></script>
        <script src="src/plugins/leaflet.ajax.min.js"></script>
        <script src="src/plugins/leaflet.sprite.js"></script>
        <script src="src/plugins/leaflet.awesome-markers.min.js"></script>
        <script src="src/plugins/leaflet-mapkey/L.Icon.Mapkey.js"></script>
        <script src="src/plugins/leaflet.markercluster.js"></script>
        <script src="src/plugins/leaflet.geometryutil.js"></script>
        <script src="src/turf.min.js"></script>
        <script src="src/jquery-ui.min.js"></script>
        <script src="src/plugins/leaflet-legend.js"></script>
        
<!--    ***************  Begin Leaflet.Draw-->
        
        <script src="src/plugins/leaflet-draw/Leaflet.draw.js"></script>
        <script src="src/plugins/leaflet-draw/Leaflet.Draw.Event.js"></script>
        <link rel="stylesheet" href="src/plugins/leaflet-draw/leaflet.draw.css"/>

        <script src="src/plugins/leaflet-draw/Toolbar.js"></script>
        <script src="src/plugins/leaflet-draw/Tooltip.js"></script>

        <script src="src/plugins/leaflet-draw/ext/GeometryUtil.js"></script>
        <script src="src/plugins/leaflet-draw/ext/LatLngUtil.js"></script>
        <script src="src/plugins/leaflet-draw/ext/LineUtil.Intersect.js"></script>
        <script src="src/plugins/leaflet-draw/ext/Polygon.Intersect.js"></script>
        <script src="src/plugins/leaflet-draw/ext/Polyline.Intersect.js"></script>
        <script src="src/plugins/leaflet-draw/ext/TouchEvents.js"></script>

        <script src="src/plugins/leaflet-draw/draw/DrawToolbar.js"></script>
        <script src="src/plugins/leaflet-draw/draw/handler/Draw.Feature.js"></script>
        <script src="src/plugins/leaflet-draw/draw/handler/Draw.SimpleShape.js"></script>
        <script src="src/plugins/leaflet-draw/draw/handler/Draw.Polyline.js"></script>
        <script src="src/plugins/leaflet-draw/draw/handler/Draw.Circle.js"></script>
        <script src="src/plugins/leaflet-draw/draw/handler/Draw.Marker.js"></script>
        <script src="src/plugins/leaflet-draw/draw/handler/Draw.Polygon.js"></script>
        <script src="src/plugins/leaflet-draw/draw/handler/Draw.Rectangle.js"></script>


        <script src="src/plugins/leaflet-draw/edit/EditToolbar.js"></script>
        <script src="src/plugins/leaflet-draw/edit/handler/EditToolbar.Edit.js"></script>
        <script src="src/plugins/leaflet-draw/edit/handler/EditToolbar.Delete.js"></script>

        <script src="src/plugins/leaflet-draw/Control.Draw.js"></script>

        <script src="src/plugins/leaflet-draw/edit/handler/Edit.Poly.js"></script>
        <script src="src/plugins/leaflet-draw/edit/handler/Edit.SimpleShape.js"></script>
        <script src="src/plugins/leaflet-draw/edit/handler/Edit.Circle.js"></script>
        <script src="src/plugins/leaflet-draw/edit/handler/Edit.Rectangle.js"></script>
        <script src="src/plugins/leaflet-draw/edit/handler/Edit.Marker.js"></script>
        
<!--    **************  End of Lealet Draw-->

        <style>
            #mapdiv {
                height:100vh;
            }

            .col-xs-12, .col-xs-6, .col-xs-4 {
                padding:3px;
            }

            #divProject {
                background-color: beige;
            }
            
            #divBUOWL {
                background-color: #ffffb3;
            }
            
            #divEagle {
                background-color: #ccffb3;
            }
            
            #divRaptor {
                background-color: #e6ffff;
            }
            
            .errorMsg {
                padding:0;
                text-align:center;
                background-color:darksalmon;
            }
            
        </style>
    </head>
    <body>
        <div id="side-bar" class="col-md-3">
            <button id='btnLocate' class='btn btn-primary btn-block'>Locate</button><br>
            <!-- Map Legend -->
            <button id="btnShowLegend" class="btn btn-success btn-block ">Show Legend</button>
            <div id="legend">
                <div id="lgndClientLinears">
                    <h4 class="text-center">Linear Projects - Legend <i id="btnLinearProjects" class="fa fa-server"></i></h4>
                    <div id="legendLinearProjectDetails">
                        <svg height="270" width="100%">
                            <!-- Pipeline -->
                            <line x1="10" y1="10" x2="40" y2="10" style="stroke: peru; stroke-width: 2;"/>
                            <text x="50" y="15" style="font-family: sans-serif; font-size: 16px;">Pipeline</text>
                            <!-- Flowline -->
                            <line x1="10" y1="40" x2="40" y2="40" style="stroke: navy; stroke-width: 2;"/>
                            <text x="50" y="45" style="font-family: sans-serif; font-size: 16px;">Flowline</text>
                            <!-- Flowline - estimated  -->
                            <line x1="10" y1="70" x2="40" y2="70" style="stroke: navy; stroke-width: 2; stroke-dasharray: 5,5;"/>
                            <text x="50" y="75" style="font-family: sans-serif; font-size: 16px;">Flowline - estimated</text>
                            <!-- Electric Lines -->
                            <line x1="10" y1="100" x2="40" y2="100" style="stroke: darkgreen; stroke-width: 2;"/>
                            <text x="50" y="105" style="font-family: sans-serif; font-size: 16px;">Electric Lines</text>
                            <!-- Access Road - Confirmed -->
                            <line x1="10" y1="130" x2="40" y2="130" style="stroke: darkred; stroke-width: 2;"/>
                            <text x="50" y="135" style="font-family: sans-serif; font-size: 16px;">Access Road - Confirmed</text>
                            <!-- Access Road - Estimated -->
                            <line x1="10" y1="160" x2="40" y2="160" style="stroke: darkred; stroke-width: 2; stroke-dasharray: 5,5;"/>
                            <text x="50" y="165" style="font-family: sans-serif; font-size: 16px;">Access Road - Estimated</text>
                            <!-- Extraction -->
                            <line x1="10" y1="190" x2="40" y2="190" style="stroke: indigo; stroke-width: 2;"/>
                            <text x="50" y="195" style="font-family: sans-serif; font-size: 16px;">Extraction</text>
                            <!-- Others -->
                            <line x1="10" y1="220" x2="40" y2="220" style="stroke: darkgoldenrod; stroke-width: 2;"/>
                            <text x="50" y="225" style="font-family: sans-serif; font-size: 16px;">Other</text>
                            <!-- Right of way  -->
                            <rect x="10" y="240" width="30" height="20" style="stroke: gray; stroke-width: 4; stroke-dasharray: 5,5;
                                    fill: yellow; fill-opacity: 0.0;"/>
                            <text x="50" y="255" style="font-family: sans-serif; font-size: 16px;">Right-of-way</text>
                        </svg>
                    </div>
                </div>
                <div id="lgndBurrowingOwlHabitat">
                    <h4 class="text-center">Burrowing Owl Habitat <i id="btnBUOWL" class="fa fa-server"></i></h4>
                    <div id="legendBUOWLDetails">
                        <svg height="90">
                            <rect x="10" y="5" width="30" height="20" style="stroke-width: 4; stroke: deeppink; fill: yellow; fill-opacity: 0.5"/>
                            <text x="50" y="20" style="font-family: sans-serif; font-size: 16px;">Historically Occupied</text>
                            <rect x="10" y="35" width="30" height="20" style="stroke-width: 4; stroke: yellow; fill: yellow; fill-opacity: 0.5;"/>
                            <text x="50" y="50" style="font-family: sans-serif; font-size: 16px;">Not Historically Occupied</text>
                            <rect x="10" y="65" width="30" height="20" style="stroke-width: 4; stroke: yellow; stroke-dasharray: 5,5;
                                    fill: yellow; fill-opacity: 0.0;"/>
                            <text x="50" y="80" style="font-family: sans-serif; font-size: 16px;">300m Buffer Zone</text>
                        </svg>
                    </div>
                </div>
                <div id="lgndEagleNest">
                    <h4 class="text-center">Eagle Nests <i id="btnEagle" class="fa fa-server"></i></h4>
                    <div id="lgndEagleDetail">
                        <svg height="60">
                            <circle cx="25" cy="15" r="10" style="stroke-width: 4; stroke: deeppink; fill: chartreuse; fill-opacity:0.5;"/>
                            <text x="50" y="20" style="font-family: sans-serif; font-size: 16px;">Active Nest</text>
                            <circle cx="25" cy="45" r="10" style="stroke-width: 4; stroke: chartreuse; fill: chartreuse; fill-opacity:0.5;"/>
                            <text x="50" y="50" style="font-family: sans-serif; font-size: 16px;">Unknown status</text>
                        </svg>
                    </div>
                </div>
                <div id="lgndRaptorNest">
                    <h4 class="text-center">Raptor Nests <i id="btnRaptor" class="fa fa-server"></i></h4>
                    <div id="lgndRaptorDetail">
                        <svg height="90">
                            <circle cx="25" cy="15" r="10" style="stroke-width: 4; stroke: deeppink; fill: cyan; fill-opacity:0.5;"/>
                            <text x="50" y="20" style="font-family: sans-serif; font-size: 16px;">Active Nest</text>
                            <circle cx="25" cy="45" r="10" style="stroke-width: 4; stroke: deeppink; stroke-dasharray: 5, 5; fill: cyan; fill-opacity:0.5;"/>
                            <text x="50" y="50" style="font-family: sans-serif; font-size: 16px;">Fledged Nest</text>
                            <circle cx="25" cy="75" r="10" style="stroke-width: 4; stroke: cyan; fill: cyan; fill-opacity:0.5;"/>
                            <text x="50" y="80" style="font-family: sans-serif; font-size: 16px;">Unknown status</text>
                        </svg>
                    </div>
                </div>
                <div id="lgndGBHRookeries">
                    <h4 class="text-center">Heron Rookeries <i id="btnGBH" class="fa fa-server"></i></h4>
                    <div id="lgndGBHDetail">
                        <svg height="40">
                            <rect x="10" y="5" width="30" height="20" style="stroke-width: 4; stroke: fuchsia; fill: fuchsia; fill-opacity:0.5;"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div id="divProject" class="col-xs-12">
                <div id="divProjectLabel" class="text-center col-xs-12">
                    <h4 id="lblProject">Linear Projects</h4>
                </div>
                <div id="divProjectError" class="errorMsg col-xs-12"></div>
                <div id="divFindProject" class="form-group has-error">
                    <div class="col-xs-6">
                        <input type="text" id="txtFindProject" class="form-control" placeholder="Project ID">
                    </div>
                    <div class="col-xs-6">
                        <button id="btnFindProject" class="btn btn-primary btn-block" disabled>Find Project</button>
                    </div>
                </div>
                <div class="col-xs-12" id="#divFilterProject">
                    <div class="col-xs-4">
                        <input type="checkbox" name="fltProject" value="Pipeline" checked>Pipeline<br>
                        <input type="checkbox" name="fltProject" value="Road" checked>Access Roads
                        <button class="btn btn-block btn-primary" id="btnProjectFilterAll">Check All</button>
                    </div>
                    <div class="col-xs-4">
                        <input type="checkbox" name="fltProject" value="Electric" checked>Electric Lines<br>
                        <input type="checkbox" name="fltProject" value="Extraction" checked>Extractions
                        <button class="btn btn-block btn-primary" id="btnProjectFilterNone">Uncheck All</button>
                    </div>
                    <div class="col-xs-4">
                        <input type="checkbox" name="fltProject" value="Flowline" checked>Flowlines<br>
                        <input type="checkbox" name="fltProject" value="Other" checked>Other
                        <button class="btn btn-block btn-primary" id="btnProjectFilter">Filter</button>
                    </div>
                </div>
                <div class="" id="divProjectData"></div>
            </div>
            <div id="divBUOWL" class="col-xs-12">
                <div id="divBUOWLLabel" class="text-center col-xs-12">
                    <h4 id="lblBUOWL">BUOWL Habitat</h4>
                </div>
                <div id="divBUOWLError" class="errorMsg col-xs-12"></div>
                <div id="divFindBUOWL" class="form-group has-error">
                    <div class="col-xs-6">
                        <input type="text" id="txtFindBUOWL" class="form-control" placeholder="Habitat ID">
                    </div>
                    <div class="col-xs-6">
                        <button id="btnFindBUOWL" class="btn btn-primary btn-block" disabled>Find BUOWL</button>
                    </div>
                </div>
                <div class="col-xs-12" id="divFilterBUOWL">
                    <div class="col-xs-4">
                        <input type="radio" name="fltBUOWL" value="ALL" checked>All
                    </div>
                    <div class="col-xs-4">
                        <input type="radio" name="fltBUOWL" value="Yes">Historically Occupied
                    </div>
                    <div class="col-xs-4">
                        <input type="radio" name="fltBUOWL" value="INACTIVE LOCATION">Undetermined
                    </div>
                </div>
                <div class="" id="divBUOWLData"></div>
            </div>
            <div id="divEagle" class="col-xs-12">
                <div id="divEagleLabel" class="text-center col-xs-12">
                    <h4 id="lblEagle">Eagle Nests</h4>
                </div>
                <div id="divEagleError" class="errorMsg col-xs-12"></div>
                <div id="divFindEagle" class="form-group has-error">
                    <div class="col-xs-6">
                        <input type="text" id="txtFindEagle" class="form-control" placeholder="Eagle Nest ID">
                    </div>
                    <div class="col-xs-6">
                        <button id="btnFindEagle" class="btn btn-primary btn-block" disabled>Find Eagle Nest</button>
                    </div>
                </div>
                <div class="col-xs-12" id="divFilterEagle">
                    <div class="col-xs-4">
                        <input type="radio" name="fltEagle" value="ALL" checked>All
                    </div>
                    <div class="col-xs-4">
                        <input type="radio" name="fltEagle" value="ACTIVE NEST">Active
                    </div>
                    <div class="col-xs-4">
                        <input type="radio" name="fltEagle" value="INACTIVE LOCATION">Inactive
                    </div>
                </div>
                <div class="" id="divEagleData"></div>
            </div>
            <div id="divRaptor" class="col-xs-12">
                <div id="divRaptorLabel" class="text-center col-xs-12">
                    <h4 id="lblRaptor">Raptor Nests</h4>
                </div>
                <div id="divRaptorError" class="errorMsg col-xs-12"></div>
                <div id="divFindRaptor" class="form-group has-error">
                    <div class="col-xs-6">
                        <input type="text" id="txtFindRaptor" class="form-control" placeholder="Raptor Nest ID">
                    </div>
                    <div class="col-xs-6">
                        <button id="btnFindRaptor" class="btn btn-primary btn-block" disabled>Find Raptor Nest</button>
                    </div>
                </div>
                <div id="divFilterRaptor" class="col-xs-12">
                    <div class="col-xs-3">
                        <input type='radio' name='fltRaptor' value='ALL' checked>All
                    </div>
                    <div class="col-xs-3">
                        <input type='radio' name='fltRaptor' value='ACTIVE NEST'>Active
                    </div>
                    <div class="col-xs-3">
                        <input type='radio' name='fltRaptor' value='INACTIVE NEST'>Inactive
                    </div>
                    <div class="col-xs-3">
                        <input type='radio' name='fltRaptor' value='FLEDGED NEST'>Fledged
                    </div>
                </div>
                <div class="" id="divRaptorData"></div>
            </div>
        </div>
        <div id="mapdiv" class="col-md-12"></div>
        <script>
            var mymap;
            var lyrOSM;
            var lyrWatercolor;
            var lyrTopo;
            var lyrImagery;
            var lyrOutdoors;
            var lyrEagleNests;
            var lyrRaptorNests;
            var lyrClientLines;
            let lyrClientLinesBuffer;
            var lyrBUOWL;
            let lyrBUOWLbuffer;
            let jsonBUOWLbuffer;
            var lyrGBH;
            var lyrSearch;
            var lyrMarkerCluster;
            var mrkCurrentLocation;
            var fgpDrawnItems;
            var ctlAttribute;
            var ctlScale;
            var ctlMouseposition;
            var ctlMeasure;
            var ctlEasybutton;
            var ctlSidebar;
            var ctlLayers;
            var ctlDraw;
            var ctlStyle;
            let ctlLegend;
            var objBasemaps;
            var objOverlays;
            var arProjectIDs = [];
            var arHabitatIDs = [];
            var arEagleIDs = [];
            var arRaptorIDs = [];
            
            $(document).ready(function(){
                
                //  ********* Map Initialization ****************
                
                mymap = L.map('mapdiv', {center:[40.18, -104.83], zoom:11, attributionControl:false});
                
                ctlSidebar = L.control.sidebar('side-bar').addTo(mymap);
                
                ctlEasybutton = L.easyButton('glyphicon-transfer', function(){
                   ctlSidebar.toggle(); 
                }).addTo(mymap);
                
                ctlAttribute = L.control.attribution().addTo(mymap);
                ctlAttribute.addAttribution('OSM');
                ctlAttribute.addAttribution('&copy; <a href="http://millermountain.com">Naziru Development SRL</a>');
                
                ctlScale = L.control.scale({position:'bottomleft', metric:false, maxWidth:200}).addTo(mymap);

                ctlMouseposition = L.control.mousePosition().addTo(mymap);
                
                ctlStyle = L.control.styleEditor({position:'topright', openOnLeafletDraw: false}).addTo(mymap);
                ctlMeasure = L.control.polylineMeasure().addTo(mymap);
                
                //   *********** Layer Initialization **********
                
                lyrOSM = L.tileLayer.provider('OpenStreetMap.Mapnik');
                lyrTopo = L.tileLayer.provider('OpenTopoMap');
                lyrImagery = L.tileLayer.provider('Esri.WorldImagery');
                lyrOutdoors = L.tileLayer.provider('Thunderforest.Outdoors');
                lyrWatercolor = L.tileLayer.provider('Stamen.Watercolor');
                mymap.addLayer(lyrOSM);
                
                fgpDrawnItems = new L.FeatureGroup();
                fgpDrawnItems.addTo(mymap);

                /*** Loading our data ***/
                // Here we are loading the same data from bellow but from the postGIS database use AJAX
                refreshEagles();
                refreshRaptors();
                refreshLinears();
                refreshBUOWL();

                // Here we are loading data from a static file
                /*lyrEagleNests = L.geoJSON.ajax('data/wildlife_eagle.geojson', {pointToLayer:returnEagleMarker, filter:filterEagle}).addTo(mymap);
                lyrEagleNests.on('data:loaded', function(){
                    arEagleIDs.sort(function(a,b){return a-b});
                    $("#txtFindEagle").autocomplete({
                        source:arEagleIDs
                    });
                });*/
                
                lyrGBH = L.geoJSON.ajax('data/wildlife_gbh.geojson', {style:{color:'fuchsia'}}).bindTooltip("GBH Nesting Area").addTo(mymap);
                
                // ********* Setup Layer Control  ***************
                
                objBasemaps = {
                    "Open Street Maps": lyrOSM,
                    "Topo Map":lyrTopo,
                    "Imagery":lyrImagery,
                    "Outdoors":lyrOutdoors,
                    "Watercolor":lyrWatercolor
                };
                
                objOverlays = {

                };
                
                ctlLayers = L.control.layers(objBasemaps, objOverlays).addTo(mymap);

                mymap.on("overlayadd", function (e) {
                    let strDiv = "#lgnd"+stripSpaces(e.name);
                    $(strDiv).show();
                });
                mymap.on("overlayremove", function (e) {
                    let strDiv = "#lgnd"+stripSpaces(e.name);
                    $(strDiv).hide();
                });

                ctlLegend = new L.Control.Legend({
                    position: "topright",
                    controlButton: {
                        title: "Legend"
                    }
                }).addTo(mymap);

                $(".legend-container").append($("#legend"));
                $(".legend-toggle").append($("<i class='legend-toggle-icon fa fa-server fa-2x' style='color: #000'> </i>"));
                
                // **********  Setup Draw Control ****************
                
                ctlDraw = new L.Control.Draw({
                    draw:{
                        polygon: false,
                        circle:false,
                        rectangle:false,
                    },
                    edit:{
                        featureGroup:fgpDrawnItems,
                        remove: false
                    }
                });
                ctlDraw.addTo(mymap);
                
                mymap.on('draw:created', function(e){
                    switch (e.layerType) {
                        case "marker":
                            let strTable;
                            const llRef = e.layer.getLatLng();
                            strTable = "<table class='table table-hover'>";
                            strTable += "<tr><th>Constraint</th><th>ID</th><th>Type</th><th>Distance</th><th>Direction</th></tr>";
                            let nrBUOWL = returnClosestLayer(lyrBUOWL, llRef);
                            strTable += "<tr><td>BUOWL</td><td>"+nrBUOWL.att.habitat_id+"</td><td>"+nrBUOWL.att.recentstatus+"</td>" +
                                "<td>"+nrBUOWL.distance.toFixed(0)+" m</td><td>"+nrBUOWL.bearing.toFixed(0)+"</td></tr>";
                            let nrEagle = returnClosestLayer(lyrEagleNests, llRef);
                            strTable += "<tr><td>Eagle Nest</td><td>"+nrEagle.att.nest_id+"</td><td>"+nrEagle.att.status+"</td>" +
                                "<td>"+nrEagle.distance.toFixed(0)+" m</td><td>"+nrEagle.bearing.toFixed(0)+"</td></tr>";
                            let nrRaptor = returnClosestLayer(lyrRaptorNests, llRef);
                            strTable += "<tr><td>Raptor Nest</td><td>"+nrRaptor.att.Nest_ID+"</td><td>"+nrRaptor.att.recentspecies+"<br>"+nrRaptor.att.recentstatus+"</td>" +
                                "<td>"+nrRaptor.distance.toFixed(0)+" m</td><td>"+nrRaptor.bearing.toFixed(0)+"</td></tr>";
                            let nrGBH = returnClosestLayer(lyrGBH, llRef);
                            strTable += "<tr><td>GBH</td><td>N/A</td><td>N/A</td>" +
                                "<td>"+(nrGBH.distance+255).toFixed(0)+" m</td><td>"+nrGBH.bearing.toFixed(0)+"</td></tr>";
                            strTable += "</table>"
                            fgpDrawnItems.addLayer(e.layer.bindPopup(strTable, {maxWidth: 400}));
                            break;
                        case "polyline":
                            const line = e.layer.toGeoJSON();
                            const colEagle = summarizePointsByLine(line, 0.8, lyrEagleNests.toGeoJSON(), "status");

                            const sumEagle = summarizeArray(colEagle.features[0].properties.statusVals);
                            let strPopup = "Eagles";
                            for (let i = 0; i < sumEagle[0].length; i++ ) {
                                strPopup += "<br> "+sumEagle[0][i]+": "+sumEagle[1][i];
                            }
                            // Red tail hawk popup
                            const arRTH = returnLayersByAttribute(lyrRaptorNests, "recentspecies", "Red-tail Hawk");
                            const fcRTH = L.featureGroup(arRTH).toGeoJSON();
                            const colRTH = summarizePointsByLine(line, 0.533, fcRTH, "recentstatus");

                            const sumRTH = summarizeArray(colRTH.features[0].properties.recentstatusVals);
                            strPopup += "<br>Hawks<br>&nbsp;&nbsp;Red-Tail Hawk"; // &nbsp - stands for: non bracking space. HTML ignores white space
                            for (let i = 0; i < sumRTH[0].length; i++ ) {
                                strPopup += "<br>&nbsp;&nbsp;&nbsp;&nbsp;"+sumRTH[0][i]+": "+sumRTH[1][i];
                            }
                            // Swaisons Hawk
                            const arSWH = returnLayersByAttribute(lyrRaptorNests, "recentspecies", "Swainsons Hawk");
                            const fcSWH = L.featureGroup(arSWH).toGeoJSON();
                            const colSWH = summarizePointsByLine(line, 0.533, fcSWH, "recentstatus");

                            const sumSWH = summarizeArray(colSWH.features[0].properties.recentstatusVals);
                            strPopup += "<br>Hawks<br>&nbsp;&nbsp;Swainsons Hawk"; // &nbsp - stands for: non bracking space. HTML ignores white space
                            for (let i = 0; i < sumSWH[0].length; i++ ) {
                                strPopup += "<br>&nbsp;&nbsp;&nbsp;&nbsp;"+sumSWH[0][i]+": "+sumSWH[1][i];
                            }
                            //BUOWL Polygons
                            const bufLine = turf.buffer(line, 0.05, "kilometers");
                            const intBUOWL = intersectPolyByPolyFC(bufLine, lyrBUOWL.toGeoJSON());
                            const intBUOWLline = intersectLineByPolyFC(line, lyrBUOWLbuffer.toGeoJSON());

                            L.geoJSON(intBUOWL, {style: {color: "red", weight: 5}}).addTo(mymap);
                            L.geoJSON(intBUOWLline, {style: {color: "red", weight: 5}}).addTo(mymap);

                            var arBUOWLsummary = summarizePolyFC(intBUOWL, "hist_occup");
                            strPopup += "<br>BUOWL<br>&nbsp;&nbsp;Direct Impacts";
                            for (let i = 0; i < arBUOWLsummary[0].length; i ++) {
                                strPopup += "<br>&nbsp;&nbsp;&nbsp;&nbsp;"+arBUOWLsummary[0][i]+": "+arBUOWLsummary[1][i]+" " +
                                    "("+(arBUOWLsummary[2][i]/10000).toFixed(1)+" hectare)";
                            }
                            var arBUOWLsummary = summarizeLineFC(intBUOWLline, "hist_occup");
                            strPopup += "<br>BUOWL<br>&nbsp;&nbsp;Indirect Impacts";
                            for (let i = 0; i < arBUOWLsummary[0].length; i ++) {
                                strPopup += "<br>&nbsp;&nbsp;&nbsp;&nbsp;"+arBUOWLsummary[0][i]+": "+arBUOWLsummary[1][i]+" " +
                                    "("+(arBUOWLsummary[2][i]).toFixed(3)+" km)";
                            }

                            fgpDrawnItems.addLayer(e.layer.bindPopup(strPopup));
                            e.layer.openPopup();
                            break;
                    }

                });
                
                // ************ Location Events **************
                
                mymap.on('locationfound', function(e) {
                    console.log(e);
                    if (mrkCurrentLocation) {
                        mrkCurrentLocation.remove();
                    }
                    mrkCurrentLocation = L.circle(e.latlng, {radius:e.accuracy/2}).addTo(mymap);
                    mymap.setView(e.latlng, 14);
                });
                
                mymap.on('locationerror', function(e) {
                    console.log(e);
                    alert("Location was not found");
                })
                
            });

            //  ********* BUOWL Functions

            $("#btnBUOWL").click(function () {
                $("#legendBUOWLDetails").toggle();
            });

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
            
            function filterBUOWL(json){
                var att = json.properties;
                if (att.recentstatus=='REMOVED') {
                    return false;
                } else {
                    const optFilter = $("input[name=fltBUOWL]:checked").val();
                    if (optFilter === "ALL") {
                        return true;
                    } else {
                        return (att.hist_occup == optFilter);
                    }
                }
            }
            
            $("#txtFindBUOWL").on('keyup paste', function(){
                var val = $("#txtFindBUOWL").val();
                testLayerAttribute(arHabitatIDs, val, "Habitat ID", "#divFindBUOWL", "#divBUOWLError", "#btnFindBUOWL");
            });
            
            $("#btnFindBUOWL").click(function(){
                var val = $("#txtFindBUOWL").val();
                var lyr = returnLayerByAttribute(lyrBUOWL,'habitat_id',val);
                if (lyr) {
                    if (lyrSearch) {
                        lyrSearch.remove();
                    }
                    lyrSearch = L.geoJSON(lyr.toGeoJSON(), {style:{color:'red', weight:10, opacity:0.5, fillOpacity:0}}).addTo(mymap);
                    mymap.fitBounds(lyr.getBounds().pad(1));
                    var att = lyr.feature.properties;
                    $("#divBUOWLData").html("<h4 class='text-center'>Attributes</h4><h5>Habitat: "+att.habitat_id+"</h5><h5>Historically Occupied: "+att.hist_occup+"</h5><h5>Recent Status: "+att.recentstatus+"</h5>");
                    $("#divBUOWLError").html("");

                    // Editing geometries. Leaflet Draw function doesn't handle polygons. But there are way to get around this.
                    fgpDrawnItems.clearLayers();
                    fgpDrawnItems.addLayer(lyr);
                } else {
                    $("#divBUOWLError").html("**** Habitat ID not found ****");
                }
            });
            
            $("#lblBUOWL").click(function(){
                $("#divBUOWLData").toggle(); 
            });

            $("input[name=fltBUOWL]").click(function () {
                refreshBUOWL();
            });

            function refreshBUOWL() {
                $.ajax({url: "load_data.php",
                    data: {tbl: "dj_buowl", flds: "id, habitat, hist_occup, recentstatus, habitat_id"},
                    type: "POST",
                    success: function (response){
                        // Reset the eagle id layer, it has to be empty before we reload the data
                        arHabitatIDs = [];
                        jsonBUOWL = JSON.parse(response);
                        if (lyrBUOWL) {
                            ctlLayers.removeLayer(lyrBUOWL);
                            lyrBUOWL.remove();
                            lyrBUOWLbuffer.remove();
                        }
                        lyrBUOWL = L.geoJSON(jsonBUOWL, {style:styleBUOWL, onEachFeature:processBUOWL, filter:filterBUOWL}).addTo(mymap);
                        // Layer control
                        ctlLayers.addOverlay(lyrBUOWL, "Burrowing Owl Habitat")
                        arHabitatIDs.sort(function(a,b){return a-b});
                        $("#txtFindBUOWL").autocomplete({
                            source:arHabitatIDs
                        });
                        // Radius
                        jsonBUOWLbuffer = turf.buffer(lyrBUOWL.toGeoJSON(), 0.3, "kilometers");
                        lyrBUOWLbuffer = L.geoJSON(jsonBUOWLbuffer, {style:{color: "yellow", dashArray: "5,5", fillOpacity: 0}}).addTo(mymap);
                        lyrBUOWL.bringToFront();
                    }
                });
            }

            /*lyrBUOWL = L.geoJSON.ajax('data/wildlife_buowl.geojson', {style:styleBUOWL, onEachFeature:processBUOWL, filter:filterBUOWL}).addTo(mymap);
            lyrBUOWL.on('data:loaded', function(){
                arHabitatIDs.sort(function(a,b){return a-b});
                $("#txtFindBUOWL").autocomplete({
                    source:arHabitatIDs
                });
                // Radius
                    jsonBUOWLbuffer = turf.buffer(lyrBUOWL.toGeoJSON(), 0.3, "kilometers");
                    lyrBUOWLbuffer = L.geoJSON(jsonBUOWLbuffer, {style:{color: "yellow", dashArray: "5,5", fillOpacity: 0}}).addTo(mymap);
                    lyrBUOWL.bringToFront();
            });*/
            
            // ************ Client Linears **********

            $("#btnLinearProjects").click(function () {
                $("#legendLinearProjectDetails").toggle();
            });

            function styleClientLinears(json) {
                var att = json.properties;
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
                const jsonBuffer = turf.buffer(json, att.row_width / 1000, "kilometers");
                const lyrBuffer = L.geoJSON(jsonBuffer, {style: {color: "gray", dashArray: "5,5"}});
                lyrClientLinesBuffer.addLayer(lyrBuffer);
            }

            function filterClientLines(json) {
                const arProjectFilters = [];
                $("input[name=fltProject]").each(function () {
                    if (this.checked) {
                        arProjectFilters.push(this.value);
                    }
                });
                const att = json.properties;
                switch (att.type) {
                    case "Pipeline":
                        return (arProjectFilters.indexOf("Pipeline") >= 0);
                        break;
                    case "Flowline":
                        return (arProjectFilters.indexOf("Flowline") >= 0);
                        break;
                    case "Flowline, est":
                        return (arProjectFilters.indexOf("Flowline") >= 0);
                        break;
                    case "Electric line":
                        return (arProjectFilters.indexOf("Electric") >= 0);
                        break;
                    case "Access Road - Confirmed":
                        return (arProjectFilters.indexOf("Road") >= 0);
                        break;
                    case "Access Road - Estimated":
                        return (arProjectFilters.indexOf("Road") >= 0);
                        break;
                    case "Extraction":
                        return (arProjectFilters.indexOf("Extraction") >= 0);
                        break;
                    default:
                        return (arProjectFilters.indexOf("Other") >= 0);
                        break;
                }
            }
            
            $("#txtFindProject").on('keyup paste', function(){
                var val = $("#txtFindProject").val();
                testLayerAttribute(arProjectIDs, val, "PROJECT ID", "#divFindProject", "#divProjectError", "#btnFindProject");
            });
            
            $("#btnFindProject").click(function(){
                var val = $("#txtFindProject").val();
                var lyr = returnLayerByAttribute(lyrClientLines,'project',val);
                if (lyr) {
                    if (lyrSearch) {
                        lyrSearch.remove();
                    }
                    lyrSearch = L.geoJSON(lyr.toGeoJSON(), {style:{color:'red', weight:10, opacity:0.5}}).addTo(mymap);
                    mymap.fitBounds(lyr.getBounds().pad(1));
                    var att = lyr.feature.properties;
                    $("#divProjectData").html("<h4 class='text-center'>Attributes</h4><h5>Type: "+att.type+"</h5><h5>ROW width: "+att.row_width+ "m </h5>");
                    $("#divProjectError").html("");

                    // Editing geometries.
                    fgpDrawnItems.clearLayers();
                    fgpDrawnItems.addLayer(lyr);
                } else {
                    $("#divProjectError").html("**** Project ID not found ****");
                }
            });
            
            $("#lblProject").click(function(){
                $("#divProjectData").toggle(); 
            });

            // Click event
            $("#btnProjectFilterAll").click(function () {
                $("input[name=fltProject]").prop("checked", true);
            });
            $("#btnProjectFilterNone").click(function () {
                $("input[name=fltProject]").prop("checked", false);
            });
            $("#btnProjectFilter").click(function () {
                refreshLinears();
            });

            function refreshLinears() {
                $.ajax({url: "load_data.php",
                    data: {tbl: "dj_linear_projects", flds: "id, type, row_width, project"},
                    type: "POST",
                    success: function (response){
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
                        lyrClientLines = L.geoJSON(jsonLinears, {style:styleClientLinears, onEachFeature:processClientLinears,
                            filter:filterClientLines}).addTo(mymap);
                        // Layer control
                        ctlLayers.addOverlay(lyrClientLines, "Linear Projects")
                        arProjectIDs.sort(function(a,b){return a-b});
                        $("#txtFindProject").autocomplete({
                            source:arProjectIDs
                        });
                        lyrClientLinesBuffer.addTo(mymap);
                        lyrClientLines.bringToFront();
                    }
                });
            }

            /*lyrClientLinesBuffer = L.featureGroup();
            lyrClientLines = L.geoJSON.ajax('data/client_lines.geojson', {style:styleClientLinears, onEachFeature:processClientLinears,
                filter:filterClientLines}).addTo(mymap);
            lyrClientLines.on('data:loaded', function(){
                arProjectIDs.sort(function(a,b){return a-b});
                $("#txtFindProject").autocomplete({
                    source:arProjectIDs
                });
                lyrClientLinesBuffer.addTo(mymap);
                lyrClientLines.bringToFront();
            });*/
            
            // *********  Eagle Functions *****************

            $("#btnEagle").click(function () {
                $("#lgndEagleDetail").toggle();
            });

            function returnEagleMarker(json, latlng){
                var att = json.properties;
                if (att.status=='ACTIVE NEST') {
                    var clrNest = 'deeppink';
                } else {
                    var clrNest = 'chartreuse';
                }
                arEagleIDs.push(att.nest_id.toString());
                return L.circle(latlng, {radius:804, color:clrNest,fillColor:'chartreuse', fillOpacity:0.5}).bindTooltip("<h4>Eagle Nest: "+att.nest_id+"</h4>Status: "+att.status);
            }

            /* Radio button */
            function filterEagle(json) {
              const att = json.properties;
              const optFilter = $("input[name=fltEagle]:checked").val()
                if (optFilter === "ALL") {
                    return true;
                } else {
                    return (att.status === optFilter);
                }
            }
            
            $("#txtFindEagle").on('keyup paste', function(){
                var val = $("#txtFindEagle").val();
                testLayerAttribute(arEagleIDs, val, "Eagle Nest ID", "#divFindEagle", "#divEagleError", "#btnFindEagle");
            });
            
            $("#btnFindEagle").click(function(){
                var val = $("#txtFindEagle").val();
                var lyr = returnLayerByAttribute(lyrEagleNests,'nest_id',val);
                if (lyr) {
                    if (lyrSearch) {
                        lyrSearch.remove();
                    }
                    lyrSearch = L.circle(lyr.getLatLng(), {radius:800, color:'red', weight:10, opacity:0.5, fillOpacity:0}).addTo(mymap);
                    mymap.setView(lyr.getLatLng(), 14);
                    var att = lyr.feature.properties;
                    $("#divEagleData").html("<h4 class='text-center'>Attributes</h4><h5>Status: "+att.status+"</h5>");
                    $("#divEagleError").html("");

                    // Editing geometries.
                    fgpDrawnItems.clearLayers();
                    fgpDrawnItems.addLayer(lyr);
                } else {
                    $("#divEagleError").html("**** Eagle Nest ID not found ****");
                }
            });
            
            $("#lblEagle").click(function(){
                $("#divEagleData").toggle(); 
            });

            // Radio buttons
            $("input[name=fltEagle]").click(function () {
                refreshEagles();
            });

            function refreshEagles() {
                $.ajax({url: "load_data.php",
                    data: {tbl: "dj_eagle", flds: "id, status, nest_id"},
                    type: "POST",
                    success: function (response){
                        // Reset the eagle id layer, it has to be empty before we reload the data
                        arEagleIDs = [];
                        jsonEagles = JSON.parse(response);
                        if (lyrEagleNests) {
                            ctlLayers.removeLayer(lyrEagleNests);
                            lyrEagleNests.remove();
                        }
                        lyrEagleNests = L.geoJSON(jsonEagles, {pointToLayer:returnEagleMarker, filter:filterEagle}).addTo(mymap);
                        // Layer control
                        ctlLayers.addOverlay(lyrEagleNests, "Eagle Nests")
                        arEagleIDs.sort(function(a,b){return a-b});
                        $("#txtFindEagle").autocomplete({
                            source:arEagleIDs
                        });
                    }
                });
            }
            
            //  *********** Raptor Functions

            $("#btnRaptor").click(function () {
                $("#lgndRaptorDetail").toggle();
            });
            
            function returnRaptorMarker(json, latlng){
                let att = json.properties;
                arRaptorIDs.push(att.nest_id.toString());
                switch (att.recentspecies) {
                    case 'Red-tail Hawk':
                        var radRaptor = 533;
                        break;
                    case 'Swainsons Hawk':
                        var radRaptor = 400;
                        break;
                    default:
                        var radRaptor = 804;
                        break;
                }
                switch (att.recentstatus) {
                    case 'ACTIVE NEST':
                        var optRaptor = {radius:radRaptor, color:'deeppink', fillColor:"cyan", fillOpacity:0.5};
                        break;
                    case 'INACTIVE NEST':
                        var optRaptor = {radius:radRaptor, color:'cyan', fillColor:'cyan', fillOpacity:0.5};
                        break;
                    case 'FLEDGED NEST':
                        var optRaptor = {radius:radRaptor, color:'deeppink', fillColor:"cyan", fillOpacity:0.5, dashArray:"2,8"};
                        break;
                }
                return L.circle(latlng, optRaptor).bindPopup("<h4>Raptor Nest: "+att.nest_id+"</h4>Status: "+att.recentstatus+"<br>Species: "+att.recentspecies+"<br>Last Survey: "+att.lastsurvey);
            }
                
            $("#txtFindRaptor").on('keyup paste', function(){
                var val = $("#txtFindRaptor").val();
                testLayerAttribute(arRaptorIDs, val, "Raptor Nest ID", "#divFindRaptor", "#divRaptorError", "#btnFindRaptor");
            });
            
            $("#btnFindRaptor").click(function(){
                var val = $("#txtFindRaptor").val();
                var lyr = returnLayerByAttribute(lyrRaptorNests,'nest_id',val);
                if (lyr) {
                    if (lyrSearch) {
                        lyrSearch.remove();
                    }
                    var att = lyr.feature.properties;
                    switch (att.recentspecies) {
                        case 'Red-tail Hawk':
                            var radRaptor = 533;
                            break;
                        case 'Swainsons Hawk':
                            var radRaptor = 400;
                            break;
                        default:
                            var radRaptor = 804;
                            break;
                    }
                    lyrSearch = L.circle(lyr.getLatLng(), {radius:radRaptor, color:'red', weight:10, opacity:0.5, fillOpacity:0}).addTo(mymap);
                    mymap.setView(lyr.getLatLng(), 14);
                    $("#divRaptorData").html("<h4 class='text-center'>Attributes</h4><h5>Status: "+att.recentstatus+"</h5><h5>Species: "+att.recentspecies+"</h5><h5>Last Survey: "+att.lastsurvey+"</h5>");
                    $("#divRaptorError").html("");

                    // Editing geometries.
                    fgpDrawnItems.clearLayers();
                    fgpDrawnItems.addLayer(lyr);
                } else {
                    $("#divRaptorError").html("**** Raptor Nest ID not found ****");
                }
            });

            function filterRaptor(json) {
                const att = json.properties;
                const optFilter = $("input[name=fltRaptor]:checked").val();
                if (optFilter === "ALL") {
                    return true;
                } else {
                    return (att.recentstatus === optFilter)
                }
            }
            
            $("#lblRaptor").click(function(){
                $("#divRaptorData").toggle(); 
            });

            $("input[name=fltRaptor]").click(function () {
                refreshRaptors();
            });

            function refreshRaptors() {
                $.ajax({url: "load_data.php",
                    data: {tbl: "dj_raptor", flds: "id, nest_id, recentstatus, recentspecies, lastsurvey"},
                    type: "POST",
                    success: function (response){
                        // Reset the eagle id layer, it has to be empty before we reload the data
                        arRaptorIDs = [];
                        jsonRaptor = JSON.parse(response);
                        if (lyrMarkerCluster) {
                            ctlLayers.removeLayer(lyrMarkerCluster);
                            lyrMarkerCluster.remove();
                            lyrRaptorNests.remove();
                        }
                        lyrRaptorNests = L.geoJSON(jsonRaptor, {pointToLayer:returnRaptorMarker, filter:filterRaptor}).addTo(mymap);

                        arRaptorIDs.sort(function(a,b){return a-b});
                        $("#txtFindRaptor").autocomplete({
                            source:arRaptorIDs
                        });
                        lyrMarkerCluster = L.markerClusterGroup();
                        lyrMarkerCluster.addLayer(lyrRaptorNests);
                        lyrMarkerCluster.addTo(mymap);
                        // Layer control
                        ctlLayers.addOverlay(lyrMarkerCluster, "Raptor Nests")
                    }
                });
            }

            /*lyrRaptorNests = L.geoJSON.ajax('data/wildlife_raptor.geojson', {pointToLayer:returnRaptorMarker, filter:filterRaptor});
            lyrRaptorNests.on('data:loaded', function(){
                arRaptorIDs.sort(function(a,b){return a-b});
                $("#txtFindRaptor").autocomplete({
                    source:arRaptorIDs
                });

            });*/

            // /*** GBH Functions ***/


            
            //  *********  jQuery Event Handlers  ************

            $("#btnGBH").click(function () {
                $("#lgndGBHDetail").toggle();
            });
            
            $("#btnLocate").click(function(){
                mymap.locate();
            });

            $("#btnShowLegend").click(function () {
                $("#legend").toggle();
            });
            
            //  ***********  General Functions *********
            
            function LatLngToArrayString(ll) {
                return "["+ll.lat.toFixed(5)+", "+ll.lng.toFixed(5)+"]";
            }
            
            function returnLayerByAttribute(lyr,att,val) {
                const arLayers = lyr.getLayers();
                for (i=0;i<arLayers.length-1;i++) {
                    const ftrVal = arLayers[i].feature.properties[att];
                    if (ftrVal==val) {
                        return arLayers[i];
                    }
                }
                return false;
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
        </script>
    </body>
</html>