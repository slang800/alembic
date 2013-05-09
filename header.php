<head>
	<title><?php s_titles(); ?></title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	<meta http-equiv="x-ua-compatible" content="ie=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1" />
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

	<?php if(get_option('s_favicon') and get_option('s_favicon')!=""): ?>
	<link rel="shortcut icon" href="<?php echo stripslashes(get_option('s_favicon')); ?>" />	
	<?php endif; ?>	
	<?php wp_head(); ?>

	<?php 
		$rss=get_bloginfo('rss2_url');
		if(get_option('s_rss_url') and get_option('s_rss_url')!="")
			$rss=stripslashes(get_option('s_rss_url'));
	?>
	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php echo $rss; ?>" />
	<link rel="alternate" type="application/atom+xml" title="<?php bloginfo('name'); ?> Atom Feed" href="<?php bloginfo('atom_url')?>" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/assets/css/fonts/fonts.css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/assets/css/isotope.css" />
	<link href="<?php bloginfo('template_directory'); ?>/assets/css/core.css" media="screen" rel="stylesheet" type="text/css" />

	<script type="text/javascript">
		var ajaxurl="<?php echo admin_url('admin-ajax.php'); ?>";		
	</script>
		
	<?php 
		if(get_option('s_css_code')=="true")
			echo '<style type="text/css">' . stripslashes(get_option('s_custom_css')) . '</style>';

		if(get_option('s_child_stylesheet')=="true")
			echo '<link rel="stylesheet" href="' . stripslashes(get_option('s_child_css')) . '" />';

		if(get_option('s_header_js_code')=="true")
			echo stripslashes(get_option('s_header_js'));

		$header_bg = get_option('h_bg');
		if(! $header_bg == ''){
			echo '<style>header{background-image:url('.$header_bg.');background-repeat: repeat;}</style>';
		}
	?>
</head>
