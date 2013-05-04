<?php
	$footer_cols = get_option('footer_cols');
?>

<?php
	if(function_exists('dynamic_sidebar') && dynamic_sidebar('Footer Column 1')) : 
	endif; 
?>

<?php
	if(function_exists('dynamic_sidebar') && dynamic_sidebar('Footer Column 2')) : 
	endif; 
?>

<?php
	if(function_exists('dynamic_sidebar') && dynamic_sidebar('Footer Column 3')) : 
	endif; 
?>

<?php
	if($footer_cols=='four'){
		if(function_exists('dynamic_sidebar') && dynamic_sidebar('Footer Column 4')) : 
		endif; 
	}
?>
