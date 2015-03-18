<?php

// DOn't put pages in cache:
session_cache_limiter('nocache');
$error=array();


//Cookie permettant de stocker le positionnement de la boîte de message, valable pendant 30 jours
if (!isset($_COOKIE['POSITION'])) {
    setcookie("POSITION", "15,15,325", time()+(86400 * 30),"/",false,false);
}

//Timezone par défaut pour éviter les problèmes d'ajout d'heures lors des transformations des temps 
date_default_timezone_set('UTC');


//Minimal library required:
require_once('main/libs/config.php');
require_once('main/libs/db_get_common.php');
require_once 'main/libs/utilfunc.php';


//Set lang:
if((isset($_COOKIE['LANG']))&&(!empty($_COOKIE['LANG']))) {
    $lang=$_COOKIE['LANG'];
} else {
    $lang=get_configuration("DEFAULT_LANG",$error);
    setcookie("LANG", "$lang", time()+(86400 * 365),"/",false,false);
    header('Location: /cultibox/');
}
__('LANG');


setcookie("CHECK_SD", "False", time()+1800,"/",false,false);


// Library required:
require_once('main/libs/db_set_common.php');
require_once('main/libs/debug.php');
require_once('main/libs/utilfunc_sd_card.php');

// Check database consistency
check_database();


// Variables for page cost :
$cost=get_configuration("SHOW_COST");
$webcam=get_configuration("SHOW_WEBCAM");


?>
<!DOCTYPE HTML>
<head>
<?php
    $filename = '../../VERSION.txt';
    if (file_exists($filename)) {
        clearstatcache();
        $time=time();
        $mod_time=filemtime($filename);
        $duration=$time-$mod_time;

        //If software is opened 10mn after the installation or the upgrade, we delete the cache:
        if($duration<600) { 
            header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
            header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
            header( 'Cache-Control: no-store, no-cache, must-revalidate' );
            header( 'Cache-Control: post-check=0, pre-check=0', false );
            header( 'Pragma: no-cache' ); 
        }
    }
?>

    <title>Cultibox</title>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    <link href="/cultibox/favicon.ico" rel="shortcut icon"/>
    <link rel="stylesheet" href="/cultibox/css/base.css?v=<?=@filemtime('css/base.css')?>" />
    <link rel="stylesheet" href="/cultibox/css/layout.css?v=<?=@filemtime('css/layout.css')?>" />
    <link rel="stylesheet" href="/cultibox/fonts/opensans.css?v=<?=@filemtime('fonts/opensans.css')?>" />

    <link rel="stylesheet" media="all" type="text/css" href="/cultibox/main/libs/css/jquery-ui-1.8.19.custom.css?v=<?=@filemtime('main/libs/css/jquery-ui-1.8.19.custom.css')?>" />
    <link rel="stylesheet" media="all" type="text/css" href="/cultibox/main/libs/css/fullcalendar.css?v=<?=@filemtime('main/libs/css/fullcalendar.css')?>" />
    <link rel="stylesheet" media="all" type="text/css" href="/cultibox/main/libs/css/jquery.colourPicker.css?v=<?=@filemtime('main/libs/css/jquery.colourPicker.css')?>" />
    <link rel="stylesheet" media="all" type="text/css" href="/cultibox/main/libs/css/cultibox.css?v=<?=@filemtime('main/libs/css/cultibox.css')?>" />

    <script type="text/javascript" src="/cultibox/main/libs/js/jquery-1.8.3.js?v=<?=@filemtime('main/libs/js/jquery-1.8.3.js')?>"></script>
    <script type="text/javascript" src="/cultibox/main/libs/js/jquery-ui-1.9.2.custom.js?v=<?=@filemtime('main/libs/js/jquery-ui-1.9.2.custom.js')?>"></script>
    <script type="text/javascript" src="/cultibox/main/libs/js/jquery-ui-1.9.2.custom.min.js?v=<?=@filemtime('main/libs/js/jquery-ui-1.9.2.custom.min.js')?>"></script>
    <script type="text/javascript" src="/cultibox/main/libs/js/highcharts.js?v=<?=@filemtime('main/libs/js/highcharts.js')?>"></script>
    <script type="text/javascript" src="/cultibox/main/libs/js/exporting.js?v=<?=@filemtime('main/libs/js/exporting.js')?>"></script>
    <script type="text/javascript" src="/cultibox/main/libs/js/jquery-ui-timepicker-addon.js?v=<?=@filemtime('main/libs/js/jquery-ui-timepicker-addon.js')?>"></script>
    <script type="text/javascript" src="/cultibox/main/libs/js/jquery.colourPicker.js?v=<?=@filemtime('main/libs/js/jquery.colourPicker.js')?>"></script>
    <script type="text/javascript" src="/cultibox/main/libs/js/cultibox.js?v=<?=@filemtime('main/libs/js/cultibox.js')?>"></script>
    <script type="text/javascript" src="/cultibox/main/libs/js/cultibox-utils.js?v=<?=@filemtime('main/libs/js/cultibox-utils.js')?>"></script>
    <script type="text/javascript" src="/cultibox/main/libs/js/fullcalendar.js?v=<?=@filemtime('main/libs/js/fullcalendar.js')?>"></script>
    <script type="text/javascript" src="/cultibox/main/libs/js/jquery.blockUI.js?v=<?=@filemtime('main/libs/js/jquery.blockUI.js')?>"></script>
    <script type="text/javascript" src="/cultibox/main/libs/js/scrollTo.js?v=<?=@filemtime('main/libs/js/scrollTo.js')?>"></script>
    <script type="text/javascript" src="/cultibox/main/libs/js/fileDownload.js?v=<?=@filemtime('main/libs/js/fileDownload.js')?>"></script>
    <script type="text/javascript" src="/cultibox/main/libs/js/jquery.ui.datepicker-<?php echo substr($_COOKIE['LANG'], 0 , 2); ?>.js?v=<?=@filemtime('main/libs/js/jquery.ui.datepicker-'.substr($_COOKIE['LANG'], 0 , 2).'.js')?>"></script>
    <script type="text/javascript" src="/cultibox/main/libs/js/fileUpload.js?v=<?=@filemtime('main/libs/js/fileUpload.js')?>"></script>
</head>


<body id="page" class="page">
    <div id="diff_conf" style="display:none">
    <?php echo __('DIR_CONF_NOT_UPTODATE_POPUP'); ?>
    <p class="disable_popup">
        <?php echo __('DISABLE_POPUP'); ?>
        <input type="checkbox" id="disable_popup" name="disable_popup" />
    </p>
    </div>
    <div id="sync_conf" style="display:none">
        <?php echo __('SYNC_CONF'); ?>
    </div>
    <div id="scrollto"></div>
    <div id="page-bg">
        <div>
            <!-- Small eye for displaying message pop up-->
            <script>title_msgbox="<?php echo __('TOOLTIP_MSGBOX_EYES'); ?>";</script>

            <div id="tooltip_msg_box" style="display:none" class="eyes_msgbox"><img src='/cultibox/main/libs/img/eye.png' alt="" title="" id="eyes_msgbox"></div>

            <div class="wrapper grid-block">
                <header id="header">
                    <div id="headerbar" class="grid-block">
                        <div class="mod-languages">
                            <ul class="lang-inline">
                                <li class="translate"><a href="/cultibox/index.php?lang=en_GB" id="en_GB"><img src="/cultibox/main/libs/img/en.gif" alt="Translate the software in English (UK)" title="Translate the software in English (UK)" /></a></li>

                                <li class="translate"><a href="/cultibox/index.php?lang=fr_FR" id="fr_FR"><img src="/cultibox/main/libs/img/fr.gif" alt="Traduire le logiciel en Français (FR)" title="Traduire le logiciel en Français (FR)" /></a></li>

                                <li class="translate"><a href="/cultibox/index.php?lang=it_IT" id="it_IT"><img src="/cultibox/main/libs/img/it.gif" alt="Tradurre il software in Italiano (IT)" title="Tradurre il software in Italiano (IT)" /></a> </li>

                                <li class="translate"><a href="/cultibox/index.php?lang=es_ES" id="es_ES"><img src="/cultibox/main/libs/img/es.gif" alt="Traducir el software en Español (ES)" title="Traducir el sotware en Español (ES)" /></a></li>

                                <li class="translate"><a href="/cultibox/index.php?lang=de_DE" id="de_DE"><img src="/cultibox/main/libs/img/de.gif" alt="Übersetzen Sie die Software Deutsch (DE)" title="Übersetzen Sie die Software Deutsch (DE)" /></a></li>
                                
                                <li ><a <?php if((isset($GLOBALS['MODE']))&&(strcmp($GLOBALS['MODE'],"cultipi")==0)) { ?>href="/documentation_cultibox.pdf"<?php } else { ?>href="/cultibox/main/docs/documentation_cultibox.pdf" <?php } ?> target="_blank"><img src="/cultibox/main/libs/img/help.png" alt="<?php echo __('MENU_HELP'); ?>" title="<?php echo __('MENU_HELP'); ?>" /></a></li>
                            
                            </ul>
                        </div>

                                          
                        <div id="box">                      
                            <img src="/cultibox/main/libs/img/box.png" alt="" height="95" width="105">
                        </div>
                                    
                        <a class="logo" href="/cultibox" id="welcome-logo"><img src="/cultibox/main/libs/img/logo_cultibox.png" alt=""></a>    
                </header>
                
                        
                <!-- Display Menu-->
                <div id="menubar">
                    <ul id="menubar-ul">
                            <li id="menu-configuration"><a href="/cultibox/index.php?menu=configuration" class="level1 href-configuration"><span><?php echo __('MENU_CONF'); ?></span></a></li>

                            <li id="menu-logs"><a href="/cultibox/index.php?menu=logs" class="level1 href-logs"><span><?php echo __('MENU_LOGS'); ?></span></a></li>

                            <li id="menu-plugs"><a href="/cultibox/index.php?menu=plugs" class="level1 href-plugs"><span><?php echo __('MENU_PLUGS'); ?></span></a></li>

                            <li id="menu-programs"><a href="/cultibox/index.php?menu=programs" class="level1 href-programs"><span><?php echo __('MENU_PROGS'); ?></span></a></li>

                            <li id="menu-calendar"><a href="/cultibox/index.php?menu=calendar" class="level1 href-calendar"><span><?php echo __('MENU_CAL'); ?></span></a></li>

                            <li id="menu-cost" <?php if(!$cost) { echo 'style="display:none"'; } ?>><a href="/cultibox/index.php?menu=wizard" class="level1 href-cost"><span><?php echo __('MENU_COST'); ?></span></a></li>

                            <li id="menu-wizard" class="level1 item173"><a href="/cultibox/index.php?menu=wizard" class="level1 href-wizard" ><span><?php echo __('MENU_WIZARD'); ?></span></a></li>

                            <?php if((isset($GLOBALS['MODE']))&&(strcmp($GLOBALS['MODE'],"cultipi")==0)) { ?>
                                    <li id="menu-cultipi" class="level1 item164"><a href="/cultibox/index.php?menu=cultipi" class="level1 href-cultipi" ><span><?php echo __('MENU_CULTIPI'); ?></span></a></li>
                            <?php } ?>

                            <li id="menu-webcam" <?php if(!$webcam) { echo 'style="display:none"'; } ?>><a href="/cultibox/index.php?menu=webcam" class="level1 href-webcam" ><span><?php echo __('MENU_WEBCAM'); ?></span></a></li>
                   </ul>
            </div>               

                <div class="message" style="display:none" title="<?php echo __('MESSAGE_BOX'); ?>">
                    <br />
                    <div id="pop_up_information_container">
                        <img src="main/libs/img/informations.png" alt="" />
                        <label class="info_title"><?php echo __('INFORMATION'); ?>:</label>
                        <div class="info"  id="pop_up_information_part">
                            <ul>
                            </ul>
                            <br />
                        </div>
                    </div>
                    <div id="pop_up_error_container">
                        <img src="main/libs/img/warning.png" alt="" />
                        <label class="error_title"><?php  echo __('WARNING'); ?>:</label>
                        <div class="error" id="pop_up_error_part">
                            <ul>
                            </ul>
                        </div>
                    </div>
                </div>
                <br />

                <!-- To check that javascript is enable: -->
                <noscript>
                <div id="compat-js" class="text_info">
                <p><?php echo __('ENABLE_JAVASCRIPT'); ?></p>
                </div>
                </noscript>
                
                <!--  Main content part: -->
                <div id="main" class="grid-block">
                    <div id="maininner" class="grid-box">
                        <div id="content" class="grid-block">
                        </div>
                        <div class="shortlogo"><img src="main/libs/img/shortlogo2.png" alt=""></div>
                    </div> 
                </div>
            </div>
            
            <footer id="footer" class="grid-block">
                    <p class="p_center">
                        <!-- Displays version and license for the software at the footer -->
                        <?php
                            $error="";
                            echo "v".get_configuration("VERSION",$error)."&nbsp;&nbsp;GPL-V3<br />";
                        ?>
                    </p>
            </footer>
        </div>
    </div>
</body>
</html>
