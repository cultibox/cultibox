<?php 
    // IN DEV : Add check sd card message
    include $GLOBALS['BASE_PATH'].'main/libs/js/check_sd.js';
  
    $main_info['check_version_progress'] = __('INFO_UPDATE_CHECKING') . "<img src='main/libs/img/waiting_small.gif' />";
    include $GLOBALS['BASE_PATH'].'main/libs/js/check_version.js';
  
?>
