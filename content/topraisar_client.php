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
        <link rel="stylesheet" href="src/plugins/leaflet-sidebar.min.css">
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
        <link rel="stylesheet" href="src/plugins/leaflet.pm.css">
        <link rel="stylesheet" href="../css/modal.css">
        <link rel="stylesheet" href="../css/form.css">
        
        <script src="src/leaflet-src.js"></script>
        <script src="src/jquery-3.2.0.min.js"></script>
        <script src="src/plugins/L.Control.MousePosition.js"></script>
        <script src="src/plugins/leaflet-sidebar.min.js"></script>
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
        <script src="src/jquery-ui.min.js"></script>
        <script src="src/plugins/leaflet-legend.js"></script>
        <script src="src/plugins/leaflet.pm.min.js"></script>
        <script src="js/general_functions.js"></script>

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

            .btnSurveys {
                display: none;
            }
            
        </style>
    </head>
    <body>
    <div id="sidebar" class="leaflet-sidebar collapsed">
        <!-- Nav tabs -->
        <div class="leaflet-sidebar-tabs">
            <ul role="tablist"> <!-- top aligned tabs -->
                <li><a href="#home" role="tab"><i class="fa fa-bars"></i></a></li>
                <li><a href="#legend" role="tab"><i class="fa fa-server"></i></a></li>
                <li><a href="#project" role="tab"><i class="fa fa-gavel"></i></a></li>
                <li><a href="#buowl" role="tab"><i class="fa fa-cubes"></i></a></li>
                <li><a href="#eagles" role="tab"><i class="fa fa-snowflake-o"></i></a></li>
                <li><a href="#raptors" role="tab"><i class="fa fa-tree"></i></a></li>
            </ul>

            <ul role="tablist"> <!-- bottom aligned tabs -->
                <li><a href="#settings" role="tab"><i class="fa fa-gear"></i></a></li>
            </ul>
        </div>

        <!-- Tab panes -->
        <div class="leaflet-sidebar-content">
            <div class="leaflet-sidebar-pane" id="home">
                <h1 class="leaflet-sidebar-header">
                    Acasa
                    <div class="leaflet-sidebar-close"><i class="fa fa-caret-left"></i></div>
                </h1>
                <button id='btnLocate' class="btn btn-primary btn-block">Locate</button><br>
                <!-- Map Legend -->
                <button id="btnZoomToDj" class="btn btn-success btn-block">Zoom to DjBasin</button>
                <button id="btnTransparent" class="btn btn-warning btn-block">Make Polygons Transparent</button>
            </div>

            <div class="leaflet-sidebar-pane" id="legend">
                <h1 class="leaflet-sidebar-header">Legend<div class="leaflet-sidebar-close"><i class="fa fa-caret-left"></i></div></h1>
                <div id="lgndLinearProjects">
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
                <div id="lgndEagleNests">
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
                <div id="lgndRaptorNests">
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
                <div id="lgndHeronRookeries">
                    <h4 class="text-center">Heron Rookeries <i id="btnGBH" class="fa fa-server"></i></h4>
                    <div id="lgndGBHDetail">
                        <svg height="40">
                            <rect x="10" y="5" width="30" height="20" style="stroke-width: 4; stroke: fuchsia; fill: fuchsia; fill-opacity:0.5;"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="leaflet-sidebar-pane" id="project">
                <h1 class="leaflet-sidebar-header"> Linear Project <button id="btnRefreshLinear" class="btn btn-primary"><i class="fa fa-refresh"></i></button>
                    <div class="leaflet-sidebar-close"><i class="fa fa-caret-left"></i></div></h1>

                <div id="divProject" class="col-xs-12">
                    <div id="divProjectError" class="errorMsg col-xs-12"></div>
                    <div id="divFindProject" class="form-group has-error">
                        <div class="col-xs-6">
                            <input type="text" id="txtFindProject" class="form-control" placeholder="Project ID">
                        </div>
                        <div class="col-xs-6">
                            <button id="btnFindProject" class="btn btn-primary btn-block" disabled>Find Project</button>
                        </div>
                    </div>
                    <div id="divFilterProject" class="col-xs-12">
                        <div class="col-xs-4">
                            <input type='checkbox' name='fltProject' value='Pipeline' checked>Pipelines<br>
                            <input type='checkbox' name='fltProject' value='Road' checked>Access Roads
                            <button id="btnProjectFilterAll" class="btn btn-primary btn-block">Check All</button>
                        </div>
                        <div class="col-xs-4">
                            <input type='checkbox' name='fltProject' value='Electric' checked>Electric Lines<br>
                            <input type='checkbox' name='fltProject' value='Extraction' checked>Extractions
                            <button id="btnProjectFilterNone" class="btn btn-primary btn-block">Uncheck All</button>
                        </div>
                        <div class="col-xs-4">
                            <input type='checkbox' name='fltProject' value='Flowline' checked>Flowlines<br>
                            <input type='checkbox' name='fltProject' value='Other' checked>Other
                            <button id="btnProjectFilter" class="btn btn-primary btn-block">Filter</button>
                        </div>
                    </div>
                    <div class="" id="divProjectData">
                        <form class="form-horizontal" id="formProject">
                            <div class="form-group">
                                <label class="control-label col-sm-3" for="type">Type:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="type" id="linear_type" placeholder="Type" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3" for="row_width">ROW Width:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="row_width" id="linear_row_width" placeholder="ROW Width" readonly>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="" id="divProjectAffected"></div>
                </div>
            </div>

            <div class="leaflet-sidebar-pane" id="buowl">
                <h1 class="leaflet-sidebar-header">Burrowing Owl Habitat <button id="btnRefreshBUOWL"class="btn btn-primary"><i class="fa fa-refresh"></i></button>
                    <div class="leaflet-sidebar-close"><i class="fa fa-caret-left"></i></div></h1>
                <div id="divBUOWL" class="col-xs-12">
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
                            <input type="radio" name="fltBUOWL" value="Undetermined">Undetermined
                        </div>
                    </div>
                    <div class="" id="divBUOWLData">
                        <form class="form-horizontal" id="formBUOWL">
                            <div class="form-group">
                                <label class="control-label col-sm-3" for="hist_occup">Historically Occupied: </label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="hist_occup" id="buowl_hist_occup" placeholder="Historically Occupied" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3" for="habitat">Habitat:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="habitat" id="buowl_habitat" placeholder="Habitat" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3" for="recentstatus">Recent Status:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="recentstatus" id="buowl_recentstatus" placeholder="Recent Status" readonly>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="" id="divBUOWLAffected"></div>
                    <button id="btnBUOWLsurveys" class="btnSurveys btn btn-danger btn-block">Show Surveys</button>
                </div>
            </div>

            <div class="leaflet-sidebar-pane" id="eagles">
                <h1 class="leaflet-sidebar-header">Eagle Nests <button id="btnRefreshEagles" class="btn btn-primary"><i class="fa fa-refresh"></i></button>
                    <div class="leaflet-sidebar-close"><i class="fa fa-caret-left"></i></div></h1>
                <div id="divEagle" class="col-xs-12">

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
                    <div class="" id="divEagleAffected"></div>
                    <button id="btnEagleSurveys" class="btnSurveys btn btn-danger btn-block">Show Surveys</button>
                </div>
            </div>

            <div class="leaflet-sidebar-pane" id="raptors">
                <h1 class="leaflet-sidebar-header">Raptor Nests  <button id="btnRefreshRaptors" class="btn btn-primary"><i class="fa fa-refresh"></i></button>
                    <div class="leaflet-sidebar-close"><i class="fa fa-caret-left"></i></div></h1>
                <div id="divRaptor" class="col-xs-12">
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
                    <div class="" id="divRaptorAffected"></div>
                    <button id="btnRaptorSurveys" class="btnSurveys btn btn-danger btn-block">Show Surveys</button>
                </div>
            </div>

            <div class="leaflet-sidebar-pane" id="settings"">
                <h1 class="leaflet-sidebar-header">
                    Setari
                    <div class="leaflet-sidebar-close"><i class="fa fa-caret-left"></i></div>
                </h1>
                <div id="logInInfo" style="margin-top: 10px;"></div>
                <a href="../mycontent.php"><button class="btn btn-info btn-block" style="margin-bottom: 10px; margin-top: 10px">My Content</button></a>
                <button id="btnLogout" class="btn btn-danger btn-block">Logout</button>
            </div>

        </div>
    </div>
        <div id="mapdiv" class="col-md-12"></div>
        <!-- Modal -->
        <div id="dlgModal" class="modal">
            <div id="dlgContent" class="modal-content col-sm-10 col-sm-offset-1 col-md-7 col-md-offset-4">
                <span id="btnCloseModal" class="pull-right"><i class="fa fa-close fa-2x"></i></span>
                <div id="tableData"></div>
            </div>
        </div>

        <script>
            /* Global variables */
            let user;
            $.ajax({
                url: "php/return_user.php",
                success: function (response) {
                    if (response.substring(0,5) == "ERROR") {
                        alert(response);
                    } else {
                        // it is passed as a string, but we need to parse it and JSON that we can use
                        user = JSON.parse(response);
                        $("#logInInfo").html("Logged in as "+user.firstname+" "+user.lastname+" on "+returnCurrentDate());
                    }
                }
            });
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
                
                ctlSidebar = L.control.sidebar({container: 'sidebar'}) // the DOM container or #ID of a predefined sidebar container that should be used)
                .addTo(mymap);
                
                /*ctlEasybutton = L.easyButton('glyphicon-transfer', function(){
                   ctlSidebar.toggle(); 
                }).addTo(mymap);*/
                
                ctlAttribute = L.control.attribution({position:"bottomright"}).addTo(mymap);
                ctlAttribute.addAttribution('OSM');
                ctlAttribute.addAttribution('&copy; <a href="http://millermountain.com">Naziru Development SRL</a>');
                
                ctlScale = L.control.scale({position:'bottomright', metric:false, maxWidth:200}).addTo(mymap);

                ctlMouseposition = L.control.mousePosition({position:'bottomright'}).addTo(mymap);
                
                /*ctlStyle = L.control.styleEditor({position:'topright', openOnLeafletDraw: false}).addTo(mymap);*/
                
                //   *********** Layer Initialization **********
                
                lyrOSM = L.tileLayer.provider('OpenStreetMap.Mapnik');
                lyrTopo = L.tileLayer.provider('OpenTopoMap');
                lyrImagery = L.tileLayer.provider('Esri.WorldImagery');
                lyrOutdoors = L.tileLayer.provider('Thunderforest.Outdoors');
                lyrWatercolor = L.tileLayer.provider('Stamen.Watercolor');
                mymap.addLayer(lyrOSM);
                
                /*fgpDrawnItems = new L.FeatureGroup();*/

                /*** Leaflet PM control ***/

                // define toolbar options
                const options = {
                        position: 'topright', // toolbar position, options are 'topleft', 'topright', 'bottomleft', 'bottomright'
                        drawMarker: true, // adds button to draw markers
                        drawPolyline: true, // adds button to draw a polyline
                        drawRectangle: false, // adds button to draw a rectangle // geoJSON won't work with Rectangles
                        drawPolygon: true, // adds button to draw a polygon
                        drawCircle: false, // adds button to draw a cricle // geoJSON won't work with circles
                        cutPolygon: false, // adds button to cut a hole in a polygon
                        editMode: true, // adds button to toggle edit mode for all layers
                        removalMode: true, // adds a button to remove layers
                    };

                // add leaflet.pm controls to the map
                mymap.pm.addControls(options);

                // listen to when a new layer is created
                mymap.on('pm:create', function(e) {
                    const jsn = e.layer.toGeoJSON().geometry;
                    console.log("Type: "+e.shape+"\nGeometry:"+JSON.stringify(jsn))
                    $.ajax({
                        url: "djbasin_resources/php_affected_constraints.php",
                        data: {id: 'geojson', geojson: JSON.stringify(jsn)},
                        type: "POST",
                        success: function (response) {
                            $("#tableData").html(response);
                            $("#dlgModal").show();
                        },
                        error: function (xhr, status, error) {
                            $("#tableData").html("ERROR: "+error);
                            $("#dlgModal").show();
                        }
                    })
                    //e.shape; // the name of the shape being drawn (i.e. 'Circle')
                    //e.layer; // the leaflet layer created
                });

                /*** Loading our data ***/
                // Here we are loading the same data from bellow but from the postGIS database use AJAX
                refreshEagles();
                refreshRaptors();
                refreshLinears();
                refreshBUOWL();
                refreshGBH();

                // Logout button
                $("#btnLogout").click(function () {
                    //redirecting
                    window.location="../logout.php"
                })
                
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
                    // we use == because we don't want to compare type
                    if (e.name == "Linear Projects") {
                        lyrClientLinesBuffer.addTo(mymap);
                        lyrClientLines.bringToFront();
                    }
                    if (e.name == "Burrowing Owl Habitat") {
                        lyrBUOWLbuffer.addTo(mymap);
                        lyrBUOWL.bringToFront();
                    }
                });
                mymap.on("overlayremove", function (e) {
                    let strDiv = "#lgnd"+stripSpaces(e.name);
                    $(strDiv).hide();
                    // Removing the buffer too (dots around the overlay)
                    if (e.name == "Linear Projects") {
                        lyrClientLinesBuffer.remove();
                    }
                    if (e.name == "Burrowing Owl Habitat") {
                        lyrBUOWLbuffer.remove();
                    }
                });

                ctlMeasure = L.control.polylineMeasure({position:'topright'}).addTo(mymap);

                /*ctlLegend = new L.Control.Legend({
                    position: "topright",
                    controlButton: {
                        title: "Legend"
                    }
                }).addTo(mymap);*/

                $(".legend-container").append($("#legend"));
                $(".legend-toggle").append($("<i class='legend-toggle-icon fa fa-server fa-2x' style='color: #000'> </i>"));
                
                // **********  Setup Draw Control ****************
                
               /* ctlDraw = new L.Control.Draw({
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

                });*/
                
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
            
            $("#txtFindBUOWL").on('keyup paste', function(){
                var val = $("#txtFindBUOWL").val();
                testLayerAttribute(arHabitatIDs, val, "Habitat ID", "#divFindBUOWL", "#divBUOWLError", "#btnFindBUOWL");
            });
            
            $("#btnFindBUOWL").click(function(){
                const val = $("#txtFindBUOWL").val();
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
                        // Turning the form on
                        $("#formBUOWL").show();
                       /* $("#divBUOWLData").html("<h4 class='text-center'>Attributes</h4><h5>Habitat: " + att.habitat_id + "</h5>" +
                            "<h5>Historically Occupied: " + att.hist_occup + "</h5>" +
                            "<h5>Recent Status: " + att.recentstatus + "</h5>");*/

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
            });
            
            $("#lblBUOWL").click(function(){
                $("#divBUOWLData").toggle(); 
            });

            // Filtering
            $("input[name=fltBUOWL]").click(function () {
                const optFilter = $("input[name=fltBUOWL]:checked").val();
                if (optFilter == "ALL") {
                    refreshBUOWL();
                } else {
                    refreshBUOWL("hist_occup='"+optFilter+"'");
                }
            });

            // Refreshing the server
            $("#btnRefreshBUOWL").click(function () {
                alert("Refreshing BUOWL");
                refreshBUOWL();
            });

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
            
            // ************ Client Linears **********

            $("#btnLinearProjects").click(function () {
                $("#legendLinearProjectDetails").toggle();
            });

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
            
            $("#txtFindProject").on('keyup paste', function(){
                const val = $("#txtFindProject").val();
                testLayerAttribute(arProjectIDs, val, "PROJECT ID", "#divFindProject", "#divProjectError", "#btnFindProject");
            });
            
            $("#btnFindProject").click(function(){
                const val = $("#txtFindProject").val();
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
                let arTypes = [];
                let cntChecks = 0;
                $("input[name=fltProject]").each(function () {
                    // This function goes threw all the checkboxes, and it checkes them if they are checked and it adds different types of values to the arTypes
                    if (this.checked) {
                        if (this.value == "Pipeline") {
                            arTypes.push("'Pipeline'");
                            // After it loops threw the checkboxes, and it is checked, it gets incremented
                            // after it does that, we will now how many boxes there are checked
                            cntChecks++;
                        }
                        if (this.value=='Flowline') {
                            arTypes.push("'Flowline'");
                            arTypes.push("'Flowline, est.'");
                            cntChecks++;
                        }
                        if (this.value=='Electric') {
                            arTypes.push("'Electric Line'");
                            cntChecks++;
                        }
                        if (this.value=='Road') {
                            arTypes.push("'Access Road - Confirmed'");
                            arTypes.push("'Access Road - Estimated'");
                            cntChecks++;
                        }
                        if (this.value=='Extraction') {
                            arTypes.push("'Extraction'");
                            arTypes.push("'Delayed-Extraction'");
                            cntChecks++;
                        }
                        if (this.value=='Other') {
                            arTypes.push("'Other'");
                            arTypes.push("'Underground Pipe'");
                            cntChecks++;
                        }
                    }
                });
                if (cntChecks == 0) {
                    // we are sending a false where clause so we won't get anything back or an error from the db
                    refreshLinears("1=2");
                } else if (cntChecks == 6) {
                    refreshLinears();
                } else {
                    refreshLinears("type IN ("+arTypes.toString()+")");
                }
            });

            // Refreshing the server and filtering data
            $("#btnRefreshLinear").click(function () {
                alert("Refreshed Linears");
                refreshLinears();
            });

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
            
            $("#txtFindEagle").on('keyup paste', function(){
                var val = $("#txtFindEagle").val();
                testLayerAttribute(arEagleIDs, val, "Eagle Nest ID", "#divFindEagle", "#divEagleError", "#btnFindEagle");
            });
            
            $("#btnFindEagle").click(function(){
                const val = $("#txtFindEagle").val();
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
                        var att = lyr.feature.properties;
                        $("#divEagleData").html("<h4 class='text-center'>Attributes</h4><h5>Status: " + att.status + "</h5>");

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
            });
            
            $("#lblEagle").click(function(){
                $("#divEagleData").toggle(); 
            });

            // Radio buttons
            $("input[name=fltEagle]").click(function () {
                // DB filtering instead from the local files
                const optFilter = $("input[name=fltEagle]:checked").val();
                if (optFilter == "ALL") {
                    refreshEagles();
                } else {
                    refreshEagles("status='"+optFilter+"'");
                }
            });

            // Refreshing the server
            $("#btnRefreshEagles").click(function () {
                alert("Refreshing Eagles");
                refreshEagles();
            });

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
                let radRaptor;
                const val = $("#txtFindRaptor").val();
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
                        $("#divRaptorData").html("<h4 class='text-center'>Attributes</h4><h5>Status: " + att.recentstatus + "</h5><h5>Species: " + att.recentspecies + "</h5><h5>Last Survey: " + att.lastsurvey + "</h5>");
                        $("#divRaptorError").html("");

                        $.ajax({
                            url: "dj_basin_affected_projects.php",
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
            });
            
            $("#lblRaptor").click(function(){
                $("#divRaptorData").toggle(); 
            });

            $("input[name=fltRaptor]").click(function () {
                const optFilter = $("input[name=fltRaptor]:checked").val();
                if (optFilter == "ALL") {
                    refreshRaptors();
                } else {
                    refreshRaptors ("recentstatus='"+optFilter+"'")
                }
            });

            // Refreshing the server
            $("#btnRefreshRaptors").click(function () {
                alert("Refreshing Raptors");
                refreshRaptors();
            });

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

            //               /*** GBH Functions ***/

            function refreshGBH() {
                $.ajax({url: "php/load_data.php",
                    data: {tbl: "dj_gbh", flds: "id, activity"},
                    type: "POST",
                    success: function (response){
                        if (response.substring(0, 5) == "ERROR") {
                            alert(response);
                        } else {
                            jsonGBH = JSON.parse(response);
                            if (lyrGBH) {
                                ctlLayers.removeLayer(lyrGBH);
                                lyrGBH.remove();
                            }
                            lyrGBH = L.geoJSON(jsonGBH, {style: {color: 'fuchsia'}}).bindTooltip("GBH Nesting Area").addTo(mymap);
                            // Layer control
                            ctlLayers.addOverlay(lyrGBH, "Heron Rookeries")
                        }
                    },
                    error: function (xhr, status, error) {
                        alert("ERROR: " + error)
                    }
                });
            }
            
            //  *********  jQuery Event Handlers  ************

            $("#btnGBH").click(function () {
                $("#lgndGBHDetail").toggle();
            });
            
            $("#btnLocate").click(function(){
                mymap.locate();
            });

            $("#btnZoomToDj").click(function () {
                mymap.setView([40.18, -104.83], 11);
            });

                        /* Raptor Surveys */

            $("#btnRaptorSurveys").click(function () {
                const search_id = $("#txtFindRaptor").val();
                const whr = "habitat="+search_id;
                $("#dlgModal").show();
                $.ajax({
                    url: "php/load_table.php",
                    data: {tbl: "dj_raptor_survey", title: 'Surveys for Raptor Nest '+search_id, order: "surveydate DESC",
                            flds: 'surveyor AS "Surveyor", surveydate AS "Survey Date", result AS "Result"',
                            where:whr},
                    type: "POST",
                    success: function (response) {
                        $("#tableData").html(response);
                        $("#dlgModal").show();
                    },
                    error: function (xhr, status, error) {
                        $("#tableData").html("ERROR: "+error);
                        $("#dlgModal").show();
                    }
                });
            });

                        /* Eagle Surveys */

            $("#btnEagleSurveys").click(function () {
                const search_id = $("#txtFindEagle").val();
                const whr = "habitat="+search_id;
                $("#dlgModal").show();
                $.ajax({
                    url: "php/load_table.php",
                    data: {tbl: "dj_eagle_surveys", title: 'Surveys for Eagle Nest '+search_id, order: "surveydate DESC",
                        flds: 'surveyor AS "Surveyor", surveydate AS "Survey Date", result AS "Result"',
                        where:whr},
                    type: "POST",
                    success: function (response) {
                        $("#tableData").html(response);
                        $("#dlgModal").show();
                    },
                    error: function (xhr, status, error) {
                        $("#tableData").html("ERROR: "+error);
                        $("#dlgModal").show();
                    }
                });
            });

                        /* BUOWL Surveys */

            $("#btnBUOWLsurveys").click(function () {
                const search_id = $("#txtFindBUOWL").val();
                const whr = "habitat="+search_id;
                $("#dlgModal").show();
                $.ajax({
                    url: "php/load_table.php",
                    data: {tbl: "dj_buowl_survey", title: 'Surveys for BUOWL habitat '+search_id, order: "surveydate DESC",
                        flds: '"surveyor" AS "Surveyor", surveydate AS "Survey Date", result AS "Result"',
                        where:whr},
                    type: "POST",
                    success: function (response) {
                        $("#tableData").html(response);
                        $("#dlgModal").show();
                    },
                    error: function (xhr, status, error) {
                        $("#tableData").html("ERROR: "+error);
                        $("#dlgModal").show();
                    }
                });
            });

            $("#btnCloseModal").click(function () {
                $("#dlgModal").hide();
            });

            // Transparent polygons
            $("#btnTransparent").click(function () {
                // This toggles fill to unfill the polygons
                if ($("#btnTransparent").html() == "Fill Polygons") {
                    lyrRaptorNests.setStyle({fillOpacity: 0.5});
                    lyrEagleNests.setStyle({fillOpacity: 0.5});
                    lyrBUOWL.setStyle({fillOpacity: 0.5});
                    lyrGBH.setStyle({fillOpacity: 0.5});
                    // Make them filled again
                    $("#btnTransparent").html("Make Polygons Transparent")
                } else {
                    lyrRaptorNests.setStyle({fillOpacity: 0});
                    lyrEagleNests.setStyle({fillOpacity: 0});
                    lyrBUOWL.setStyle({fillOpacity: 0});
                    lyrGBH.setStyle({fillOpacity: 0});
                    // Make them filled again
                    $("#btnTransparent").html("Fill Polygons")
                }
            })
            
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
                        if (response.substr(0,5)=="ERROR") {
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
                /*const arLayers = lyr.getLayers();
                for (i=0;i<arLayers.length-1;i++) {
                    const ftrVal = arLayers[i].feature.properties[att];
                    if (ftrVal==val) {
                        return arLayers[i];
                    }
                }
                return false;*/
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