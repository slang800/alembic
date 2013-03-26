<?php
$info=array(
	'name' => 'footer',
	'pagename' => 'footer-options',
	'title' => 'Footer Options',
	'sublevel' => 'yes'
);


$options=array(


array(
	"type" => "text",
	"name" => "Footer copyright text",
	"id" => "s_footer_copyright",
	"desc" => "Enter the text to be used in the footer copyright region",
	"default" => "Your copyright - footer info here" ),
	
array(
	"type" => "radio",
	"values" => array("three","four"),
	"name" => "Choose number of column to display in footer",
	"id" => "footer_cols",
	"options" => array( "Three column", "Four column"),					
	"desc" => "Choose footer column",
	"default" => array("Checked","")
	)
	
);

$optionspage=new s_options_page($info, $options);
?>