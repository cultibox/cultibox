<?php 

    // IN DEV : Add check sd card message
    //echo "<script>pop_up_add_information('" . __('WAIT_UPDATED_PROGRAM') . "<img src=\"main/libs/img/waiting_small.gif\" alt=\"sd_check\" />', \"check_sd_progress\", \"information\");</script>";
    //    include('main/libs/js/check_sd.js'); 
    
    // Check software version if needed
    if(strcmp(get_configuration("CHECK_UPDATE",$main_error),"True")==0)
    {
        $main_info['check_version_progress'] = __('INFO_UPDATE_CHECKING') . "<img src=\"main/libs/img/waiting_small.gif\" alt=\"version_check\" />";
        include('main/libs/js/check_version.js'); 
    }
    
    include('main/libs/js/send_info_error.js');
    

?>