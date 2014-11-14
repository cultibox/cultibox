<?php

namespace calendar {

// {{{ check_db()
// ROLE check and update dabase
// RET none
function check_db() {

    // Define columns of the calendar table
    $calendar_col = array();
    $calendar_col["Id"] = array ( 'Field' => "Id", "Type" => "int(11)", 'carac' => "NOT NULL AUTO_INCREMENT");
    $calendar_col["Title"] = array ( 'Field' => "Title", "Type" => "VARCHAR(1000)", "default_value" => "NULL");
    $calendar_col["Description"] = array ( 'Field' => "Description", "Type" => "VARCHAR(500)", "default_value" => "NULL");
    $calendar_col["StartTime"] = array ( 'Field' => "StartTime", "Type" => "DATETIME", "default_value" => "NULL");
    $calendar_col["EndTime"] = array ( 'Field' => "EndTime", "Type" => "DATETIME", "default_value" => "NULL");
    $calendar_col["External"] = array ( 'Field' => "External", "Type" => "SMALLINT(6)", "default_value" => 0, 'carac' => "NOT NULL");
    $calendar_col["Color"] = array ( 'Field' => "Color", "Type" => "VARCHAR(7)","default_value" => "#4A40A4", 'carac' => "NOT NULL");
    $calendar_col["Icon"] = array ( 'Field' => "Icon", "Type" => "VARCHAR(30)");
    $calendar_col["Important"] = array ( 'Field' => "Important", "Type" => "int(1)", 'carac' => "NOT NULL", "default_value" => 0);
    $calendar_col["program_index"] = array ( 'Field' => "program_index", "Type" => "VARCHAR(30)","default_value" => "NULL");

    
    // Check if table program_index exists
    $sql = "SHOW TABLES FROM cultibox LIKE 'calendar';";

    $db = \db_priv_pdo_start("root");
    try {
        $sth=$db->prepare($sql);
        $sth->execute();
        $res = $sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
        $ret=$e->getMessage();
    }

    // If table exists, return
    if ($res == null)
    {

        // Build MySQL command to create table
        $sql = "CREATE TABLE calendar ("
                ."Id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,"
                ."Title varchar(1000) DEFAULT NULL,"
                ."Description varchar(500) DEFAULT NULL,"
                ."StartTime datetime DEFAULT NULL,"
                ."EndTime datetime DEFAULT NULL,"
                ."External SMALLINT(6) NOT NULL DEFAULT '0',"
                ."Color varchar(7) NOT NULL DEFAULT '#4A40A4'," 
                ."Icon VARCHAR(30) NULL,"
                ."Important INT(1) NOT NULL DEFAULT '0',"
                ."program_index VARCHAR(30) DEFAULT NULL,"
                ."  KEY Id (Id));";

        // Create table
        try {
            $sth = $db->prepare($sql);
            $sth->execute();
        } catch(\PDOException $e) {
            $ret = $e->getMessage();
            print_r($ret);
        }
    } else {
        // Check column
        check_and_update_column_db ("calendar", $calendar_col);
    }
    $db = null;

}


// {{{ concat_entries()
// ROLE concat calendar entries to use several comments for the same day
// IN   $data        data to be proccessed
// IN   $date        date to be checked
// RET return an array containing all datas for the day checked
function concat_entries($data,$date) {
    $new_data=array();

    // Foreach event
    foreach($data as $val) {

       $start   = date("Y-m-d", strtotime($val['start']));
       $end     = date("Y-m-d", strtotime($val['end']));
                     
        // Don't concat programm_index event
       if(($start <= $date) && ($end >= $date) && $val['program_index'] == "" ) {
           $new_data[]=$val;
       }
    }

    return $new_data;
}
// }}}

// {{{ read_event_from_db()
// ROLE read calendar from the database and format its to be writen into a sd card
// IN   $start         ms unix format
// IN   $end           ms unix format
// RET an array containing datas
function read_event_from_db (&$tab_event,$start="",$end="") {

        $id = 0;

        $date = date("Y-m-d H:i:s");
        
        if ($start == "")
            $start = date("Y-m-d H:i:s");
        else
            $start = date("Y-m-d H:i:s", $start);
            
        if ($end == "")
            $end  = date("Y-m-d H:i:s", strtotime("+3 months", strtotime($date)));
        else
            $end = date("Y-m-d H:i:s", $end);
            

        $data=array();
        $db = \db_priv_pdo_start();

        $sql = "SELECT * FROM calendar WHERE (StartTime <= \"" . $end . "\") OR (`EndTime` >= \"" . $start . "\") ORDER BY EndTime ASC;";

        foreach($db->query($sql) as $val) {

            $start_month=substr($val['StartTime'],5,2);
            $start_day=substr($val['StartTime'],8,2);
            $start_year=substr($val['StartTime'],0,4);

            $end_month=substr($val['EndTime'],5,2);
            $end_day=substr($val['EndTime'],8,2);
            $end_year=substr($val['EndTime'],0,4);

            // Save greater ID
            if ($val['Id'] > $id)
                $id = $val['Id'];
            
            if($val['Important']==1) {
                $color_event="red";
            } else {
                $color_event="white";
            }
            
            $program_index = $val['program_index'];
            if (!isset($val['program_index']) || empty($val['program_index']))
                $program_index = "";
            
            $tab_event[] = array(
                "id"    => $val['Id'],
                "start" => $val['StartTime'],
                "start_year" => $start_year,
                "start_month" => $start_month,
                "start_day" => $start_day,
                "end" => $val['EndTime'],
                "end_year" => $end_year,
                "end_month" => $end_month,
                "end_day" => $end_day,  
                "subject" => $val['Title'],
                "title" => $val['Title'],
                "description" => $val['Description'],
                "program_index" => $val['program_index'],
                "textColor" => $color_event,
                "color" => $val['Color'],
                "icon" => $val['Icon'],
                "important" => $val['Important'],
                "external" => 0
            );
            unset($s);
            unset($desc);
      }

    // CLose DB connexion
    $db=null;
      
    return $id;
}
// }}}

// {{{ read_event_from_XML()
// ROLE read calendar from the database and format its to be writen into a sd card
// IN   $start    Start date (unix format in s)
// IN   $end      End Date (unix format in s)
// RET an array containing datas
function read_event_from_XML ($file, &$tab_event, $id = 1, $start="",$end="") {
    
    // Check if it's a file
    if (!is_file($file))
        exit ('read_event_from_XML : ' . $file . 'is not a file');

    // Convert start and end in time
    if ($start == "")
        $start = strtotime("-1 month" , strtotime(date(DATE_ATOM)));
        
    if ($end == "")
        $end = strtotime("+3 month" , strtotime(date(DATE_ATOM)));

    // Read XML File
    $xml_string = file_get_contents($file);
    
    // Convert content of XML
    $xml = new \SimpleXMLElement($xml_string);

    // Fore each entry of the file
    foreach($xml->entry as $event) {
    
        $start_event  = strtotime($event->start);
        $end_event    = strtotime($event->start) + 80000;
   
        // Check if event is in the good plage
        if ($start_event >= $start && $start_event <= $end)
        {
            $tab_event[] = array(
                "id"    => $id,
                "subject" => ((string)$event->title),
                "title" => ((string)$event->title),
                "start" => date("Y-m-d H:i:s",$start_event),
                "end"   => date("Y-m-d H:i:s",$end_event),
                "description" => ((string)$event->content),
                "color" => ((string)$event->color),
                "icon" => ((string)$event->icon),
                "icon0" => ((string)$event->icon0),
                "icon1" => ((string)$event->icon1),
                "icon2" => ((string)$event->icon2),
                "icon3" => ((string)$event->icon3),
                "textColor" => ((string)$event->text_color),
                "program_index" => "",
                "cbx_symbol" => ((string)$event->cbx_symbol),
                "external" => 1
            );
            
            $id = $id + 1;
        }
    }
        
    return $id;
}


// {{{ get_external_calendar_file()
// ROLE get an array containing list of xml external files available for calendar (like moon calendar)
// RET array containing datas
function get_external_calendar_file() {
    $ret=array();

    // Define directory
    if (is_dir('main/xml/permanent'))
        $dir = 'main/xml/permanent';
    elseif (is_dir('../../xml/permanent'))
        $dir = '../../xml/permanent';
    else
        return false;

    $id = 0;
        
    // Get every activ xml
    $filesActiv = glob($dir. '/*.{xml}', GLOB_BRACE);
    foreach ($filesActiv as $file)
    {
        $ret[basename($file)] = array (
            "filename" => $file,
            "name" => basename($file),
            "activ" => 1,
            "value" => 1,
            "id" => "xmlchange" . $id
            );
        $id = $id + 1;
    }
    
    // Get every not activ xml
    $filesNotActiv = glob($dir. '/_not_used/*.{xml}', GLOB_BRACE);
    foreach ($filesNotActiv as $file)
    {
        $ret[basename($file)] = array (
            "filename" => $file,
            "name" => basename($file),
            "activ" => 0,
            "value" => 1,
            "id" => "xmlchange" . $id
            );
        $id = $id + 1;
    }

    array_multisort($ret, SORT_ASC);
    
    return $ret;
}
// }}}

// {{{ set_external_calendar_file()
// ROLE get an array containing list of xml external files available for calendar (like moon calendar)
// RET array containing datas
function set_external_calendar_file($file, $beActiv) {

    // Define directory
    if (is_dir('main/xml/permanent'))
        $dir = 'main/xml/permanent';
    elseif (is_dir('../../xml/permanent'))
        $dir = '../../xml/permanent';
    else
        return false;

    // Move file
    if ($beActiv == "true")
    {
        rename($dir . "/_not_used/" . $file, $dir . "/" . $file);
    }
    else
    {
        rename($dir . "/" . $file, $dir . "/_not_used/" . $file);
    }

    return true;
}
// }}}

// {{{ get_title_list()
// ROLE get list of available titles from the calendar database
// RET return array containing data
function get_title_list() {
    $title=array();

    foreach($GLOBALS['LIST_SUBJECT_CALENDAR'] as $value) {
        switch ($value) {
            case 'Beginning':
                $title[]=__('SUBJECT_START','calendar');
               break;
            case 'Fertilizers':
               $title[]=__('SUBJECT_FERTILIZERS','calendar');
               break;
            case 'Water':
               $title[]=__('SUBJECT_WATER','calendar');
               break;
            case 'Bloom':
               $title[]=__('SUBJECT_BLOOM','calendar');
               break;
            case 'Harvest':
               $title[]=__('SUBJECT_HARVEST','calendar');
               break;
            case 'Other':
               $tmp=__('SUBJECT_OTHER','calendar');
               break;
        }
    }

    $sql = <<<EOF
SELECT DISTINCT `title` from `calendar`
EOF;
   $db=\db_priv_pdo_start();
   $res="";
   try {
       $sth=$db->prepare("$sql");
       $sth->execute();
       $res=$sth->fetchAll(\PDO::FETCH_ASSOC);
   } catch(\PDOException $e) {
       $ret=$e->getMessage();
   }
   $db=null;

   if(!empty($res)) {
        foreach($res as $result) {
            foreach($result as $data) {
               if(strcmp(rtrim($data),$tmp)!=0) {
                $title[]=rtrim($data);
               }
            }
        }
   }

   //To put the 'other' value at the end:
   $title_return=array_unique($title);
   $title_return[]=$tmp;

   return $title_return;
}
// }}}


// {{{ get_important_event_list()
// ROLE get list of important event for next or past week
// IN $out      error or warning message
// RET array containing datas or nothing if no data catched
function get_important_event_list(&$out) {
    $start=date('Y-m-j',strtotime('-1 days'));
    $end=date('Y-m-j',strtotime('+7 days'));
    $sql = <<<EOF
SELECT title,StartTime,EndTime,color,Description from `calendar` WHERE `important`=1 AND ((`StartTime` BETWEEN '{$start}' AND '{$end}')OR (`EndTime` BETWEEN '{$start}' AND '{$end}')OR(`StartTime` <= '{$start}' AND `EndTime` >= '{$end}'))
EOF;

    $db=\db_priv_pdo_start();
    $res="";
    try {
        $sth=$db->prepare("$sql");
        $sth->execute();
        $res=$sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
        $ret=$e->getMessage();
    }
    $db=null;

    return $res;
}
/// }}}

// {{{ insert_calendar()
// ROLE insert a new calendar event
// IN   $out         error or warning message
//      $event[]     the event to be recorded
// RET false if errors occured, true else
function insert_calendar($event,&$out) {
    
    // If there is no event to add, return
    if(count($event)==0)
        return false;
    
    // Create sql line
    $sql="";
    foreach($event as $evt) {
    
        // Check if program_index exists. If not add empty
        if (!array_key_exists("program_index",$evt))
            $evt["program_index"] = "";
    
        $sql .= "INSERT INTO calendar" 
             . " (Title, StartTime, EndTime, Description, Color, Icon, program_index) "
             . "  VALUES ('{$evt['title']}', '{$evt['start']}', '{$evt['end']}', '{$evt['description']}', '{$evt['color']}', '{$evt['icon']}', '{$evt['program_index']}');";
    }

    $db = \db_priv_pdo_start();
    $db->exec($sql);
    $db=null;
    return true;
}
// }}}


}

?>
