<?php

namespace webcam {

// {{{ update_db()
// ROLE update dabase
// RET none
function check_db() {

    // Define columns of the calendar table
    $conf_index_col = array();
    $conf_index_col["id"]                   = array ( 'Field' => "id", 'Type' => "int(11)", 'carac' => "NOT NULL AUTO_INCREMENT");
    $conf_index_col["brightness"]           = array ( 'Field' => "brightness", 'Type' => "int(11)", 'default_value' => -1,'carac' => "NOT NULL");
    $conf_index_col["contrast"]             = array ( 'Field' => "contrast", 'Type' => "int(11)", 'default_value' => -1,'carac' => "NOT NULL");
    $conf_index_col["resolution"]           = array ( 'Field' => "resolution", 'Type' => "varchar(11)", 'default_value' => "-1",'carac' => "NOT NULL");
    $conf_index_col["palette"]              = array ( 'Field' => "palette", 'Type' => "varchar(11)", 'default_value' => "-1",'carac' => "NOT NULL");


    // Check if table webcam exists
    $sql = "SHOW TABLES FROM cultibox LIKE 'webcam';";
    
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
        
        // Buil MySQL command to create table
        $sql = "CREATE TABLE `webcam` ("
            ."id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,"
            ."brightness int(11) NOT NULL DEFAULT -1,"
            ."contrast int(11) NULL DEFAULT -1,"
            ."palette VARCHAR(11) NULL DEFAULT '-1',"
            ."resolution VARCHAR(11) NULL DEFAULT '-1');";
        
        // Create table
        try {
            $sth = $db->prepare($sql);
            $sth->execute();
        } catch(\PDOException $e) {
            $ret = $e->getMessage();
            print_r($ret);
        }

         $sql = "INSERT INTO webcam (brightness, contrast,resolution,palette) VALUES (-1,-1,'-1','-1');";
        // Insert row:
        try {
            $sth = $db->prepare($sql);
            $sth->execute();
        } catch(PDOException $e) {
            $ret = $e->getMessage();
            print_r($ret);
        }

    } else {
        // Check column
        check_and_update_column_db ("webcam", $conf_index_col);
    } 
    $db=null;
  }


  //Get webcam conf from files, ret array containing datas
  function get_webcam_conf() {
        $return=array();

        for($i=0;$i<$GLOBALS['MAX_WEBCAM'];$i++) {
            if(is_file("/etc/culticam/webcam$i.conf")) {
                $handle = fopen("/etc/culticam/webcam$i.conf", "r");
                if($handle) {
                    while(($line = fgets($handle)) !== false) {
                    // process the line read.
                    if(strpos($line, "resolution")!==false) {
                        $value=explode(" ",$line);
                        $return[$i]['resolution']=trim($value[1]);
                    }

                    if(strpos($line, "brightness")!==false) {
                        $value=explode("=",$line);
                        $value[1]=trim($value[1]);
                        $return[$i]['brightness']=substr($value[1],0,strlen($value[1])-1);
                    }

                    if(strpos($line, "contrast")!==false) {
                        $value=explode("=",$line);
                        $value[1]=trim($value[1]);
                        $return[$i]['contrast']=substr($value[1],0,strlen($value[1])-1);
                    }

                    if(strpos($line, "palette")!==false) {
                        $value=explode(" ",$line);
                        $return[$i]['palette']=trim($value[1]);
                    }
                    }
                    fclose($handle);
                } else {
                    // error opening the file.
                    $return[$i]['resolution']="320x240";
                    $return[$i]['brightness']="50";
                    $return[$i]['contrast']="50";
                    $return[$i]['palette']="MJPEG";
                } 
            }
        }
        return $return;
  }
}

?>
