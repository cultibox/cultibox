<?php

// On ne met pas en cache les pages chargées 
session_cache_limiter('nocache');
session_start();

//Cookie permettant de stocker le positionnement de la boîte de message, valable pendant 30 jours
if (!isset($_COOKIE['position'])) {
    setcookie("position", "15,15,325", time()+(86400 * 30));
}

//Timezone par défaut pour éviter les problèmes d'ajout d'heures lors des transformations des temps 
date_default_timezone_set('UTC');

require_once('main/libs/db_get_common.php');


//Set menu configuration :
if((isset($_GET['menu']))&&(!empty($_GET['menu']))) {
        $menu=$_GET['menu'];
} else {
        $menu="";
}

//Set lang:
if((isset($_GET['lang']))&&(!empty($_GET['lang']))) {
    $lang=$_GET['lang'];
} else if((isset($_SESSION['LANG']))&&(!empty($_SESSION['LANG']))) {
    $lang=$_SESSION['LANG'];
} else {
    $lang=get_configuration("DEFAULT_LANG",$main_error);
}



require_once('main/libs/config.php');
require_once('main/libs/db_set_common.php');
require_once('main/libs/debug.php');
require_once 'main/libs/utilfunc.php';
require_once('main/libs/utilfunc_sd_card.php');

$_SESSION['LANG'] = $lang; //Language used by the user
$_SESSION['SHORTLANG'] = get_short_lang($_SESSION['LANG']); //Short language used to compute pages
__('LANG');

$wifi=get_configuration("WIFI");
$cost=get_configuration("SHOW_COST");

set_timezone("$lang");
$_SESSION['LANG'] = $lang;
$_SESSION['SHORTLANG'] = get_short_lang($lang);
__('LANG');

session_write_close();
session_start();


 echo "$lang";
    print_r($_SESSION);
    print_r($_COOKIE);
    echo session_id();
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

        //Si le logiciel vient d'être ouvert 10mn après l'installation (ou la mise à jour), on supprime le cache
        if($duration<600) { //10 Minutes après l'installation:
            header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
            header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
            header( 'Cache-Control: no-store, no-cache, must-revalidate' );
            header( 'Cache-Control: post-check=0, pre-check=0', false );
            header( 'Pragma: no-cache' ); 
        }
    }
?>
<?php 
    // Check database consistency
    check_database();
    
?>

    <title>Cultibox</title>

    <link href="/cultibox/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />
    <link rel="stylesheet" href="/cultibox/css/base.css?v=<?=@filemtime('css/base.css')?>" />
    <link rel="stylesheet" href="/cultibox/css/layout.css?v=<?=@filemtime('css/layout.css')?>" />
    <link rel="stylesheet" href="/cultibox/css/menus.css?v=<?=@filemtime('css/menu.css')?>" />

    <link rel="stylesheet" href="/cultibox/css/style.css?v=<?=@filemtime('css/style.css')?>" />
    <link rel="stylesheet" href="/cultibox/css/noise.css?v=<?=@filemtime('css/noise.css')?>" />
    <link rel="stylesheet" href="/cultibox/css/print.css?v=<?=@filemtime('css/print.css')?>" />
    <link rel="stylesheet" href="/cultibox/fonts/opensans.css?v=<?=@filemtime('fonts/opensans.css')?>" />
    <link rel="stylesheet" href="/cultibox/css/mod.css?v=<?=@filemtime('css/mod.css')?>" />

    <link rel="stylesheet" media="all" type="text/css" href="/cultibox/main/libs/css/jquery-ui-1.8.19.custom.css?v=<?=@filemtime('main/libs/css/jquery-ui-1.8.19.custom.css')?>" />
    <link rel="stylesheet" media="all" type="text/css" href="/cultibox/main/libs/css/fullcalendar.css?v=<?=@filemtime('main/libs/css/fullcalendar.css')?>" />
    <link rel="stylesheet" media="all" type="text/css" href="/cultibox/main/libs/css/jquery.colourPicker.css?v=<?=@filemtime('main/libs/css/jquery.colourPicker.css')?>" />
    <link rel="stylesheet" media="all" type="text/css" href="/cultibox/main/libs/css/cultibox.css?v=<?=@filemtime('main/libs/css/cultibox.css')?>" />
    

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <script type="text/javascript" src="/cultibox/main/libs/js/jquery-1.8.3.js?v=<?=@filemtime('main/libs/js/jquery-1.8.3.js')?>"></script>
    <script type="text/javascript" src="/cultibox/main/libs/js/jquery-ui-1.9.2.custom.js?v=<?=@filemtime('main/libs/js/jquery-ui-1.9.2.custom.js')?>"></script>
    <script type="text/javascript" src="/cultibox/main/libs/js/jquery-ui-1.9.2.custom.min.js?v=<?=@filemtime('main/libs/js/jquery-ui-1.9.2.custom.min.js')?>"></script>
    <!-- Javascript JQUERY libraries for cultibox components: calendar, datepicker, highcharts... -->
    <script type="text/javascript" src="/cultibox/main/libs/js/highcharts.js?v=<?=@filemtime('main/libs/js/main/libs/js/highcharts.js')?>"></script>
    <script type="text/javascript" src="/cultibox/main/libs/js/exporting.js?v=<?=@filemtime('main/libs/js/main/libs/js/exporting.js')?>"></script>
    <script type="text/javascript" src="/cultibox/main/libs/js/jquery-ui-timepicker-addon.js?v=<?=@filemtime('main/libs/js/jquery-ui-timepicker-addon.js')?>"></script>
    <script type="text/javascript" src="/cultibox/main/libs/js/jquery.colourPicker.js"></script>
    <script type="text/javascript" src="/cultibox/main/libs/js/cultibox.js?v=<?=@filemtime('main/libs/js/main/libs/js/cultibox.js')?>"></script>
    <script type="text/javascript" src="/cultibox/main/libs/js/cultibox-utils.js?v=<?=@filemtime('main/libs/js/main/libs/js/cultibox-utils.js')?>"></script>
    <script type="text/javascript" src="/cultibox/main/libs/js/fullcalendar.js?v=<?=@filemtime('main/libs/js/main/libs/js/fullcalendar.js')?>"></script>
    <script type="text/javascript" src="/cultibox/main/libs/js/jquery.blockUI.js?v=<?=@filemtime('main/libs/js/main/libs/js/jquery.blockUI.js')?>"></script>
    <script type="text/javascript" src="/cultibox/main/libs/js/scrollTo.js?v=<?=@filemtime('main/libs/js/main/libs/js/scrollTo.js')?>"></script>
    <script type="text/javascript" src="/cultibox/main/libs/js/jquery.ui.datepicker-<?php echo substr($_SESSION['LANG'], 0 , 2); ?>.js"></script>
</head>


<body id="page" class="page sidebar-a-right sidebar-b-right isblog">
    <div id="page-bg">
        <div id="page-bg2">
                        
            <!-- Small eye for displaying message pop up-->
            
            <script>title_msgbox="Cliquez sur l'image pour faire r&eacute;appara&icirc;tre la bo&icirc;te de messages du logiciel";</script>
            <div id="tooltip_msg_box" style="display:none"><img src='/cultibox/main/libs/img/eye.png' alt="" title="" id="eyes_msgbox"></div>
            <div class="wrapper grid-block">
                <header id="header">
                    <div id="headerbar" class="grid-block">
                        <div class="mod-languages">
                            <ul class="lang-inline">
                                <li><a href="/cultibox/index.php?lang=en_GB"><img src="/cultibox/main/libs/img/en.gif" alt="Translate all texts on the site in English (UK)" title="Translate all texts on the site in English (UK)" /></a></li>

                                <li><a href="/cultibox/index.php?lang=fr_FR"><img src="/cultibox/main/libs/img/fr.gif" alt="Traduire tous les textes du site en Français (FR)" title="Traduire tous les textes du site en Français (FR)" /></a></li>

                                <li><a href="/cultibox/index.php?lang=it_IT"><img src="/cultibox/main/libs/img/it.gif" alt="Tradurre tutti i testi del sito in Italiano (IT)" title="Tradurre tutti i testi del sito in Italiano (IT)" /></a> </li>

                               <li><a href="/cultibox/index.php?lang=es_ES"><img src="/cultibox/main/libs/img/es.gif" alt="Traducir los textos del sitio en Español (ES)" title="Traducir los textos del sitio en Español (ES)" /></a></li>

                                <li><a href="/cultibox/index.php?lang=de_DE"><img src="/cultibox/main/libs/img/de.gif" alt="Übersetzen alle Texte auf der Website Deutsch (DE)" title="Übersetzen alle Texte auf der Website Deutsch (DE)" /></a></li>
                            </ul>
                        </div>

                                          
                        <div id="box">                      
                            <img src="/cultibox/main/libs/img/box.png" alt="" height="95" width="105">
                        </div>
                                    
                        <a class="logo" href="/cultibox" id="welcome-logo"><img src="/cultibox/main/libs/img/logo_cultibox.png" alt=""></a>        
                            
                        
                        <!-- Display Menu-->
                        <div id="menubar" class="grid-block">
                            <nav id="menu">
                                <ul class="menu menu-dropdown" id="menubar-ul">
                                    <li id="menu-welcome" class="level1 item155 <?php if(strcmp("$menu","")==0) { echo 'active current'; } ?>"><a href="/cultibox/index.php" class="level1 <?php if(strcmp("$menu","")==0) { echo 'active current'; } ?>" id="href-welcome"><span><?php echo __('MENU_WELCOME'); ?></span></a></li>
                                    <li id="menu-configuration" class="level1 item157 <?php if(strcmp("$menu","conf")==0) { echo 'active current'; } ?>"><a href="/cultibox/index.php?menu=configuration" class="level1 <?php if(strcmp("$menu","conf")==0) { echo 'active current'; } ?>" id="href-configuration"><span><?php echo __('MENU_CONF'); ?></span></a></li>
                                    <li id="menu-logs" class="level1 item158 <?php if(strcmp("$menu","log")==0) { echo 'active current'; } ?>"><a href="/cultibox/index.php?menu=logs" class="level1 <?php if(strcmp("$menu","log")==0) { echo 'active current'; } ?>" id="href-logs"><span><?php echo __('MENU_LOGS'); ?></span></a></li>
                                    <li id="menu-plugs" class="level1 item159 <?php if(strcmp("$menu","plug")==0) { echo 'active current'; } ?>"><a href="/cultibox/index.php?menu=plugis" class="level1 <?php if(strcmp("$menu","plug")==0) { echo 'active current'; } ?>" id="href-plugs"><span><?php echo __('MENU_PLUGS'); ?></span></a></li>
                                    <li id="menu-programss" class="level1 item160 <?php if(strcmp("$menu","prog")==0) { echo 'active current'; } ?>"><a href="/cultibox/index.php?menu=programs" class="level1 <?php if(strcmp("$menu","prog")==0) { echo 'active current'; } ?>" id="href-programs"><span><?php echo __('MENU_PROGS'); ?></span></a></li>
                                    <li id="menu-calendar" class="level1 item162 <?php if(strcmp("$menu","cal")==0) { echo 'active current'; } ?>"><a href="/cultibox/index.php?menu=calendar" class="level1 <?php if(strcmp("$menu","cal")==0) { echo 'active current'; } ?>" id="href-calendar"><span><?php echo __('MENU_CAL'); ?></span></a></li>
                                    <li id="menu-cost" class="level1 item173 <?php if(strcmp("$menu","cost")==0) { echo 'active current'; } ?>" <?php if(!$cost) { echo 'style="display:none"'; } ?>><a href="/cultibox/index.php?menu=wizard" class="level1 <?php if(strcmp("$menu","cost")==0) { echo 'active current'; } ?>" id="href-cost"><span><?php echo __('MENU_COST'); ?></span></a></li>
                                    <li id="menu-wizard" class="level1 item173 <?php if(strcmp("$menu","wiz")==0) { echo 'active current'; } ?>"><a href="/cultibox/index.php?menu=wizard" class="level1 <?php if(strcmp("$menu","wiz")==0) { echo 'active current'; } ?>" id="href-wizard"><span><?php echo __('MENU_WIZARD'); ?></span></a></li>
                                    <li id="menu-wifi" class="level1 item173 <?php if(strcmp("$menu","wifi")==0) { echo 'active current'; } ?>" <?php if(!$wifi) { echo 'style="display:none"'; } ?>><a href="/cultibox/index.php?menu=wifi" class="level1 <?php if(strcmp("$menu","wifi")==0) { echo 'active current'; } ?>" id="href-wifi"><span><?php echo __('MENU_WIFI'); ?></span></a></li>
                                    <li id="menu-help" class="level1 item164 <?php if(strcmp("$menu","help")==0) { echo 'active current'; } ?>"><a href="/cultibox/main/docs/documentation_cultibox.pdf" target="_blank" class="level1 <?php if(strcmp("$menu","help")==0) { echo 'active current'; } ?>" id="href-help"><span><?php echo __('MENU_HELP'); ?></span></a></li>
                                </ul>
                            </nav>

                        </div>               
                        
                    </div>
                </header>
                
                <!-- Pop up -->
                <?php
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
                ?>

                <!-- Message box -->
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
 
                                
                      
                <div id="main" class="grid-block">
                    <div id="maininner" class="grid-box">
                        <div id="content" class="grid-block">
                            <?php
                                switch($menu) {
                                    case 'configuration' : include('main/scripts/configuration.php');
                                                  include('main/libs/js/page_configuration.js');
                                                  include('main/templates/configuration.html');
                                    break;
                                case 'plugs' : include('main/scripts/plugs.php');
                                              include('main/libs/js/page_plugs.js');
                                              include('main/templates/plugs.html');
                                    break;
                                case 'programs' : include('main/scripts/programs.php');
                                              include('main/libs/js/page_programs.js');
                                              include('main/templates/programs.html');
                                    break;
                                case 'logs' : include('main/scripts/logs.php');
                                             include('main/libs/js/page_logs.js');
                                             include('main/templates/logs.html');
                                    break;
                                case 'calendar' : include('main/scripts/calendar.php');
                                             include('main/libs/js/page_calendar.js');
                                             include('main/templates/calendar.html');
                                    break;
                                case 'wizard' : include('main/scripts/wizard.php');
                                             include('main/libs/js/page_wizard.js');
                                             include('main/templates/wizard.html');
                                    break;
                                 case 'wifi' : include('main/scripts/wifi.php');
                                             include('main/libs/js/page_wifi.js');
                                             include('main/templates/wifi.html');
                                    break;
                                case 'cost' : include('main/scripts/cost.php');
                                             include('main/libs/js/page_cost.js');
                                             include('main/templates/cost.html');
                                    break;
                                case 'help' : 
                                    break;
                                default: /*include('main/scripts/welcome.php');
                                         include('main/libs/js/page_welcome.js');
                                         include('main/templates/welcome.html');*/
                                    break;
                                }
                           ?>
                        </div>
                        <div class="shortlogo"><img src="main/libs/img/shortlogo2.png" alt=""></div>
                    </div> 

                    <?php include('main/scripts/post_script.php'); ?>

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
