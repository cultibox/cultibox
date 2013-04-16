<?php

$link = mysql_connect('localhost','cultibox','cultibox');
if (!$link) { die('Could not connect: ' . mysql_error()); }
mysql_select_db('cultibox');


// Initializes a container array for all of the calendar events
$jsonArray = array();

$event=array();

$sql = <<<EOF
SELECT * FROM `calendar`;
EOF;


$res = mysql_query($sql);
while($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
        $event[]=array(
                        "id" => $row['Id'],
                        "title" => utf8_encode($row['Title']),
                        "start" => $row['StartTime'],
                        "end" => $row['EndTime'],
                        "description" => utf8_encode($row['Description']),
                        "color" => $row['Color']
            );
}


echo json_encode($event);

?>
