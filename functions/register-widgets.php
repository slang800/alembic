<?php

	if(function_exists('register_sidebar')){
		register_sidebar(array(
						'name'          => 'Footer Column 1',
						'description'   => 'The first column in the footer',
						'before_widget' => '<div class="footer-cols">',
						'after_widget'  => '</div>',
						'before_title'  => '<h2>',
						'after_title'   => '</h2>' )
						);
	}

	if(function_exists('register_sidebar')){
		register_sidebar(array(
						'name'          => 'Footer Column 2',
						'description'   => 'The second column in the footer',
						'before_widget' => '<div class="footer-cols">',
						'after_widget'  => '</div>',
						'before_title'  => '<h2>',
						'after_title'   => '</h2>' )
						);
	}

	if(function_exists('register_sidebar')){
		register_sidebar(array(
						'name'          => 'Footer Column 3',
						'description'   => 'The third column in the footer',
						'before_widget' => '<div class="footer-cols">',
						'after_widget'  => '</div>',
						'before_title'  => '<h2>',
						'after_title'   => '</h2>' )
						);
	}

	if(function_exists('register_sidebar')){
		register_sidebar(array(
						'name'          => 'Footer Column 4',
						'description'   => 'The third column in the footer',
						'before_widget' => '<div class="footer-cols">',
						'after_widget'  => '</div>',
						'before_title'  => '<h2>',
						'after_title'   => '</h2>' )
						);
	}

	if(function_exists('register_sidebar')){
		register_sidebar(array(
						'name'          => 'Sidebar',
						'description'   => 'Sidebar on all pages',
						'before_widget' => '',
						'after_widget'  => '',
						'before_title'  => '<h2>',
						'after_title'   => '</h2>' )
						);
	}
	if(function_exists('register_sidebar')){
		register_sidebar(array(
						'name'          => 'Page Sidebar',
						'description'   => 'Sidebar for all static pages',
						'before_widget' => '',
						'after_widget'  => '',
						'before_title'  => '<h2>',
						'after_title'   => '</h2>' )
						);
	}

	if(function_exists('register_sidebar')){
		register_sidebar(array(
						'name'          => 'Portfolio Sidebar',
						'description'   => 'Portfolio sidebar',
						'before_widget' => '',
						'after_widget'  => '',
						'before_title'  => '<h2>',
						'after_title'   => '</h2>' )
						);
	}
?>
