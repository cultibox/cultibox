<?php

//	This configuration file is dedicated to some GLOBALS variables 
//	to define some possible customizations	

// Default language:
define('LANG_FALLBACK', 'en_GB');

// Default port use by the application:
$GLOBALS['SOFT_PORT'] = '6891';

// List of available languages: (l10N format)
$GLOBALS['LIST_LANG'] = array('en_GB','fr_FR');

// List of available colors for graphics:
$GLOBALS['LIST_GRAPHIC_COLOR'] = array('blue','red', 'green', 'black','purple');

// List of the recording frequency available: (in minute)
$GLOBALS['LIST_RECORD_FREQUENCY'] = array('1','5','30');

// Number of max plugs managed by the cultibox
$GLOBALS['NB_MAX_PLUG'] = '16';

// Number of min plugs used by the cultibox
$GLOBALS['NB_MIN_PLUG'] = '3';

// Number of maximal sensor used
$GLOBALS['NB_MAX_SENSOR'] = '4';

// List of the updating plugs frequency: (-1 means every clock heartbeat)
$GLOBALS['LIST_UPDATE_FREQUENCY'] = array('-1','1','5');

// Colors for plugs program:
$GLOBALS['LIST_GRAPHIC_COLOR_PROGRAM'] = array('#0033CC','#FF0000', '#336600', '#F6F61A','#FF9900','#006666','#999966','#663300','#FF0066','#CC66FF','#660000','#3D96AE','#DB843D','#00FF00','#CCFF33','#B5CA92');

//Subject for the calendar:
$GLOBALS['LIST_SUBJECT_CALENDAR'] = array('Beginning','Fertilizers', 'Water', 'Bloom','Harvest','Other');

//To print debug trace:
$GLOBALS['DEBUG_TRACE'] = false;

// Definition of the plugs addresses default values:
$GLOBALS['PLUGA_DEFAULT'] = array('000', '247', '222', '219', '215', '207', '252', '250', '246', '238', '187', '183', '189', '125', '123','119');

// Definition of the plugs addresses default values:
$GLOBALS['PLUGA_DEFAULT_3500W'] = array('004', '000', '000', '006', '008', '010', '012', '014', '016', '018', '020', '022', '024', '026', '028','030');

// List of value possible for temperature and hygrometry axis:
$GLOBALS['LIST_MAX_AXIS'] = array('20', '40', '60', '80','100');

// List of value possible for power graphic axis:
$GLOBALS['LIST_MAX_POWER'] = array('100', '200', '500', '1000','2000');

// Variable to enable/disable "first use" page:
$GLOBALS['FIRST_USE'] = true;

// Variable to define the first year to be used for the log part:
$GLOBALS['FIRST_LOG_YEAR'] = '2012';

// Remote file to check update for the interface
$GLOBALS['UPDATE_FILE'] = 'http://cultibox.highproshop.fr/download/software/updates/update_list.txt';

// Remote site to test internet connection
$GLOBALS['REMOTE_SITE'] = 'cultibox.highproshop.fr';

// Enable/Disable export and import module
$GLOBALS['MODULE_IMPORT_EXPORT'] = true;

// List of value possible for log's research: 2, 3, 6 or 12 months
$GLOBALS['LOG_SEARCH'] = array('2', '3', '6', '12');

// Colors for the different sensors:
$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_BLUE'] = array ('#7A7AE5','#0000F0','#020289','#0D0D57');
$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_RED'] = array ('#FA4F4F','#E30000','#AB0000','#890000');
$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_GREEN'] = array ('#68F071','#00F010','#006A07','#416543');
$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_BLACK'] = array ('#000000','#494141','#B9A5A5','#FFFFFF');
$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_PURPLE'] = array ('#F089B6','#F00068','#8D003D','#9F3F69');

?>
