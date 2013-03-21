<?php
$info=array(
	'name' => 'blog',
	'pagename' => 'blog-options',
	'title' => 'Blog Settings',
	'sublevel' => 'yes'
);



$all_categories=get_categories('hide_empty=0&orderby=name');
$cat_list = array();
$cat_options=array();
$checked_cats=array();
foreach ($all_categories as $category_list ) {
    $cat_list[] = "s_cat_".$category_list->cat_ID;
    $cat_options[] = $category_list->cat_name;
	$checked_cats[]="checked";
}


$options=array(

	
array(
	"type" => "checkbox-nav",
	"name" => "Exclude Categories",
	"id" => $cat_list,
	"options" => $cat_options,	
	"desc" => "Select which categories to display and exclude from the blog page",
	"default" => $checked_cats )
	
);

$optionspage=new s_options_page($info, $options);
?>