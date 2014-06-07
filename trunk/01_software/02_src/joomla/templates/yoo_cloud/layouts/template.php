<?php

if (!isset($_SESSION)) {
   session_start();
}

if (!isset($_COOKIE['position'])) {
    setcookie("position", "15,15,325", time()+(86400 * 30));
}

// get template configuration
include($this['path']->path('layouts:template.config.php'));
	
?>
<!DOCTYPE HTML>
<html lang="<?php echo $this['config']->get('language'); ?>" dir="<?php echo $this['config']->get('direction'); ?>">

<head>
<?php
    $filename = '../../VERSION.txt';
    if (file_exists($filename)) {
        clearstatcache();
        $time=time();
        $mod_time=filemtime($filename);
        $duration=$time-$mod_time;
        if($duration<600) { //10 Minutes après l'installation:
            header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
            header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
            header( 'Cache-Control: no-store, no-cache, must-revalidate' );
            header( 'Cache-Control: post-check=0, pre-check=0', false );
            header( 'Pragma: no-cache' ); 
        }
    }
?>
<?php echo $this['template']->render('head'); ?>
<?php 
	// Surcharge Calagan
	// L'idée est de remplacer les liens direct de menu Jumi par des liens vers des articles qui contiennent une syntaxe style {jumi [ok.php]}
	require_once 'main/libs/config.php'; 
	require_once 'main/libs/db_set_common.php'; 
    require_once 'main/libs/db_get_common.php';
	require_once 'main/libs/utilfunc.php'; 
    require_once 'main/libs/utilfunc_sd_card.php';
    $_SESSION['LANG'] = get_current_lang(); //Language used by the user
    $_SESSION['SHORTLANG'] = get_short_lang($_SESSION['LANG']); //Short language used to compute pages
    $lang=$_SESSION['LANG'];
    __('LANG');
    
    // Check database consistency
    check_database();
    
?>
    <link rel="stylesheet" media="all" type="text/css" href="main/libs/css/jquery-ui-1.8.19.custom.css?v=<?=@filemtime('main/libs/css/jquery-ui-1.8.19.custom.css')?>" />
    <link rel="stylesheet" media="all" type="text/css" href="main/libs/css/fullcalendar.css?v=<?=@filemtime('main/libs/css/fullcalendar.css')?>" />
    <link rel="stylesheet" media="all" type="text/css" href="main/libs/css/jquery.colourPicker.css?v=<?=@filemtime('main/libs/css/jquery.colourPicker.css')?>" />
    <link rel="stylesheet" media="all" type="text/css" href="main/libs/css/cultibox.css?v=<?=@filemtime('main/libs/css/cultibox.css')?>" />
    

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <script type="text/javascript" src="main/libs/js/jquery-1.8.3.js?v=<?=@filemtime('main/libs/js/jquery-1.8.3.js')?>"></script>
    <script type="text/javascript" src="main/libs/js/jquery-ui-1.9.2.custom.js?v=<?=@filemtime('main/libs/js/jquery-ui-1.9.2.custom.js')?>"></script>
    <script type="text/javascript" src="main/libs/js/jquery-ui-1.9.2.custom.min.js?v=<?=@filemtime('main/libs/js/jquery-ui-1.9.2.custom.min.js')?>"></script>
    <!-- Javascript JQUERY libraries for cultibox components: calendar, datepicker, highcharts... -->
    <script type="text/javascript" src="main/libs/js/highcharts.js?v=<?=@filemtime('main/libs/js/main/libs/js/highcharts.js')?>"></script>
    <script type="text/javascript" src="main/libs/js/exporting.js?v=<?=@filemtime('main/libs/js/main/libs/js/exporting.js')?>"></script>
    <script type="text/javascript" src="main/libs/js/jquery-ui-timepicker-addon.js?v=<?=@filemtime('main/libs/js/jquery-ui-timepicker-addon.js')?>"></script>
    <script type="text/javascript" src="main/libs/js/jquery.colourPicker.js"></script>
    <script type="text/javascript" src="main/libs/js/cultibox.js?v=<?=@filemtime('main/libs/js/main/libs/js/cultibox.js')?>"></script>
    <script type="text/javascript" src="main/libs/js/cultibox-utils.js?v=<?=@filemtime('main/libs/js/main/libs/js/cultibox-utils.js')?>"></script>
    <script type="text/javascript" src="main/libs/js/fullcalendar.js?v=<?=@filemtime('main/libs/js/main/libs/js/fullcalendar.js')?>"></script>
    <script type="text/javascript" src="main/libs/js/jquery.blockUI.js?v=<?=@filemtime('main/libs/js/main/libs/js/jquery.blockUI.js')?>"></script>
    <script type="text/javascript" src="main/libs/js/scrollTo.js?v=<?=@filemtime('main/libs/js/main/libs/js/scrollTo.js')?>"></script>
    <script type="text/javascript" src="main/libs/js/jquery.ui.datepicker-<?php echo substr($_SESSION['LANG'], 0 , 2); ?>.js"></script>
</head>

<body id="page" class="page <?php echo $this['config']->get('body_classes'); ?>" data-config='<?php echo $this['config']->get('body_config','{}'); ?>'>
	<div id="page-bg">
		<div id="page-bg2">
			<?php if ($this['modules']->count('absolute')) : ?>
			<div id="absolute">
				<?php echo $this['modules']->render('absolute'); ?>
			</div>
			<?php endif; ?>
			
            <!-- Small eye for displaying message pop up-->
            <script>title_msgbox=<?php echo json_encode(__('TOOLTIP_MSGBOX_EYES')); ?>;</script>
            <div id="tooltip_msg_box" style="display:none"><img src='/cultibox/main/libs/img/eye.png' alt="" title="" id="eyes_msgbox"></div>
			<div class="wrapper grid-block">
				<header id="header">
					<div id="headerbar" class="grid-block">
									
						<?php if ($this['modules']->count('logo')) : ?>	
						<?php echo $this['modules']->render('logo'); ?>
						<?php endif; ?>
                  
                        <div id="box">                      
                        	<img src="main/libs/img/box.png" alt="" height="95" width="105">
                        </div>
                        			
                        <a class="logo" href="<?php echo $this['config']->get('site_url'); ?>">                       
                        	<img src="main/libs/img/logo_cultibox.png" alt="">
                        </a>		
                        	
						<?php if($this['modules']->count('headerbar')) : ?>
						<div class="left"><?php echo $this['modules']->render('headerbar'); ?></div>
						<?php endif; ?>    
                        
                        <!-- Display Menu-->
                        <div id="menubar" class="grid-block">

							<?php  if ($this['modules']->count('menu')) : ?>
                            <nav id="menu"><?php echo $this['modules']->render('menu'); ?></nav>
                            <?php endif; ?>
   
                        </div>               
						
					</div>

					<div id="diaporama" class="grid-block">
						
						<?php  if ($this['modules']->count('diaporama')) : ?>
						<nav id="menu"><?php echo $this['modules']->render('diaporama'); ?></nav>
						<?php endif; ?>
						
					</div>   
 				
 					<?php if ($this['modules']->count('banner')) : ?>
					<div id="banner"><?php echo $this['modules']->render('banner'); ?></div>
					<?php endif;  ?>
				
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
                        <label class="error_title"><?php echo __('WARNING'); ?>:</label>
                        <div class="error" id="pop_up_error_part">
                            <ul>
                            </ul>
                        </div>
                    </div>
                </div>
                <br />
		
				<?php if ($this['modules']->count('top-a')) : ?>
				<section id="top-a" class="grid-block"><?php echo $this['modules']->render('top-a', array('layout'=>$this['config']->get('top-a'))); ?></section>
				<?php endif; ?>
				
				<?php if ($this['modules']->count('top-b')) : ?>
				<section id="top-b" class="grid-block"><?php echo $this['modules']->render('top-b', array('layout'=>$this['config']->get('top-b'))); ?></section>
				<?php endif; ?>
				
				<?php if ($this['modules']->count('innertop + innerbottom + sidebar-a + sidebar-b') || $this['config']->get('system_output')) : ?>
				<div id="main" class="grid-block">
				
					<div id="maininner" class="grid-box">
					
						<?php if ($this['modules']->count('innertop')) : ?>
						<section id="innertop" class="grid-block"><?php echo $this['modules']->render('innertop', array('layout'=>$this['config']->get('innertop'))); ?></section>
						<?php endif; ?>
		
						<?php if ($this['modules']->count('breadcrumbs')) : ?>
						<section id="breadcrumbs"><?php echo $this['modules']->render('breadcrumbs'); ?></section>
						<?php endif; ?>
		
						<?php if ($this['config']->get('system_output')) : ?>
						<div id="content" class="grid-block"><?php echo $this['template']->render('content'); ?></div>
						<?php endif; ?>
		
						<?php if ($this['modules']->count('innerbottom')) : ?>
						<section id="innerbottom" class="grid-block"><?php echo $this['modules']->render('innerbottom', array('layout'=>$this['config']->get('innerbottom'))); ?></section>
						<?php endif; ?>
		
					</div>
					<!-- maininner end -->
					
					<?php if ($this['modules']->count('sidebar-a')) : ?>
					<aside id="sidebar-a" class="grid-box"><?php echo $this['modules']->render('sidebar-a', array('layout'=>'stack')); ?></aside>
					<?php endif; ?>
					
					<?php if ($this['modules']->count('sidebar-b')) : ?>
					<aside id="sidebar-b" class="grid-box"><?php echo $this['modules']->render('sidebar-b', array('layout'=>'stack')); ?></aside>
					<?php endif; ?>
		
				</div>
				<?php endif; ?>
				<!-- main end -->
		
				<?php if ($this['modules']->count('bottom-a')) : ?>
				<section id="bottom-a" class="grid-block"><?php echo $this['modules']->render('bottom-a', array('layout'=>$this['config']->get('bottom-a'))); ?></section>
				<?php endif; ?>
				
				<?php if ($this['modules']->count('bottom-b')) : ?>
				<section id="bottom-b" class="grid-block"><?php echo $this['modules']->render('bottom-b', array('layout'=>$this['config']->get('bottom-b'))); ?></section>
				<?php endif; ?>
				
				<?php if ($this['modules']->count('footer + debug') || $this['config']->get('warp_branding')) : ?>
                <footer id="footer2" class="grid-block">                
                    <div id="shortlogo">                      
                        <img src="main/libs/img/shortlogo2.png" alt="">
                    </div>           
                </footer>
				<footer id="footer" class="grid-block">
                    <p class="p_center">
                        <!-- Displays version and license for the software at the footer -->
                        <?php 
                            $error="";
                            echo "v".get_configuration("VERSION",$error)."&nbsp;&nbsp;GPL-V3<br />";
                        ?>    
                    </p>
                    <br /><br />	
                    <?php
                        echo $this['modules']->render('footer');
                        echo $this['modules']->render('debug');
                    ?>
				</footer>
				<?php endif; ?>
				
			</div>
				
		</div>

	</div>
	
	<?php echo $this->render('footer'); ?>
	
</body>
</html>
