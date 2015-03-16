<?php

//	This configuration file is dedicated to some GLOBALS variables for cultipi
//	to define some possible customizations	

//Slave IP:
$GLOBALS_CULTIPI['USE_REMOTE_SLAVE'] = 0; // Can be 0 or 1
$GLOBALS_CULTIPI['REMOTE_NB_SLAVE'] = 5;
$GLOBALS_CULTIPI['REMOTE_SLAVE'] = array ( 
    'IP_0' => "192.168.0.50", 'NAME_0' => "Centrale",
    'IP_1' => "192.168.0.51", 'NAME_1' => "Mogador",
    'IP_2' => "192.168.0.52", 'NAME_2' => "Montmartre",
    'IP_3' => "192.168.0.54", 'NAME_3' => "DAntin",
    'IP_4' => "192.168.0.55", 'NAME_4' => "Opéra"
);

// Remote sensors
$GLOBALS_CULTIPI['USE_REMOTE_SENSOR'] = 0; // Can be 0 or 1
$GLOBALS_CULTIPI['REMOTE_SENSOR'][] = array ( 
    "SENSOR_INDEX_IN_MASTER" => 2,
    "SENSOR_INDEX_IN_SLAVE" => 1,
    "REMOTE_SLAVE" => 0
);
$GLOBALS_CULTIPI['REMOTE_SENSOR'][] = array ( 
    "SENSOR_INDEX_IN_MASTER" => 3,
    "SENSOR_INDEX_IN_SLAVE" => 1,
    "REMOTE_SLAVE" => 1
);
$GLOBALS_CULTIPI['REMOTE_SENSOR'][] = array ( 
    "SENSOR_INDEX_IN_MASTER" => 4,
    "SENSOR_INDEX_IN_SLAVE" => 1,
    "REMOTE_SLAVE" => 2
);
$GLOBALS_CULTIPI['REMOTE_SENSOR'][] = array ( 
    "SENSOR_INDEX_IN_MASTER" => 5,
    "SENSOR_INDEX_IN_SLAVE" => 1,
    "REMOTE_SLAVE" => 3
);
$GLOBALS_CULTIPI['REMOTE_SENSOR'][] = array ( 
    "SENSOR_INDEX_IN_MASTER" => 6,
    "SENSOR_INDEX_IN_SLAVE" => 1,
    "REMOTE_SLAVE" => 4
);

// Direct read
$GLOBALS_CULTIPI['USE_DIRECT_READ'] = 0; // Can be 0 or 1
// Type can be SHT DS18B20 WATER_LEVEL PH EC OD ORP
$GLOBALS_CULTIPI['DIRECT_SENSOR'][] = array ( 
    "SENSOR_INDEX" => 1,
    "SENSOR_FIRST_INPUT" => 1,
    "SENSOR_FIRST_VALUE" => 1,
    "SENSOR_SECOND_INPUT" => 2,
    "SENSOR_SECOND_VALUE" => 2,
    "SENSOR_TYPE" => "WATER_LEVEL",
);

?>


