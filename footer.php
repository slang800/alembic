<footer>
	<?php $footer_cols = get_option('footer_cols'); ?>
	<div class="grid-container cols <?php echo $footer_cols ?>">
		<?php
			//Include file for the columns
			include(S_INCLUDES . 'widget-columns.php');
		?>
		<!-- //sub_content-->
		<div class="grid-100">
			<p class="copy">
				<?php
					$default_copyright = '&copy; <a href="' . get_bloginfo('url') . '">' . get_bloginfo('name') . '</a> ' . date('Y');
					echo stripslashes(get_option('s_footer_copyright', $default_copyright)); 
				?>
			</p>
			<p id="back-top">
				<a href="#top">
					<span></span>
				</a>
			</p>
		</div>
	</div>
</footer>
