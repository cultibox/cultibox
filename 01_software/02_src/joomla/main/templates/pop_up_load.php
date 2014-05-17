<?php 

    // This code is call at the start of the page to create message and pop up

    // Create pop up message if needed
    if (isset($pop_up) && "$pop_up"!="False")
    {
        if(isset($pop_up_message) && !empty($pop_up_message)) 
        {
            // Create a pop up message
            echo '<div class="pop_up_message" style="display:none">';
            echo str_replace("\\n\\n","<br /><br />","$pop_up_message");
            echo '</div>';
        } else if(isset($pop_up_error_message) && !empty($pop_up_error_message) ) {
            // Create a pop up error
            echo '<div class="pop_up_error" style="display:none">';
            echo str_replace("\\n\\n","<br /><br />","$pop_up_error_message");
            echo '</div>';
        }
    }

    // create div
    echo '<div class="message" style="display:none" title="' . __('MESSAGE_BOX') . '">';
    echo '<br />';
    
    // Display informations
    echo '<div id="pop_up_information_container">';
    echo '<img src="main/libs/img/informations.png" alt="" />';
    echo '<label class="info_title">' . __('INFORMATION') . ':</label>';
    echo '<div class="info"  id="pop_up_information_part">';
    echo '<ul>';
    if((isset($main_info))&&(!empty($main_info))&&(count($main_info)>0)) {
        // Add every element given by array main_info
        foreach($main_info as $inf) { 
            echo "<li>".$inf."</li>";            
        }
    }
    echo '</ul><br /></div></div>';
    
    // Display error
    echo '<div id="pop_up_error_container">';
    echo '<img src="main/libs/img/warning.png" alt="" />';
    echo '<label class="error_title">' . __('WARNING') . ':</label>';
    echo '<div class="error" id="pop_up_error_part">';
    echo '<ul>';
    // If there are some errors, display them
    if((isset($main_error))&&(!empty($main_error))&&(count($main_error)>0)) {
        // Add every element given by array main_error
        foreach($main_error as $err) { 
            echo "<li>".$err."</li>";            
        }

    }
    echo '</ul></div></div>';

    // Close global div of the popup
    echo '</div>';
    echo '<br />';

    // Load date picker according language
    echo '<script type="text/javascript" src="main/libs/js/jquery.ui.datepicker-' . substr($_SESSION['LANG'], 0 , 2) . '.js"></script>'; 

    // IN DEV : Add check sd card message
    //echo "<script>pop_up_add_information('" . __('WAIT_UPDATED_PROGRAM') . "<img src=\"main/libs/img/waiting_small.gif\" alt=\"sd_check\" />', \"check_sd_progress\", \"information\");</script>";
    //include('main/libs/js/check_sd.js'); 
    
    // Check software version if needed
    if(strcmp(get_configuration("CHECK_UPDATE",$main_error),"True")==0)
    {
        echo "<script>pop_up_add_information('" . __('INFO_UPDATE_CHECKING') . "<img src=\"main/libs/img/waiting_small.gif\" alt=\"version_check\" />', \"check_version_progress\", \"information\");</script>";
        include('main/libs/js/check_version.js'); 
    }
?>