<?php

//	This configuration file is dedicated to some GLOBALS variables 
//	to define some possible customizations	

//Default timezone:
date_default_timezone_set('UTC');

// Default language:
define('LANG_FALLBACK', 'en_GB');

//Default path:
$GLOBALS['BASE_PATH'] = $_SERVER["DOCUMENT_ROOT"] . '/cultibox/';

// Default port use by the application:
$GLOBALS['SOFT_PORT'] = '6891';

// List of available colors for graphics:
$GLOBALS['LIST_GRAPHIC_COLOR'] = array('blue','red', 'green', 'black','purple','orange','pink','brown','yellow');

// List of the recording frequency available: (in minute)
$GLOBALS['LIST_RECORD_FREQUENCY'] = array('1','5','30');

// Number of max plugs managed by the cultibox
$GLOBALS['NB_MAX_PLUG'] = '16';

// TO BE DELETED :
// Number of plug selectable
$GLOBALS['NB_SELECTABLE_PLUG'] = '10';

// Number of min plugs used by the cultibox
$GLOBALS['NB_MIN_PLUG'] = '3';

// Number of maximal sensor used to get logs informations
$GLOBALS['NB_MAX_SENSOR_LOG'] = '6';

// Number of maximal sensor used to manage plugs
$GLOBALS['NB_MAX_SENSOR_PLUG'] = '6';

// Number of maximal direct ouput with Cultipi
$GLOBALS['NB_MAX_CANAL_DIRECT'] = '8';

// Number of maximal MCP230XX ouput with Cultipi
$GLOBALS['NB_MAX_CANAL_MCP230XX'] = '8';

// Number of maximal MCP230XX module with Cultipi
$GLOBALS['NB_MAX_MODULE_MCP230XX'] = '3';

// Number of maximal canal used by dimmer
$GLOBALS['NB_MAX_CANAL_DIMMER'] = '8';

// Number of maximal module dimmer ouput with Cultipi
$GLOBALS['NB_MAX_MODULE_DIMMER'] = '3';

// Number of maximal network ouput with Cultipi
$GLOBALS['NB_MAX_CANAL_NETWORK'] = '16';

// Number of maximal network ouput with Cultipi
$GLOBALS['NB_MAX_CANAL_XMAX'] = '3';

// Number of maximal module dimmer ouput with Cultipi
$GLOBALS['NB_MAX_MODULE_XMAX'] = '1';


// List of the updating plugs frequency: 
$GLOBALS['LIST_UPDATE_FREQUENCY'] = array('1','5');

// Colors for plugs program:
$GLOBALS['LIST_GRAPHIC_COLOR_PROGRAM'] = array('#0033CC','#FF0000', '#336600', '#F6F61A','#FF9900','#006666','#999966','#663300','#FF0066','#CC66FF','#660000','#3D96AE','#DB843D','#00FF00','#CCFF33','#B5CA92');
$GLOBALS['LIST_GRAPHIC_COLOR_POWER'] = array('#0099CC','#990000', '#33DD00', '#A8B02F','#DD6600','#008888','#A7A37E','#E56B6B','#BB0066','#AA33BB','#DD0000','#7FC6BC','#BB641D','#00BB00','#C7C701','#84815B');

//Subject for the calendar:
$GLOBALS['LIST_SUBJECT_CALENDAR'] = array('Beginning','Fertilizers', 'Water', 'Bloom','Harvest','Other');

//To print debug trace:
$GLOBALS['DEBUG_TRACE'] = false;

// Definition of the plugs addresses default values:
$GLOBALS['PLUGA_DEFAULT'] = array('000', '247', '222', '219', '215', '207', '252', '250', '246', '238', '187', '183', '189', '125', '123','119');

// Definition of the plugs addresses default values:
$GLOBALS['PLUGA_DEFAULT_3500W'] = array('004', '000', '000', '006', '008', '010', '012', '014', '016', '018', '020', '022', '024', '026', '028','030');

// Variable to define the first year to be used for the log part:
$GLOBALS['FIRST_LOG_YEAR'] = '2013';

// Remote file to check update for the interface
$GLOBALS['UPDATE_FILE'] = 'http://www.greenbox-botanic.com/cultibox/download/software/updates/VERSION';

//Website to download software:
$GLOBALS['WEBSITE'] = 'http://cultibox.fr/telechargement.html';

// Remote site to test internet connection
$GLOBALS['REMOTE_SITE'] = 'www.greenbox-botanic.com';

// List of value possible for log's research: 2, 3, 6 or 12 months
$GLOBALS['LOG_SEARCH'] = array('2', '3', '6', '12');

// Colors for the different sensors:
$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_BLUE'] = array ('blue','#7A7AE5','#0000F0','#020289','#0D0D57','blue','#7A7AE5','#0000F0','#020289','#0D0D57');
$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_RED'] = array ('red','#FA4F4F','#E30000','#AB0000','#890000','red','#FA4F4F','#E30000','#AB0000','#890000');
$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_GREEN'] = array ('green','#68F071','#00F010','#006A07','#416543','green','#68F071','#00F010','#006A07','#416543');
$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_BLACK'] = array ('black','#000000','#494141','#B9A5A5','#FFFFFF','black','#000000','#494141','#B9A5A5','#FFFFFF');
$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_PURPLE'] = array ('purple','#F089B6','#F00068','#8D003D','#9F3F69','purple','#F089B6','#F00068','#8D003D','#9F3F69');
$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_ORANGE'] = array ('orange','#F9A856','#F79A3C','#F99027','#F07C07','orange','#F9A856','#F79A3C','#F99027','#F07C07');
$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_PINK'] = array ('pink','#F433BA','#C32493','#951B71','#5E1147','pink','#F433BA','#C32493','#951B71','#5E1147');
$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_BROWN'] = array ('brown','#C18C36','#A37223','#805613','#593A08','brown','#C18C36','#A37223','#805613','#593A08');
$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_YELLOW'] = array ('yellow','F9F781','#FCF959','#FAF726','#EDE901','yellow','F9F781','#FCF959','#FAF726','#EDE901');

// Colors for the  log's grid line:
$GLOBALS['GRAPHIC_COLOR_GRID_BLUE'] = "#609CD1";
$GLOBALS['GRAPHIC_COLOR_GRID_RED'] = "#C1626A";
$GLOBALS['GRAPHIC_COLOR_GRID_GREEN'] = "#6A905C";
$GLOBALS['GRAPHIC_COLOR_GRID_BLACK'] = "#B5ACAD";
$GLOBALS['GRAPHIC_COLOR_GRID_PURPLE'] = "#A67AA6";
$GLOBALS['GRAPHIC_COLOR_GRID_ORANGE'] = "#F28609";
$GLOBALS['GRAPHIC_COLOR_GRID_PINK'] = "#F772A1";
$GLOBALS['GRAPHIC_COLOR_GRID_BROWN'] = "#A8600E";
$GLOBALS['GRAPHIC_COLOR_GRID_YELLOW'] = "#FCE91D";

// Color for the calendar:
$GLOBALS['LIST_GRAPHIC_COLOR_CALENDAR'] = array ('00', '44', '88', 'DD', 'CC','FF');

// Remote script to send informations:
$GLOBALS['REMOTE_DATABASE'] = "http://www.cbx.greenbox-botanic.com/index.php";

// List and equivalence between sensor number and type of the sensor from the index file:
$GLOBALS['SENSOR_DEFINITION']=array(
                                '0' => 'none',
                                '2' => 'tem_humi',
                                '3' => 'water_temp',
                                '5' => 'wifi',
                                '6' => 'water_level',
                                '7' => 'water_level',
                                '8' => 'ph',
                                '9' => 'ec',
                                ':' => 'od',
                                ';' => 'orp');

//Number of state's changement allowed by the cultibox for the plugv file:
$GLOBALS['PLUGV_MAX_CHANGEMENT']=999;

//To hide/show RTC configuration:
$GLOBALS['SHOW_RTC']=false;

//Limit for a program depending plug's definition:
$GLOBALS['LIMIT_PLUG_PROGRAM']=array(
                                    'temp' => array(
                                         'min' => '5',
                                         'max' => '60'),
                                    'humi' => array(
                                         'min' => '5',
                                         'max' => '95'),
                                    'cm' => array(
                                         'min' => '5',
                                         'max' => '27'),
                                    'other' => array(
                                         'min' => '0',
                                         'max' => '99.9')
                            );

//Wifi key type:
$GLOBALS['WIFI_KEY_TYPE_LIST'] = array("NONE", "WEP", "WPA AUTO");

//Webcam resolution:
$GLOBALS['WEBCAM_RESOLUTION'] = array("320x240", "320x480", "400x300","480x360", "640x480", "800x600","960x720", "1024x768");

//Webcam palette:
$GLOBALS['WEBCAM_PALETTE'] = array("PNG","JPEG","MJPEG","S561","RGB32","RGB24","BGR32","BGR24","YUYV","UYVY","YUV420P","BAYER","SGBRG8","SGRBG8","RGB565","RGB555","Y16","GREY");

// Software mode : cultipi or cultibox:
$GLOBALS['MODE'] = "cultibox";

// For cultipi : path to the conf (for windows : C:/cultibox/xampp/htdocs/cultibox)
$GLOBALS['CULTIPI_CONF_PATH'] = "/etc/cultipi";

$GLOBALS['CULTIPI_CONF_TEMP_PATH'] = $GLOBALS['CULTIPI_CONF_PATH'] . "/conf_tmp";
?>


