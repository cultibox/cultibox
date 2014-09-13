<?php

namespace calendar {

// {{{ check_db()
// ROLE check and update dabase
// RET none
function check_db() {

    // Define columns of the calendar table
    $calendar_col = array();
    $calendar_col["Id"] = array ( 'Field' => "Id", "Type" => "int(11)");
    $calendar_col["Title"] = array ( 'Field' => "Title", "Type" => "VARCHAR(1000)");
    $calendar_col["Description"] = array ( 'Field' => "Description", "Type" => "VARCHAR(500)");
    $calendar_col["StartTime"] = array ( 'Field' => "StartTime", "Type" => "DATETIME");
    $calendar_col["EndTime"] = array ( 'Field' => "EndTime", "Type" => "DATETIME");
    $calendar_col["External"] = array ( 'Field' => "EndTime", "Type" => "SMALLINT(6)", "default_value" => 0);
    $calendar_col["Color"] = array ( 'Field' => "Color", "Type" => "VARCHAR(7)","default_value" => "#4A40A4");
    $calendar_col["Icon"] = array ( 'Field' => "Icon", "Type" => "VARCHAR(30)");
    $calendar_col["Important"] = array ( 'Field' => "Important", "Type" => "int(1)");
    $calendar_col["program_index"] = array ( 'Field' => "program_index", "Type" => "VARCHAR(30)");


    // Check column
    check_and_update_column_db ("calendar", $calendar_col);

}


// {{{ write_plgidx()
// ROLE write on sd card file plgidx
// IN $sd_card         sd card location
//    $data            data to write into the sd card (come from calendar\read_event_from_db )
// RET none
function write_plgidx ($sd_card,$data) {

    // If sd card is not available return false
    if (empty($sd_card))
        return false;
        
    // If there is not event , return false
    if(count($data) == 0) 
        return false;

    // Create plgidx array
    $plgidx = array();
        
    // Open database connexion
    $db = \db_priv_pdo_start();
    
    // Foreach event
    foreach($data as $event)
    {
        // If this is a program index event
        if ($event['program_index'] != "")
        {

            // Query plugv filename associated
            try {
                $sql = "SELECT plugv_filename FROM program_index WHERE id = \"" . $event['program_index'] . "\";";
                $sth = $db->prepare($sql);
                $sth->execute();
                $res = $sth->fetch();
            } catch(\PDOException $e) {
                $ret=$e->getMessage();
            }
        
            //
            $today = strtotime(date("Y-m-d"));
            $nextYear  = strtotime("+1 year", strtotime(date("Y-m-d")));
        
            // Start date
            $date = $event['start_year'] . "-" . $event['start_month']  . "-" . $event['start_day'];
            // End date
            $end_date = $event['end_year'] . "-" . $event['end_month']  . "-" . $event['end_day'];
            
            while (strtotime($date) <= strtotime($end_date)) {

                // Save only for futur element
                if (strtotime($date) >= $today && strtotime($date) < $nextYear)
                    $plgidx[$date] = $res['plugv_filename'];
                  
                // Incr date                  
                $date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
            }
        }
    }

    // Close connexion
    $db = null;
    
    // Init filename
    $file="$sd_card/cnf/prg/plgidx";
    
    // Open It
    if($fid = fopen("$file","w+")) {
    
        // For each day
        for ($month = 1; $month <= 13; $month++) 
        {
            for ($day = 1; $day <= 31; $day++) 
            {
                // Format day and month
                $monthToWrite = $month;
                if (strlen($monthToWrite) < 2) {
                    $monthToWrite="0$monthToWrite";
                }
            
                $dayToWrite = $day;
                if (strlen($dayToWrite) < 2) {
                    $dayToWrite="0$dayToWrite";
                }
            
                // Date to search in event
                $dateToSearch = date("Y") . "-" . $monthToWrite . "-" . $dayToWrite;
            
                $plugvToUse = "00";
                if (array_key_exists($dateToSearch, $plgidx)) {
                    $plugvToUse = $plgidx[$dateToSearch];
                    
                    if (strlen($plugvToUse) < 2)
                        $plugvToUse = "0$plugvToUse";
                    
                }

                // Write the day
                fputs($fid,$monthToWrite . $dayToWrite . $plugvToUse . "\r\n");
            }
        }
    
    } else {
        $status=false;
    }

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
        $end_event    = strtotime($event->start) + 86399;
    
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
