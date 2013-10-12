<?php

if((isset($_GET['list']))&&(!empty($_GET['list']))) {
    $xml_list=$_GET['list'];
}



if((isset($xml_list))&&(!empty($xml_list))) {
    $list=explode("/",$xml_list);

    $handle=fopen("../../xml/config", 'r+');

    if($handle) {
        ftruncate($handle,0);
        foreach($list as $key) {
            $tmp_arr=explode("*",$key);
            if(strcmp($tmp_arr[1],"false")==0) {
               fputs ($handle, $tmp_arr[0]."\n");
            }
        }
        fclose ($handle);  
    }
}
echo "1";
?>
