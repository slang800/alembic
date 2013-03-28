<?php
function s_comments($comment, $args, $depth) {


   $GLOBALS['comment'] = $comment; ?>
   
						<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">

							<div id="comment-<?php comment_ID(); ?>" class="user_info">
								<h3><?php comment_author_link()?></h3> <small><?php _e('Says', 's'); ?></small>
								<?php echo get_avatar($comment,$size='70'); ?>
								<p><?php printf(__('%1$s at %2$s', 's'), get_comment_date('F j, Y'), get_comment_time('g:i a')) ?></p>
								<p><?php comment_reply_link( array_merge( $args, array('reply_text' => __('Reply', 's'), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?></p>
							</div>
							<div class="comment">
							<?php if ($comment->comment_approved == '0') : ?>
								<em>Your comment is awaiting moderation</em>
							<?php endif; ?>
								<?php echo get_comment_text(); ?>
							</div>

<?php
        }
?>
