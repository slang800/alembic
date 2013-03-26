<?php
$info=array(
	'name' => 'general',
	'pagename' => 'general-options',
	'title' => 'General Settings'
);


$options=array(


array(
	"type" => "image",
	"name" => "Logo",
	"id" => "s_logo",
	"desc" => "Paste the URL to your logo, or upload it here.",
	"default" => get_bloginfo('template_directory')."/assets/img/logo.jpg" ),

array(
	"type" => "image",
	"name" => "Header background",
	"id" => "h_bg",
	"desc" => "Paste the URL to here, or upload it here.",
	"default" => ""),
	
array(
	"type" => "image",
	"name" => "Favicon",
	"id" => "s_favicon",
	"desc" => "Paste the URL to your favicon, or upload it here.",
	"default" => "" ),
	
	
array(
	"type" => "checkbox",
	"name" => "Enable timthumb?",
	"id" => array( "s_enable_timthumb"),
	"options" => array( "Enable"),					
	"desc" => "Check this to allow dynamic TimThumb image resizing. Uncheck to do manual crops",
	"default" => array( "checked") ),
	
array(
	"type" => "checkbox",
	"name" => "Show breadcrumbs?",
	"id" => array( "s_show_breadcrumbs"),
	"options" => array( "Show"),					
	"desc" => "Check this to show breadcrumbs (eg About &raquo; Our Company &raquo; Mission)",
	"default" => array( "checked") ),
	
array(
	"type" => "text",
	"name" => "Breadcrumb separator",
	"id" => "s_breadcrumb_separator",
	"desc" => "Type a separator to be used in the breadcrumbs (eg '/' &rarr; Home / Page name / Subpage name)",
	"default" => "/" ),
	

array(
	"type" => "text",
	"name" => "Title separator",
	"id" => "s_separator",
	"desc" => "Type a separator to be used in titles (eg '&raquo;' &rarr; Sitename &raquo; Pagename)",
	"default" => "|" ),
	
array(
	"type" => "textarea",
	"name" => "404 error message",
	"id" => "s_404",
	"desc" => "Enter a message to display on your 404 (page not found) error pages.",
	"default" => "" ),
	array(
	"type" => "image",
	"name" => "Social icon 1 - image",
	"id" => "s_social_icon1",
	"desc" => "Upload the image for social icon 1.",
	"default" => "" ),	
	
array(
	"type" => "text",
	"name" => "Social icon 1 - link",
	"id" => "s_social_link1",
	"desc" => "Enter the link for social icon 1.",
	"default" => "" ),
	
array(
	"type" => "image",
	"name" => "Social icon 2 - image",
	"id" => "s_social_icon2",
	"desc" => "Upload the image for social icon 2.",
	"default" => "" ),	
	
array(
	"type" => "text",
	"name" => "Social icon 2 - link",
	"id" => "s_social_link2",
	"desc" => "Enter the link for social icon 2.",
	"default" => "" ),
	
array(
	"type" => "image",
	"name" => "Social icon 3 - image",
	"id" => "s_social_icon3",
	"desc" => "Upload the image for social icon 3.",
	"default" => "" ),	
	
array(
	"type" => "text",
	"name" => "Social icon 3 - link",
	"id" => "s_social_link3",
	"desc" => "Enter the link for social icon 3.",
	"default" => "" )
	
);

$optionspage=new s_options_page($info, $options);
?>