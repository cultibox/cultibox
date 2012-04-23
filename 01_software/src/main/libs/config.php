<?php

//	This configuration file is dedicated to some GLOBALS variables 
//	to define some possible customizations	

// Default language
define('LANG_FALLBACK', 'en_GB');

// List of available languages (l10N format)
$GLOBALS['LIST_LANG'] = array('en_GB','fr_FR');

// List of available colors for graphics
$GLOBALS['LIST_GRAPHIC_COLOR'] = array('blue','red', 'green', 'black');

// List of the recording frequency available: (in minute)
$GLOBALS['LIST_RECORD_FREQUENCY'] = array('1','5','30');

// List of plugs available
$GLOBALS['LIST_NB_PLUGS'] = array('1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16');

// List of the updating plugs frequency: (-1 means every clock heartbeat)
$GLOBALS['LIST_UPDATE_FREQUENCY'] = array('-1','1','5');

// Colors for plugs program:
$GLOBALS['LIST_GRAPHIC_COLOR_PROGRAM'] = array('blue','red', 'green', 'black','purple','pink','yellow','brown','grey','orange','violet','beige','turquoise','blue','green','black');

?>
