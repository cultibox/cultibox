<?php

namespace calendar {

// {{{ check_db()
// ROLE check and update dabase
// RET none
function check_db() {

    // Check if table calendar have program_index field
    $sql = "show COLUMNS FROM calendar WHERE Field LIKE 'program_index';";
    
    $db = \db_priv_pdo_start("root");
    try {
        $sth=$db->prepare("$sql");
        $sth->execute();
        $res = $sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
        $ret=$e->getMessage();
    }

    // If column not exists, create them
    if ($res == null)
    {
        
        // Buil MySQL command to create column
        $sql = "ALTER TABLE calendar ADD program_index VARCHAR(30);";
        
        // Create table
        try {
            $sth = $db->prepare($sql);
            $sth->execute();
        } catch(\PDOException $e) {
            $ret = $e->getMessage();
            print_r($ret);
        }
    
    }
    
    // Check if table calendar have External field
    $sql = "show COLUMNS FROM calendar WHERE Field LIKE 'External';";
    try {
        $sth=$db->prepare("$sql");
        $sth->execute();
        $res = $sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
        $ret=$e->getMessage();
    }
    
    // If column exists, delete them
    if ($res != null)
    {
        
        // Buil MySQL command to create column
        $sql = "ALTER TABLE calendar DROP External;";
        
        // Create table
        try {
            $sth = $db->prepare($sql);
            $sth->execute();
        } catch(\PDOException $e) {
            $ret = $e->getMessage();
            print_r($ret);
        }
    
    }
    
    $db=null;

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

    $current = date("Y-m-d",strtotime($date));

    // Foreach event
    foreach($data as $val) {

       $start   =   strtotime($val['start_year'] . "-" . $val['start_month'] . "-" . $val['start_day']);
       $end     =   strtotime($val['end_year']   . "-" . $val['end_month']   . "-" . $val['end_day']);
    
        // Don't concat programm_index event
       if(($start <= $current)
            &&($end >= $current)
            && $val['program_index'] == "" ) {
           $new_data[]=$val;
       }
    }

    if(count($new_data)>0) {
        return $new_data;
    } else {
        return null;
    }
}
// }}}

// {{{ read_event_from_db()
// ROLE read calendar from the database and format its to be writen into a sd card
// IN   $out         error or warning message
// RET an array containing datas
function read_event_from_db (&$out,$start="",$end="") {

        $date = date("Y-m-d H:i:s");
        $start = $date;
        $end  = date("Y-m-d H:i:s", strtotime("+3 months", strtotime($date)));

        $data=array();
        $db = db_priv_pdo_start();

        $sql = <<<EOF
SELECT `Title`,`StartTime`,`EndTime`, `Description`, `program_index` FROM `calendar` WHERE (`StartTime` BETWEEN '{$start}' AND '{$end}') OR (`EndTime` BETWEEN '{$start}' AND '{$end}') OR (`StartTime` <= '{$start}' AND `EndTime` >= '{$end}')
EOF;

        foreach($db->query("$sql") as $val) {
            $val['Title']=clean_calendar_message($val['Title']);
            $val['Description']=clean_calendar_message($val['Description']);

            $start_month=substr($val['StartTime'],5,2);
            $start_day=substr($val['StartTime'],8,2);
            $start_year=substr($val['StartTime'],0,4);

            $end_month=substr($val['EndTime'],5,2);
            $end_day=substr($val['EndTime'],8,2);
            $end_year=substr($val['EndTime'],0,4);

            $s=mb_strtoupper($val['Title'], 'UTF-8');

            if((isset($val['Description']))&&(!empty($val['Description']))) {
                if(strcmp($val['Description'],"null")==0) {
                    $desc="";
                } else {
                    $desc=$val['Description'];
                }
            } else {
                $desc="";
            }

            $program_index="";
            if((isset($val['program_index']))&&(!empty($val['program_index']))) {
                if($val['program_index'] != "null") {
                    $program_index=$val['program_index'];
                }
            } 

            $data[]=array(
                "start_year" => $start_year,
                "start_month" => $start_month,
                "start_day" => $start_day,
                "end_year" => $end_year,
                "end_month" => $end_month,
                "end_day" => $end_day,  
                "subject" => $s,
                "description" => $desc,
                "program_index" => $program_index
            );
            unset($s);
            unset($desc);
      }

      $db=null;
      return $data;
}
// }}}

// {{{ read_event_from_XML()
// ROLE read calendar from the database and format its to be writen into a sd card
// IN   $out         error or warning message
// RET an array containing datas
function read_event_from_XML (&$out,$start="",$end="") {

    // Init event aray
    $data=array();
    
    // Open dir containg xml
    if ($handle = opendir('../xml')) {
    
        // While there are some files
        while (($entry = readdir($handle)) !== false) {
        
            // Check If it's a directory --> do nothing
            if (is_dir($entry))
                continue;
            
            // Check if it's a correct moon calendar
            if (!check_xml_calendar_file($entry))
                continue ;

            $rss_file = file_get_contents("../../xml/".$entry);
            $xml =json_decode(json_encode((array) @simplexml_load_string($rss_file)), 1);
            $id=10000;
            
            
            $value=array();
            $value = $xml;
            
            // In progress !!
            
            foreach($xml as $event) {

            }

        }
    }

}


}

?>
