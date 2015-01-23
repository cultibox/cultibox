<?php

if((isset($_GET['pwd']))&&(!empty($_GET['pwd']))) {
   $err=0;
   exec("(echo -n 'cultipi:Identification:' && echo -n 'cultipi:Identification:".$_GET['pwd']."' | md5sum | awk '{print $1}') >> /tmp/.passwd",$output,$err);
   if($err==0) {
       if(is_file("/tmp/.passwd")) {
           exec("sudo mv /tmp/.passwd /etc/lighttpd/",$output,$err);
       } else {
         $err=1;
       }
   } 
}

if($err==0) {
    echo json_encode("1");
} else {
    echo json_encode("0");
}

?>
