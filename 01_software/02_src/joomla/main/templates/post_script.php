<?php 
    // IN DEV : Add check sd card message
    include('main/libs/js/check_sd.js'); 
    
    // Check software version if needed
    if(get_configuration("CHECK_UPDATE",$main_error) == "True")
    {
        $main_info['check_version_progress'] = __('INFO_UPDATE_CHECKING') . "<img src='../../main/libs/img/waiting_small.gif' />";
        include('main/libs/js/check_version.js'); 
    }
    
    include('main/libs/js/send_info_error.js');
    
    // Send information
    if(get_configuration("STATISTICS",$main_error) == "True") {
        include('main/libs/js/send_informations.js'); 
    }
?>
