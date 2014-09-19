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
if((isset($_COOKIE['LANG']))&&(!empty(($_COOKIE['LANG'])))) {
    $lang=$_COOKIE['LANG'];
} else {
    $lang=get_configuration("DEFAULT_LANG",$error);
    setcookie("LANG", "$lang", time()+(86400 * 365),"/",false,false);
    header('Location: /cultibox/');
}
__('LANG');


// Library required:
require_once('main/libs/db_set_common.php');
require_once('main/libs/debug.php');
require_once('main/libs/utilfunc_sd_card.php');


// Variables for pages cost and wifi:
$wifi=get_configuration("WIFI");
$cost=get_configuration("SHOW_COST");

if(!isset($menu)) {
    $menu="";
}

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
<?php 
    // Check database consistency
    check_database();
    
?>

    <title>Cultibox</title>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    <link href="/cultibox/favicon.ico" rel="shortcut icon"/>
    <link rel="stylesheet" href="/cultibox/css/base.css?v=<?=@filemtime('css/base.css')?>" />
    <link rel="stylesheet" href="/cultibox/css/layout.css?v=<?=@filemtime('css/layout.css')?>" />
    <link rel="stylesheet" href="/cultibox/css/menus.css?v=<?=@filemtime('css/menu.css')?>" />
    <link rel="stylesheet" href="/cultibox/fonts/opensans.css?v=<?=@filemtime('fonts/opensans.css')?>" />

    <link rel="stylesheet" media="all" type="text/css" href="/cultibox/main/libs/css/jquery-ui-1.8.19.custom.css?v=<?=@filemtime('main/libs/css/jquery-ui-1.8.19.custom.css')?>" />
    <link rel="stylesheet" media="all" type="text/css" href="/cultibox/main/libs/css/fullcalendar.css?v=<?=@filemtime('main/libs/css/fullcalendar.css')?>" />
    <link rel="stylesheet" media="all" type="text/css" href="/cultibox/main/libs/css/jquery.colourPicker.css?v=<?=@filemtime('main/libs/css/jquery.colourPicker.css')?>" />
    <link rel="stylesheet" media="all" type="text/css" href="/cultibox/main/libs/css/cultibox.css?v=<?=@filemtime('main/libs/css/cultibox.css')?>" />

    <script type="text/javascript" src="/cultibox/main/libs/js/jquery-1.8.3.js?v=<?=@filemtime('main/libs/js/jquery-1.8.3.js')?>"></script>
    <script type="text/javascript" src="/cultibox/main/libs/js/jquery-ui-1.9.2.custom.js?v=<?=@filemtime('main/libs/js/jquery-ui-1.9.2.custom.js')?>"></script>
    <script type="text/javascript" src="/cultibox/main/libs/js/jquery-ui-1.9.2.custom.min.js?v=<?=@filemtime('main/libs/js/jquery-ui-1.9.2.custom.min.js')?>"></script>
    <script type="text/javascript" src="/cultibox/main/libs/js/highcharts.js?v=<?=@filemtime('main/libs/js/main/libs/js/highcharts.js')?>"></script>
    <script type="text/javascript" src="/cultibox/main/libs/js/exporting.js?v=<?=@filemtime('main/libs/js/main/libs/js/exporting.js')?>"></script>
    <script type="text/javascript" src="/cultibox/main/libs/js/jquery-ui-timepicker-addon.js?v=<?=@filemtime('main/libs/js/jquery-ui-timepicker-addon.js')?>"></script>
    <script type="text/javascript" src="/cultibox/main/libs/js/jquery.colourPicker.js"></script>
    <script type="text/javascript" src="/cultibox/main/libs/js/cultibox.js?v=<?=@filemtime('main/libs/js/main/libs/js/cultibox.js')?>"></script>
    <script type="text/javascript" src="/cultibox/main/libs/js/cultibox-utils.js?v=<?=@filemtime('main/libs/js/main/libs/js/cultibox-utils.js')?>"></script>
    <script type="text/javascript" src="/cultibox/main/libs/js/fullcalendar.js?v=<?=@filemtime('main/libs/js/main/libs/js/fullcalendar.js')?>"></script>
    <script type="text/javascript" src="/cultibox/main/libs/js/jquery.blockUI.js?v=<?=@filemtime('main/libs/js/main/libs/js/jquery.blockUI.js')?>"></script>
    <script type="text/javascript" src="/cultibox/main/libs/js/scrollTo.js?v=<?=@filemtime('main/libs/js/main/libs/js/scrollTo.js')?>"></script>
    <script type="text/javascript" src="/cultibox/main/libs/js/fileDownload.js?v=<?=@filemtime('main/libs/js/main/libs/js/fileDownload.js')?>"></script>
    <script type="text/javascript" src="/cultibox/main/libs/js/jquery.ui.datepicker-<?php echo substr($_COOKIE['LANG'], 0 , 2); ?>.js"></script>
</head>


<body id="page" class="page">
    <div id="page-bg">
        <div>
            <!-- Small eye for displaying message pop up-->
            <script>title_msgbox="<?php echo __('TOOLTIP_MSGBOX_EYES'); ?>";</script>
            <div id="tooltip_msg_box" style="display:none"><img src='/cultibox/main/libs/img/eye.png' alt="" title="" id="eyes_msgbox"></div>
            <div class="wrapper grid-block">
                <header id="header">
                    <div id="headerbar" class="grid-block">
                        <div class="mod-languages">
                            <ul class="lang-inline">
                                <li class="translate"><a href="/cultibox/index.php?lang=en_GB" id="en_GB"><img src="/cultibox/main/libs/img/en.gif" alt="Translate all texts on the site in English (UK)" title="Translate all texts on the site in English (UK)" /></a></li>

                                <li class="translate"><a href="/cultibox/index.php?lang=fr_FR" id="fr_FR"><img src="/cultibox/main/libs/img/fr.gif" alt="Traduire tous les textes du site en Français (FR)" title="Traduire tous les textes du site en Français (FR)" /></a></li>

                                <li class="translate"><a href="/cultibox/index.php?lang=it_IT" id="it_IT"><img src="/cultibox/main/libs/img/it.gif" alt="Tradurre tutti i testi del sito in Italiano (IT)" title="Tradurre tutti i testi del sito in Italiano (IT)" /></a> </li>

                               <li class="translate"><a href="/cultibox/index.php?lang=es_ES" id="es_ES"><img src="/cultibox/main/libs/img/es.gif" alt="Traducir los textos del sitio en Español (ES)" title="Traducir los textos del sitio en Español (ES)" /></a></li>

                                <li class="translate"><a href="/cultibox/index.php?lang=de_DE" id="de_DE"><img src="/cultibox/main/libs/img/de.gif" alt="Übersetzen alle Texte auf der Website Deutsch (DE)" title="Übersetzen alle Texte auf der Website Deutsch (DE)" /></a></li>
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
                                    <li id="menu-welcome" class="level1 item155 <?php if(strcmp("$menu","")==0) { echo 'active current'; } ?>"><a href="/cultibox/index.php" class="level1 href-welcome <?php if(strcmp("$menu","")==0) { echo 'active current'; } ?>" ><span class="<?php if(strcmp("$menu","")==0) { echo 'active'; } ?>"><?php echo __('MENU_WELCOME'); ?></span></a></li>

                                    <li id="menu-configuration" class="level1 item157 <?php if(strcmp("$menu","configuration")==0) { echo 'active current'; } ?>"><a href="/cultibox/index.php?menu=configuration" class="level1 href-configuration <?php if(strcmp("$menu","configuration")==0) { echo 'active current'; } ?>"><span class="<?php if(strcmp("$menu","configuration")==0) { echo 'active'; } ?>"><?php echo __('MENU_CONF'); ?></span></a></li>

                                    <li id="menu-logs" class="level1 item158 <?php if(strcmp("$menu","logs")==0) { echo 'active current'; } ?>"><a href="/cultibox/index.php?menu=logs" class="level1 href-logs <?php if(strcmp("$menu","logs")==0) { echo 'active current'; } ?>"><span class="<?php if(strcmp("$menu","logs")==0) { echo 'active'; } ?>"><?php echo __('MENU_LOGS'); ?></span></a></li>

                                    <li id="menu-plugs" class="level1 item159 <?php if(strcmp("$menu","plugs")==0) { echo 'active current'; } ?>"><a href="/cultibox/index.php?menu=plugs" class="level1 href-plugs  <?php if(strcmp("$menu","plugs")==0) { echo 'active current'; } ?>"><span class="<?php if(strcmp("$menu","plugs")==0) { echo 'active'; } ?>"><?php echo __('MENU_PLUGS'); ?></span></a></li>

                                    <li id="menu-programs" class="level1 item160 <?php if(strcmp("$menu","programs")==0) { echo 'active current'; } ?>"><a href="/cultibox/index.php?menu=programs" class="level1 href-programs <?php if(strcmp("$menu","programs")==0) { echo 'active current'; } ?>"><span class="<?php if(strcmp("$menu","programs")==0) { echo 'active'; } ?>"><?php echo __('MENU_PROGS'); ?></span></a></li>

                                    <li id="menu-calendar" class="level1 item162 <?php if(strcmp("$menu","calendar")==0) { echo 'active current'; } ?>"><a href="/cultibox/index.php?menu=calendar" class="level1 href-calendar <?php if(strcmp("$menu","calendar")==0) { echo 'active current'; } ?>"><span class="<?php if(strcmp("$menu","calendar")==0) { echo 'active'; } ?>"><?php echo __('MENU_CAL'); ?></span></a></li>

                                    <li id="menu-cost" class="level1 item173 <?php if(strcmp("$menu","cost")==0) { echo 'active current'; } ?>" <?php if(!$cost) { echo 'style="display:none"'; } ?>><a href="/cultibox/index.php?menu=wizard" class="level1 href-cost <?php if(strcmp("$menu","cost")==0) { echo 'active current'; } ?>"><span class="<?php if(strcmp("$menu","cost")==0) { echo 'active'; } ?>"><?php echo __('MENU_COST'); ?></span></a></li>

                                    <li id="menu-wizard" class="level1 item173 <?php if(strcmp("$menu","wizard")==0) { echo 'active current'; } ?>"><a href="/cultibox/index.php?menu=wizard" class="level1 href-wizard <?php if(strcmp("$menu","wizard")==0) { echo 'active current'; } ?>" ><span class="<?php if(strcmp("$menu","wizard")==0) { echo 'active'; } ?>"><?php echo __('MENU_WIZARD'); ?></span></a></li>

                                    <li id="menu-wifi" class="level1 item173 <?php if(strcmp("$menu","wifi")==0) { echo 'active current'; } ?>" <?php if(!$wifi) { echo 'style="display:none"'; } ?>><a href="/cultibox/index.php?menu=wifi" class="level1 href-wifi <?php if(strcmp("$menu","wifi")==0) { echo 'active current'; } ?>"><span class="<?php if(strcmp("$menu","wifi")==0) { echo 'active'; } ?>"><?php echo __('MENU_WIFI'); ?></span></a></li>

                                    <li id="menu-help" class="level1 item164 <?php if(strcmp("$menu","help")==0) { echo 'active current'; } ?>"><a href="/cultibox/main/docs/documentation_cultibox.pdf" target="_blank" class="level1 href-help <?php if(strcmp("$menu","help")==0) { echo 'active current'; } ?>"><span class="<?php if(strcmp("$menu","help")==0) { echo 'active'; } ?>"><?php echo __('MENU_HELP'); ?></span></a></li>
                                </ul>
                            </nav>

                        </div>               
                        
                    </div>
                </header>
                
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
