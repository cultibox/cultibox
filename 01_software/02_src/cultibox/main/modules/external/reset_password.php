<?php

if((isset($_GET['pwd']))&&(!empty($_GET['pwd']))) {
   $pwd=$_GET['pwd'];
   exec("(echo -n 'cultipi:Identification:' && echo -n 'cultipi:Identification:test' | md5sum | awk '{print $1}') >> /tmp/.passwd",$output,$err);
   if(is_file("/tmp/.passwd")) {
       exec("sudo mv /tmp/.passwd /etc/lighttpd/",$output,$err);
   }
}

?>
