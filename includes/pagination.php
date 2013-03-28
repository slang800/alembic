			<div class="pagination">
<?php
					include(TEMPLATEPATH . '/php/wp-pagenavi.php');
					if(function_exists('wp_pagenavi')) { wp_pagenavi(); }
?>
			</div>