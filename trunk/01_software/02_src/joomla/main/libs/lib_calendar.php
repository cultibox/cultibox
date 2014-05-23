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
//    $data            data to write into the sd card (come from create_calendar_from_database )
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

}

?>
