<?php 

$output=array();
$upgrade=array();
$err="";

exec("ping -c 4 8.8.8.8 >/dev/null",$output,$err);

if($err==0) {
    exec("sudo apt-get update -o Dir::Etc::sourcelist=\"sources.list.d/cultibox.list\"  -o Dir::Etc::sourceparts=\"-\" -o APT::Get::List-Cleanup=\"0\"",$output,$err);
    if(is_file("/etc/sources.list.d/cultibox-dev.list")) {
            exec("sudo apt-get update -o Dir::Etc::sourcelist=\"sources.list.d/cultibox-dev.list\"  -o Dir::Etc::sourceparts=\"-\" -o APT::Get::List-Cleanup=\"0\"",$output,$err);
    }

    exec("sudo apt-get -u upgrade --assume-no|grep cultibox",$upgrade,$err);
    echo json_encode($upgrade);
} else {
    echo json_encode("1");
}

?>
