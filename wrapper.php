<?php
//this file wraps all the standard stuff around the parts that are unique for
//each page. I'm not particularly fond of opening a bunch of tags in
//header.php and then closing them all in footer.php ... it just doesn't seem
//right to see them all open in a file, so using this wrapper lets all that
//default stuff be dealt with in one file without allowing tags to go
//unclosed.
$unique_content = ob_get_contents();
ob_clean();
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
	<?php get_header(); ?>
	<body <?php body_class(); ?>>
		<div class="wrap">
			<header class="clearfix">
				<div class="grid-container">
					<div class="grid-20 mobile-grid-100">
						<div class="logo">
							<a href="<?php bloginfo('url'); ?>" >
								<img src="<?php echo stripslashes(get_option('s_logo', S_THEME_LOGO)); ?>" alt="<?php bloginfo('title'); ?>"/>
							</a>
						</div>
					</div>
					<div class="grid-55">
						<nav class="main-nav">
							<?php
								//Menu call
								s_menu();
							?>
						</nav>
					</div>
					<div class="grid-25 social">
						<div class="links">
							<ul class="unstyled-h">
								<?php if(get_option('s_add_social1', 'true') == 'true' && get_option('s_social_link1') != ''): ?>
									<li>
										<a href="<?php echo stripslashes(get_option('s_social_link1')); ?>" style="background-image: url(<?php echo stripslashes(get_option('s_social_icon1')); ?>) ;">&nbsp;</a>
									</li>
								<?php endif; ?>
								<?php if(get_option('s_add_social2', 'true')=='true' && get_option('s_social_link2')!=''): ?>
									<li>
										<a href="<?php echo stripslashes(get_option('s_social_link2')); ?>" style="background-image: url(<?php echo stripslashes(get_option('s_social_icon2')); ?>);">&nbsp;</a>
									</li>
								<?php endif; ?>
								<?php if(get_option('s_add_social3', 'true')=='true' && get_option('s_social_link3')!=''): ?>
									<li>
										<a href="<?php echo stripslashes(get_option('s_social_link3')); ?>" style="background-image: url(<?php echo stripslashes(get_option('s_social_icon3')); ?>);">&nbsp;</a>
									</li>
								<?php endif; ?>
							</ul>
						</div><!--//links-->
					</div>
				</div>
			</header>
			<?php echo $unique_content;?>
			<?php get_footer(); ?>
		</div>
		<?php wp_footer(); ?>
		<?php 
			if(get_option('s_footer_js_code')=="true")
				echo stripslashes(get_option('s_footer_js'));
		?>
	</body>
</html>

