<?php
// Do not delete these lines
	if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');
	 $comments_by_type = &separate_comments($comments); 
	 

?>				

				<div id="comments">
				
<?php 
		if ( post_password_required() ) : ?>
					<p class="nopassword"><?php _e( 'This post is password protected. Enter the password to view any comments.', 's' ); ?></p>
				</div><!-- #comments -->
				
<?php
			return;
		endif;
?>

<?php if ( have_comments() ) : ?>

					<div class="count">
						<h2><?php comments_number(__( 'No Comments', 's' ), __( '1 Comment', 's' ), __( '% Comments', 's' ) ); ?></h2>
					</div>
					
					<ul id="comments_list">
					
						<?php wp_list_comments('callback=s_comments'); ?>
						
					</ul>
					
<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
					<div class="navigation">
						<div class="nav-previous"><?php previous_comments_link( __( '<span class="meta-nav">&larr;</span> Older Comments', 's' ) ); ?></div>
						<div class="nav-next"><?php next_comments_link( __( 'Newer Comments <span class="meta-nav">&rarr;</span>', 's' ) ); ?></div>
					</div> <!-- .navigation -->
<?php endif; // check for comment navigation ?>


<?php else : // this is displayed if there are no comments so far ?>
			<?php if ( comments_open() ) : ?>
				<!-- If comments are open, but there are no comments. -->

			 <?php else : // comments are closed ?>
			 
					<p class="nocomments"><span><?php _e( 'Comments are closed', 's' ); ?></p>

			<?php endif; ?>
			
<?php endif; ?>




<?php if ( comments_open() ) : ?>
					<div id="respond">
					
	<?php if ( get_option('comment_registration') && !is_user_logged_in() ) : ?>
			<?php _e( 'Please login to comment.', 's' ); ?>

	<?php else: ?>
						<h2><?php _e( 'Add Comment', 's' ); ?></h2>

						<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
							<fieldset>
							<?php if ( !is_user_logged_in() ) : ?>
								<input name="author" value="<?php _e('Name', 's'); ?>" onfocus="if(this.value==this.defaultValue){this.value=''}" onblur="if(this.value==''){this.value=this.defaultValue}" id="comment_name" />
								<input name="email" value="<?php _e('Email', 's'); ?>" onfocus="if(this.value==this.defaultValue){this.value=''}" onblur="if(this.value==''){this.value=this.defaultValue}" id="comment_email" />
								<input name="url" value="<?php _e('URL', 's'); ?>" onfocus="if(this.value==this.defaultValue){this.value=''}" onblur="if(this.value==''){this.value=this.defaultValue}" id="comment_url" />
							<?php endif; ?>
								
								<textarea name="comment" rows="10" cols="10" id="comment" onfocus="if(this.value==this.defaultValue){this.value=''}" onblur="if(this.value==''){this.value=this.defaultValue}"><?php _e('Comment', 's'); ?></textarea>
								
								<input type="submit" class="button" name="submit" value="Submit" />
								<small><?php cancel_comment_reply_link(); ?></small>
							<?php comment_id_fields(); ?>
							<?php do_action('comment_form', $post->ID); ?>
							</fieldset>

						</form>
					</div>
				
	<?php endif; // If registration required and not logged in ?>

<?php endif; // if you delete this the sky will fall on your head ?>

				</div>
