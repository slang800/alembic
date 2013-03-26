<?php
$info=array(
	'name' => 'home-page',
	'pagename' => 'home-page-options',
	'title' => 'Home Page Options',
	'sublevel' => 'yes'
);

$options=array(


	

	

array(
	"type" => "text",
	"name" => "Homepage slider - number of items?",
	"id" => "s_slider_items",				
	"desc" => "Choose the number of recent items to display. Default is 5",
	"default" => "5" ),
	
);

$optionspage=new s_options_page($info, $options);
?>