<?php

//	This configuration file is dedicated to some GLOBALS variables 
//	to define some possible customizations	

// Default language:
define('LANG_FALLBACK', 'en_GB');

//Default path:
$GLOBALS['BASE_PATH']=$_SERVER["DOCUMENT_ROOT"].'/cultibox/';

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

// Number of maximal canal used by dimmer
$GLOBALS['NB_MAX_CANAL_DIMMER'] = '8';

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
$GLOBALS['UPDATE_FILE'] = 'http://cultibox.fr/download/software/updates/VERSION';

//Website to download software:
$GLOBALS['WEBSITE'] = 'http://cultibox.fr/telechargement.html';

// Remote site to test internet connection
$GLOBALS['REMOTE_SITE'] = 'cultibox.fr';

// List of value possible for log's research: 2, 3, 6 or 12 months
$GLOBALS['LOG_SEARCH'] = array('2', '3', '6', '12');

// Colors for the different sensors:
$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_BLUE'] = array ('blue','#7A7AE5','#0000F0','#020289','#0D0D57');
$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_RED'] = array ('red','#FA4F4F','#E30000','#AB0000','#890000');
$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_GREEN'] = array ('green','#68F071','#00F010','#006A07','#416543');
$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_BLACK'] = array ('black','#000000','#494141','#B9A5A5','#FFFFFF');
$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_PURPLE'] = array ('purple','#F089B6','#F00068','#8D003D','#9F3F69');
$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_ORANGE'] = array ('orange','#F9A856','#F79A3C','#F99027','F07C07');
$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_PINK'] = array ('pink','#F433BA','#C32493','#951B71','#5E1147');
$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_BROWN'] = array ('brown','#C18C36','#A37223','#805613','#593A08');
$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_YELLOW'] = array ('yellow','F9F781','#FCF959','#FAF726','#EDE901');

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

// List of encryption supported by the wifi module:
$GLOBALS['WIFI_KEY_TYPE_LIST']=array("NONE", "WEP", "WPA", "WPA2", "WPA-AUTO");

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

?>


