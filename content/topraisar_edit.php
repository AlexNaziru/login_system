<?php include("../includes/init.php");?>
<?php
//If they are logged in, they can see this page
if (logged_in()) {
    $username = $_SESSION["username"];
    error_log("Logged in user: " . $username);
    // Checking to see if the user is a member of the group
    if (!verify_user_group($pdo, $username, "Topraisar edit")) {
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
    <link rel="stylesheet" href="djbasin_resources/djbasin.css">

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
    <script src="djbasin_resources/js_surveys.js"></script>
    <script src="djbasin_resources/GBH.js"></script>
    <script src="djbasin_resources/raptors.js"></script>
    <script src="djbasin_resources/buowl.js"></script>
    <script src="djbasin_resources/projects.js"></script>
    <script src="djbasin_resources/eagles.js"></script>
    <script src="js/general_editing.js"></script>
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
            <button id="btnZoomToTop" class="btn btn-info btn-block">Zoom to Topraisar</button>
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
                        <div id="projectMetadata"></div>
                    </form>
                </div>
                <div class="" id="divProjectAffected"></div>
            </div>
        </div>

        <div class="leaflet-sidebar-pane" id="buowl">
            <h1 class="leaflet-sidebar-header">Burrowing Owl Habitat <button id="btnRefreshBUOWL" class="btn btn-primary"><i class="fa fa-refresh"></i></button>
                <button id="btnAddBUOWL" class="btn btn-success"><i class="fa fa-plus"></i></button>
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
                        <div class="form-group featureID">
                            <label class="control-label col-sm-3" for="id">ID:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control inpBUOWL" name="id" id="buowl_id" placeholder="ID" disabled>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3" for="hist_occup">Historically Occupied: </label>
                            <div class="col-sm-9">
                                <select class="form-control inpBUOWL" name="hist_occup" id="buowl_hist_occup" disabled></select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3" for="habitat">Habitat:</label>
                            <div class="col-sm-9">
                                <select class="form-control inpBUOWL" name="habitat" id="buowl_habitat" disabled></select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3" for="recentstatus">Recent Status:</label>
                            <div class="col-sm-9">
                                <select class="form-control inpBUOWL" name="recentstatus" id="buowl_recentstatus" disabled></select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3" for="lastsurvey">Last Survey:</label>
                            <div class="col-sm-7">
                                <input type="date" class="form-control inpBUOWL" name="lastsurvey" id="buowl_lastsurvey" placeholder="Last Survey" disabled>
                            </div>
                            <div class="col-sm-2">
                                <span id="btnToggleBUOWLgeometry"><i class="fa fa-globe fa-2x"></i></span>
                            </div>
                        </div>
                        <div id="BUOWLGeojson" class="form-group">
                            <label class="control-label col-sm-3" for="geojson">
                                <span id="btnEditBUOWLgeometry"><i class="fa fa-pencil fa-2x"></i></span>
                                GeoJSON:
                            </label>
                            <div class="col-sm-9">
                                <textarea class="form-control inpBUOWL" name="geojson" id="buowl_geojson" placeholder="GeoJSON" disabled>
                                </textarea>
                            </div>
                        </div>
                        <div id="BUOWLmetadata" class="col-xs-9"></div>
                        <div class="col-xs-3">
                            <span id="btnEditBUOWL"><i class="fa fa-pencil fa-2x"></i></span>
                            <span id="btnDeleteBUOWL"><i class="fa fa-trash fa-2x pull-right"></i></span>
                        </div>
                    </form>
                </div>
                <button id="btnBUOWLUpdate" class="btnSurveys btn btn-warning btn-block">Update BUOWL</button>
                <button id="btnBUOWLInsert" class="btnSurveys btn btn-success btn-block">Insert BUOWL</button>
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
                <div class="" id="divEagleData">
                    <form class="form-horizontal" id="formEagle">
                        <div class="form-group">
                            <label class="control-label col-sm-3" for="status">Status</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="status" id="eagle_status" placeholder="Status" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3" for="lastsurvey">Last Survey:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="lastsurvey" id="eagle_lastsurvey" placeholder="Last Survey" readonly>
                            </div>
                        </div>
                        <div id="eagleMetadata"></div>
                    </form>
                </div>
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
                <div class="" id="divRaptorData">
                    <form class="form-horizontal" id="formRaptor">
                        <div class="form-group">
                            <label class="control-label col-sm-3" for="recentstatus">Status:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="recentspecies" id="raptor_recentstatus" placeholder="Status" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3" for="recentspecies">Species:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="recentspecies" id="raptor_recentspecies" placeholder="Species" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3" for="lastsurvey">Last Survey:</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" name="lastsurvey" id="raptor_lastsurvey" placeholder="Last Survey" readonly>
                            </div>
                        </div>
                        <div id="raptorMetadata"></div>
                    </form>
                </div>
                <div class="" id="divRaptorAffected"></div>
                <button id="btnRaptorSurveys" class="btnSurveys btn btn-danger btn-block">Show Surveys</button>
            </div>
        </div>

    </div>
</div>
<div id="mapdiv" class="col-md-12"></div>
<!-- Modal -->
<div id="dlgModal" class="modal">
    <div id="dlgContent" class="modal-content col-sm-10 col-sm-offset-1 col-md-7 col-md-offset-4">
        <span id="btnCloseModal" class="pull-right"><i class="fa fa-close fa-2x"></i></span>
        <div id="tableData"></div>
        <form id="formSurvey">
            <div class="col-xs-12">
                <div class="form-group">
                    <label class="control-label col-sm-3" for="survey_id">ID:</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control inpSurvey" name="id" id="survey_id" placeholder="Id" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3" for="survey_habitat">Habitat ID:</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control inpSurvey" name="habitat" id="survey_habitat" placeholder="Habitat Id" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3" for="survey_surveyor">Surveyor:</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control inpSurvey" name="surveyor" id="survey_surveyor" placeholder="Surveyor" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3" for="survey_surveydate">Survey Date:</label>
                    <div class="col-sm-9">
                        <input type="date" class="form-control inpSurvey" name="surveydate" id="survey_surveydate" placeholder="Survey Date">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3" for="survey_result">Result:</label>
                    <div class="col-sm-9">
                        <select class="form-control inpSurvey" name="result" id="survey_result">
                            <option value="NO NESTING ACTIVITY">NO NESTING ACTIVITY</option>
                            <option value="UNDETERMINED">UNDETERMINED</option>
                            <option value="ACTIVE NEST">ACTIVE NEST</option>
                            <option value="FLEDGED NEST">FLEDGED NEST</option>
                            <option value="INACTIVE NEST">INACTIVE NEST</option>
                        </select>
                    </div>
                </div>
                <div id="formSurveyButtons"></div>
            </div>
        </form>
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
                alert("Logged in as "+user.firstname+" "+user.lastname+" on "+returnCurrentDate());
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
            let jsn = e.layer.toGeoJSON().geometry;
            console.log("Layer created event triggered"); // Log the event trigger
            console.log("Shape created:", e.shape);

            if (isShowing("btnBUOWLInsert") && e.shape == "Polygon") {
                console.log("Checking condition for btnBUOWLInsert and Poly shape"); // Log condition check
                if (confirm("Are you sure you want to add geometry?")) {
                    // converting to a multipolygon as an array
                    jsn = {type: "MultiPolygon", coordinates: [jsn.coordinates]}
                    // adding the json into the text box
                    $("#buowl_geojson").val(JSON.stringify(jsn));
                    console.log("Type: "+e.shape+"\nGeometry:"+JSON.stringify(jsn))
                }
            } else {
                jsn = e.layer.toGeoJSON().geometry;
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
            }
        });

        /*** Loading our data ***/
        // Here we are loading the same data from bellow but from the postGIS database use AJAX
        refreshEagles();
        refreshRaptors();
        refreshLinears();
        refreshBUOWL();
        refreshGBH();

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

        $(".legend-container").append($("#legend"));
        $(".legend-toggle").append($("<i class='legend-toggle-icon fa fa-server fa-2x' style='color: #000'> </i>"));


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

        changeOptions("buowl_habitat", "dj_buowl", "habitat");
        changeOptions("buowl_hist_occup", "dj_buowl", "hist_occup");
        changeOptions("buowl_recentstatus", "dj_buowl", "recentstatus");
    });

    //  ********* BUOWL Functions

    $("#btnBUOWL").click(function () {
        $("#legendBUOWLDetails").toggle();
    });

    $("#txtFindBUOWL").on('keyup paste', function(){
        var val = $("#txtFindBUOWL").val();
        testLayerAttribute(arHabitatIDs, val, "Habitat ID", "#divFindBUOWL", "#divBUOWLError", "#btnFindBUOWL");
    });

    $("#btnFindBUOWL").click(function(){
        findBUOWL($("#txtFindBUOWL").val());
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

                /*** BUOWL EDITING/DELETING EVENT HANDLERS ***/

    $("#btnToggleBUOWLgeometry").click(function () {
        $("#BUOWLGeojson").toggle();
    })

    $("#btnAddBUOWL").click(function () {
        $("#buowl_id").val("New");
        $("#txtFindBUOWL").val("New");
        $("#buowl_habitat").val("");
        $("#buowl_hist_occup").val("");
        $("#buowl_recentstatus").val("");
        $("#buowl_lastsurvey").val(returnCurrentDate());
        // Populating the geojson id
        $("#buowl_geojson").val("");
        $("#BUOWLmetadata").html("");
        $(".inpBUOWL").attr("disabled", false);
        $("#buowl_id").attr("disabled", true);
        $("#buowl_geojson").attr("disabled", true);
        // Submitting a new form
        $("#btnBUOWLInsert").show();
        // Hiding update when insert is on
        $("#btnBUOWLUpdate").hide();
        $("#btnEditBUOWL").hide();
        $("#btnDeleteBUOWL").hide();
        // Turning the form on
        $("#formBUOWL").show();
    });

    // Event handle for the submit button
    $("#btnBUOWLInsert").click(function () {
        // a little validation
        if ($("#buowl_geojson").val() == "") {
            alert("No geometry added")
        } else if (($("#buowl_habitat").val() == "") || ($("#buowl_hist_occup").val() == "") || ($("#buowl_recentstatus").val() == "")) {
            alert("Fill out the fields")
        } else {
            let jsn = returnFormData("inpBUOWL");
            jsn.tbl = "dj_buowl";
            delete jsn.id;
            insertRecord(jsn)
        }
    });

    function insertRecord(jsn) {
        delete jsn.id;
        $.ajax({
            url: "php/insert_record.php",
            data: jsn,
            type: "POST",
            success: function (response) {
                if (response.substring(0,5) == "ERROR") {
                    alert(response);
                } else {
                    alert("New record added into "+jsn.tbl+"\n\n"+response);
                    // Controlling witch data is refreshed
                    switch (jsn.tbl) {
                        case "dj_buowl":
                            refreshBUOWL();
                            $("#formBUOWL").hide();
                            // we do not want the New to appear after we saved a new entry
                            $("#txtFindBUOWL").val("");
                            break;
                    }
                }
            },
            error: function (xhr, status, error) {
                alert("AJAX ERROR: "+error);
            }
        })
    }

    $("#btnEditBUOWL").click(function () {
        $(".inpBUOWL").attr("disabled", false);
        // disabling the id field
        $("#buowl_id").attr("disabled", true);
        // Disabling the geojson field
        $("#buowl_geojson").attr("disabled", true);
        // Submit button will pop out when we click the edit button
        $("#btnBUOWLUpdate").show();
    })

    // Editing postGIS
    $("#btnEditBUOWLgeometry").click(function () {
        // Creating new geometries
        if (isShowing("btnBUOWLUpdate")) {
            const jsnMulti = JSON.parse($("#buowl_geojson").val());
            const jsnSingle = explodeMulti(jsnMulti);
            const lyrEdit = L.geoJSON(jsnSingle).addTo(mymap);
            // leaflet turning on editing on the map
            lyrEdit.pm.enable();
            // To save our changes on the polygons, we need an event handler to the map that will handle the right click (delete)
            mymap.on("contextmenu", function () {
                if (confirm("Are you sure you want to update the geometry for this feature?")) {
                    // in order to work, we will take in the lyr converted to geojson
                    // Now we have to turn it into a multi polygon and inserted into our db and converting them into a 4 dimensional array
                    const jsnEdited = mergeLyrEdit(lyrEdit);
                    $("#buowl_geojson").val(JSON.stringify(jsnEdited));
                    // disable the edit layer after we save the changes
                    lyrEdit.pm.disable();
                    // We need to remove the layer from the map, bc it will still be there once we are done editing
                    lyrEdit.remove();
                    // this is going to delete all the event handlers for that edit event
                    mymap.off("contextmenu");
                }
            })
        } else if (isShowing("btnBUOWLInsert")) {
            mymap.pm.enableDraw('Poly', {finishOn: 'contextmenu'}); // contextmenu is the event that occurs when I right click
            alert("Creating new geometires")
        } else {
            alert("Editing not enabled")
        }
    })

    // Submitting edit
    $("#btnBUOWLUpdate").click(function () {
        const jsn = returnFormData("inpBUOWL");
        // This is a table property that it will be sent to an php script
        jsn.tbl = "dj_buowl";
        updateRecord(jsn);
    })

    $("#btnDeleteBUOWL").click(function () {
        const id = $("#buowl_id").val();
        if (confirm("Are you sure you want to delete this BUOWL "+id+"?")) {
            deleteRecord("dj_buowl", id); // this was a string and it deleted all
            $("#formBUOWL").hide();
            // erasing the information in the html once delete from the db
            $("#divBUOWLAffected").html("");
            $("#txtFindBUOWL").val("");
            $("#btnBUOWLsurveys").hide();
            // closing the data section
            lyrSearch.remove();
        }
    })

    // ************ Client Linears **********

    $("#btnLinearProjects").click(function () {
        $("#legendLinearProjectDetails").toggle();
    });

    $("#txtFindProject").on('keyup paste', function(){
        const val = $("#txtFindProject").val();
        testLayerAttribute(arProjectIDs, val, "PROJECT ID", "#divFindProject", "#divProjectError", "#btnFindProject");
    });

    $("#btnFindProject").click(function(){
        findProject($("#txtFindProject").val());
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

    $("#btnRefreshLinears").click(function(){
        refreshLinears();
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


    // *********  Eagle Functions *****************

    $("#btnEagle").click(function () {
        $("#lgndEagleDetail").toggle();
    });


    $("#txtFindEagle").on('keyup paste', function(){
        const val = $("#txtFindEagle").val();
        testLayerAttribute(arEagleIDs, val, "Eagle Nest ID", "#divFindEagle", "#divEagleError", "#btnFindEagle");
    });

    $("#btnFindEagle").click(function(){
        findEagle($("#txtFindEagle").val());

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

    //  *********** Raptor Functions

    $("#btnRaptor").click(function () {
        $("#lgndRaptorDetail").toggle();
    });

    $("#txtFindRaptor").on('keyup paste', function(){
        var val = $("#txtFindRaptor").val();
        testLayerAttribute(arRaptorIDs, val, "Raptor Nest ID", "#divFindRaptor", "#divRaptorError", "#btnFindRaptor");
    });

    $("#btnFindRaptor").click(function(){
        findRaptor($("#txtFindRaptor").val());
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

    // Zoom to Topraisar
    $("#btnZoomToTop").click(function () {
        mymap.setView([44.01516, 28.4739], 11);
    })

    $("#btnRaptorSurveys").click(function () {
        changeOptions("survey_result", "dj_raptor_survey", "result");
        displaySurveys("dj_raptor_survey", $("#txtFindRaptor").val());
    });

    /* Eagle Surveys */

    $("#btnEagleSurveys").click(function () {
        changeOptions("survey_result", "dj_eagle_surveys", "result");
        displaySurveys("dj_eagle_surveys", $("#txtFindEagle").val());
    });

    /* BUOWL Surveys */

    $("#btnBUOWLsurveys").click(function () {
        changeOptions("survey_result", "dj_buowl_survey", "result");
        displaySurveys("dj_buowl_survey", $("#txtFindBUOWL").val());
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
</script>
</body>
</html>