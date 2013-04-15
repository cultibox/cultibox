<?php
/**
* @package   yoo_cloud
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

// get template configuration
include($this['path']->path('layouts:template.config.php'));
	
?>
<!DOCTYPE HTML>
<html lang="<?php echo $this['config']->get('language'); ?>" dir="<?php echo $this['config']->get('direction'); ?>">

<head>
<?php echo $this['template']->render('head'); ?>
<?php 
	// Surcharge Calagan
	// L'idée est de remplacer les liens direct de menu Jumi par des liens vers des articles qui contiennent une syntaxe style {jumi [ok.php]}
	require_once 'main/libs/config.php'; 
	require_once 'main/libs/db_common.php'; 
	require_once 'main/libs/utilfunc.php'; 
?>
                <link rel="stylesheet" media="all" type="text/css" href="main/libs/css/jquery-ui-1.8.19.custom.css" />
                <link rel="stylesheet" media="all" type="text/css" href="main/libs/css/cultibox.css" />
                <link rel="stylesheet" media="all" type="text/css" href="main/libs/css/fullcalendar.css" />
                <link rel="stylesheet" media="all" type="text/css" href="main/libs/css/jquery.colourPicker.css" />

                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                <script type="text/javascript" src="main/libs/js/jquery-1.8.3.js"></script>
                <script type="text/javascript" src="main/libs/js/jquery-ui-1.9.2.custom.js"></script>
                <script type="text/javascript" src="main/libs/js/jquery-ui-1.9.2.custom.min.js"></script>
                <!-- Javascript JQUERY libraries for cultibox components: calendar, datepicker, highcharts... -->
                <script type="text/javascript" src="main/libs/js/highcharts.js"></script>
                <script type="text/javascript" src="main/libs/js/exporting.js"></script>
                <script type="text/javascript" src="main/libs/js/jquery-ui-timepicker-addon.js"></script>
                <script type="text/javascript" src="main/libs/js/jquery.colourPicker.js"></script>
	            <script type="text/javascript" src="main/libs/js/cultibox.js"></script>
                <script type="text/javascript" src="main/libs/js/cultibox-utils.js"></script>
                <script type="text/javascript" src="main/libs/js/oXHR.js"></script>
                <script type="text/javascript" src="main/libs/js/fullcalendar.js"></script>
</head>

<body id="page" class="page <?php echo $this['config']->get('body_classes'); ?>" data-config='<?php echo $this['config']->get('body_config','{}'); ?>'>
	<div id="page-bg">
		<div id="page-bg2">
			<?php if ($this['modules']->count('absolute')) : ?>
			<div id="absolute">
				<?php echo $this['modules']->render('absolute'); ?>
			</div>
			<?php endif; ?>
			
<!--			<div id="block-toolbar">
			
				<div class="wrapper">
					
					<div id="toolbar" class="grid-block">
				
						<?php if ($this['modules']->count('toolbar-l') || $this['config']->get('date')) : ?>
						<div class="float-left">
						
							<?php if ($this['config']->get('date')) : ?>
							<time datetime="<?php echo $this['config']->get('datetime'); ?>"><?php echo $this['config']->get('actual_date'); ?></time>
							<?php endif; ?>
						
							<?php echo $this['modules']->render('toolbar-l'); ?>
							
						</div>
						<?php endif; ?>
							
						<?php if ($this['modules']->count('toolbar-r')) : ?>
						<div class="float-right"><?php echo $this['modules']->render('toolbar-r'); ?></div>
						<?php endif; ?>
					
					</div>
					
				</div>
				
			</div> -->
			
			<div class="wrapper grid-block">
		
				<header id="header">
		
					<div id="headerbar" class="grid-block">
									
						<?php if ($this['modules']->count('logo')) : ?>	
						<?php echo $this['modules']->render('logo'); ?>
						<?php endif; ?>
                  
                        <div id="box">                      
                        	<img src="main/libs/img/box.png" alt="">
                        </div>
                        			
                        <a class="logo" href="<?php echo $this['config']->get('site_url'); ?>">                       
                        	<img src="main/libs/img/logo_cultibox.png" alt="">
                        </a>		
                        	
						<?php if($this['modules']->count('headerbar')) : ?>
						<div class="left"><?php echo $this['modules']->render('headerbar'); ?></div>
						<?php endif; ?>    
                        
                        <div id="menubar" class="grid-block">
						                      
							<?php  if ($this['modules']->count('menu')) : ?>
                            <nav id="menu"><?php echo $this['modules']->render('menu'); ?></nav>
                            <?php endif; ?>
<!--            
                            <?php if ($this['modules']->count('search')) : ?>
                            <div id="search"><?php echo $this['modules']->render('search'); ?></div>
                            <?php endif; ?>
 -->                            
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
						<section id="content" class="grid-block"><?php echo $this['template']->render('content'); ?></section>
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
                  <p class="p_right">
                     <!-- Displays version and license for the software at the footer -->
                  <?php 
                        $error="";
                        echo "v".get_configuration("VERSION",$error)."&nbsp;&nbsp;LGPL<br />";
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
