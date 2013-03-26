<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>

	
	<title><?php s_titles(); ?></title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	<meta http-equiv="x-ua-compatible" content="ie=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1" />
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	
	<?php if(get_option('s_favicon') and get_option('s_favicon')!=""): ?>
	<!-- Favicon -->
	<link rel="shortcut icon" href="<?php echo stripslashes(get_option('s_favicon')); ?>" />	
	<?php endif; ?>	
	
	
	<?php 
		$rss=get_bloginfo('rss2_url');
		if(get_option('s_rss_url') and get_option('s_rss_url')!="")
			$rss=stripslashes(get_option('s_rss_url'));
	?>
	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php echo $rss; ?>" />
	<link rel="alternate" type="application/atom+xml" title="<?php bloginfo('name'); ?> Atom Feed" href="<?php bloginfo('atom_url')?>" />
	
	<link href="<?php bloginfo('template_directory'); ?>/assets/css/core.css" media="screen" rel="stylesheet" type="text/css" />
	<!--[if lte IE 6]>
	<link href="<?php bloginfo('template_directory'); ?>/assets/css/ie6.css" media="screen" rel="stylesheet" type="text/css" />
	<![endif]-->
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/assets/css/superfish.css" media="screen">
	
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/assets/css/fonts/fonts.css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/assets/css/isotope.css" />
<!--[if lt IE 9]>
  <script src="./assets/javascripts/html5.js"></script>
<![endif]-->
<link rel="stylesheet" href="assets/stylesheets/style.css" />
<!--[if (gt IE 8) | (IEMobile)]><!-->
  <link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/assets/css/unsemantic-grid-responsive.css" />
<!--<![endif]-->
<!--[if (lt IE 9) & (!IEMobile)]>
  <link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/assets/css/ie.css" />
<![endif]-->
		
	
	
	<script type="text/javascript">
		var ajaxurl='<?php echo admin_url('admin-ajax.php'); ?>';		
	</script>
	
	<?php 
		if(get_option('s_css_code')=="true")
			echo '<style type="text/css">'
				.stripslashes(get_option('s_custom_css'))
				.'</style>';
	?>	
	
	<?php 
		if(get_option('s_child_stylesheet')=="true")
			echo '<link rel="stylesheet" href="'.stripslashes(get_option('s_child_css')).'" />';
	?>
	
	<?php 
		if(get_option('s_header_js_code')=="true")
			echo stripslashes(get_option('s_header_js'));
	?>
	<?php
	$header_bg = get_option('h_bg');
	if(! $header_bg == ''){
		echo '<style>header{background-image:url('.$header_bg.');background-repeat: repeat;}</style>';
	}
	?>
	<?php wp_head(); ?>

</head>
<body <?php body_class(); ?>>
<div class="wrap">
<header class="clearfix">
	<div class="grid-container">
	  <div class="grid-20 mobile-grid-100">
		<div class="logo"><a href="<?php bloginfo('url'); ?>" ><img src="<?php echo stripslashes(get_option('s_logo', S_THEME_LOGO)); ?>" alt="<?php bloginfo('title'); ?>"></a></div>
	  </div>
	  <div class="grid-55">
		<nav class="main-nav default">
		  <?php
			//Menu call
			s_menu();
		?>
		</nav>
	  </div>
	  <div class="grid-25 social">
				<div class="links">
			<ul class="unstyled-h">
		
		<?php if(get_option('s_add_social1', 'true')=='true' && get_option('s_social_link1')!=''): ?>
			<li><a href="<?php echo stripslashes(get_option('s_social_link1')); ?>" style="background-image: url(<?php echo stripslashes(get_option('s_social_icon1')); ?>) ;">&nbsp;</a></li>
		<?php endif; ?>		
		
		<?php if(get_option('s_add_social2', 'true')=='true' && get_option('s_social_link2')!=''): ?>
			<li><a href="<?php echo stripslashes(get_option('s_social_link2')); ?>" style="background-image: url(<?php echo stripslashes(get_option('s_social_icon2')); ?>);">&nbsp;</a></li>
		<?php endif; ?>	

		<?php if(get_option('s_add_social3', 'true')=='true' && get_option('s_social_link3')!=''): ?>
			<li><a href="<?php echo stripslashes(get_option('s_social_link3')); ?>" style="background-image: url(<?php echo stripslashes(get_option('s_social_icon3')); ?>);">&nbsp;</a></li>
		<?php endif; ?>
	
		</ul>
		</div><!--//links-->
	  </div>
	</div>
</header>	
	
	