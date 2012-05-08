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
         <link rel="stylesheet" media="all" type="text/css" href="main/libs/css/jquery-ui-1.8.19.custom.css" />
                <link rel="stylesheet" media="all" type="text/css" href="main/libs/css/cultibox.css" />
                <link rel="stylesheet" media="all" type="text/css" href="main/libs/css/scw.css" />

                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                <script type="text/javascript" src="main/libs/js/jquery-1.7.2.min.js"></script>
                <script type="text/javascript" src="main/libs/js/jquery-ui-1.8.19.custom.min.js"></script>
                <script type='text/JavaScript' src="main/libs/js/scw.js"></script>
                <script type="text/javascript" src="main/libs/js/highcharts.js"></script>
                <script type="text/javascript" src="main/libs/js/exporting.js"></script>
                <script type="text/javascript" src="main/libs/js/jquery-ui-timepicker-addon.js"></script>
                <script type="text/javascript" src="main/libs/js/jquery-ui-sliderAccess.js"></script>
                <script type="text/javascript" src="main/libs/js/scwLanguages.js"></script>
                <style type="text/css">
                        /* css for timepicker */
                        #ui-datepicker-div, .ui-datepicker{ font-size: 90%; }
                        .ui-timepicker-div .ui-widget-header { margin-bottom: 8px; }
                        .ui-timepicker-div dl { text-align: left; }
                        .ui-timepicker-div dl dt { height: 25px; margin-bottom: -25px; }
                        .ui-timepicker-div dl dd { margin: 0 10px 10px 65px; }
                        .ui-timepicker-div td { font-size: 90%; }
                        .ui-tpicker-grid-label { background: none; border: none; margin: 0; padding: 0; }

                </style>
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
						<a id="logo" href="<?php echo $this['config']->get('site_url'); ?>"><?php echo $this['modules']->render('logo'); ?></a>
						<?php endif; ?>
                        
						<?php if ($this['modules']->count('bus')) : ?>	
						<a id="bus" href="<?php echo $this['config']->get('site_url')."/index.php"; ?>"><?php echo $this['modules']->render('bus'); ?></a>
						<?php endif; ?>
                        						
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
				<footer id="footer" class="grid-block">
		
<!--		
					<?php
						echo $this['modules']->render('footer');
						$this->output('warp_branding');
						echo $this['modules']->render('debug');
					?>
-->
				</footer>
				<?php endif; ?>
				
			</div>
				
		</div>

	</div>
	
	<?php echo $this->render('footer'); ?>
	
</body>
</html>
