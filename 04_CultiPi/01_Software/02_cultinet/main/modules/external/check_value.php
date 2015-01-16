<?php

if((isset($_GET['type']))&&(!empty($_GET['type']))) {
    $type=$_GET['type'];
} else {
    return 0;
}

if((isset($_GET['value']))&&(!empty($_GET['value']))) {
    $value=$_GET['value'];
} else {
    return 0;
}

switch($type) {
    case 'ssid': 
                break;
    case 'password':
        if((isset($_GET['value2']))&&(!empty($_GET['value2']))) {
            $value2=$_GET['value2'];
        } else {
            echo "error";
            break;
        }

        if(strcmp(trim($value),trim($value2))!=0) {
            echo "error";
            break;
        }
        break;
    case 'ip':
        // Folowing code doesnot allow folowing adress : 192.001.001.001 ....
        // if(!filter_var($value, FILTER_VALIDATE_IP)) 
        $ipArray = explode("." , $value);
        if (count($ipArray) != 4)
            echo "error"; 
        break;
    case 'password_none':
        break;
    case 'password_wpa':
        if((strlen("$value")>=8)&&(strlen("$value")<=63)) {
            for($i=0;$i<strlen($value);$i++) {
                if((ord($value[$i])<32)||(ord($value[$i])>126)) {
                    echo "error";
                    break;
                }
            }
        } else {
            echo "error";
        }
        break;
    case 'password_wep':
        if((strlen($value)==5)||(strlen($value)==13)||(strlen($value)==29)) {
            for($i=0;$i<strlen($value);$i++) {
                if((ord($value[$i])<32)||(ord($value[$i])>126)) {
                    echo "error";
                    break;
                }
            }
        } else {
            echo "error";
        }
        break;
}
echo "1";

?>
