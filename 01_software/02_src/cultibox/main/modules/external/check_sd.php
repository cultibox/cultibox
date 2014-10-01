<?php

if((isset($_GET['path']))&&(!empty($_GET['path']))) {
    $path=$_GET['path'];
} else {
    echo "0";
}

if($f=@fopen("$path/test.txt","w+")) {
   fclose($f);
   if(!@unlink("$path/test.txt")) echo "0";
   echo "1";
} else {
   echo "0";
}

?>
