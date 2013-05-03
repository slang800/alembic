<?php /* Template Name: Portfolio */ ?>
<?php 
	wp_enqueue_script('isotope', S_THEME_DIR.'/assets/js/jquery.isotope.min.js');
?>
<section id="content" class="grid-container">
	<div class="grid-100">
		<?php s_breadcrumbs(); ?>
		<?php if(!get_the_title()==''){echo '<h2>'.get_the_title().'</h2>';}?>
		<section id="content">
			<?php
				if(have_posts()) : while(have_posts()) : the_post(); 
					the_content();
				endwhile; endif;
			?>
			<div class="portfolio">
				<section id="options" class="clearfix">
					<?php
						$portfolio_category = get_terms('portfolio_cat');
						if($portfolio_category):
					?>
					<ul id="filters" class="option-set unstyled-h" data-option-key="filter">
						<li>
							<a href="#filter" data-option-value="*" class="selected">Show all</a>
						</li>
						<?php foreach($portfolio_category as $portfolio_cat): ?>
							<li>
								<a data-option-value=".<?php echo $portfolio_cat->slug; ?>" href="#">
									<?php echo $portfolio_cat->name; ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
					<?php endif; ?>
				</section>
				<div id="gallery" class="gallery">
					<?php 
					global $paged;
					$paged = (get_query_var('paged')) ? get_query_var('paged') : 1; //For pagination

					query_posts('post_type=portfolio&paged='.$paged);
					//Make sure we let WordPress know we need posts ONLY from the portfolio post type
					if(have_posts()){
						while(have_posts()){
							the_post();

							$item_classes = '';
							$item_cats = get_the_terms($post->ID, 'portfolio_cat');
							if($item_cats){
								foreach($item_cats as $item_cat) {
									$item_classes .= $item_cat->slug . ' ';
								}
							}

							$id = $post->ID;
							$image_url = s_post_image(); //Use the function to fetch the portfolio image

							echo '<div class="element '. $item_classes . '">';

							if($image_url){
								$image_url = s_build_image($image_url, 180, 220);
								echo '
								<a href="'. get_permalink() .'">
									<img class="image align-left" alt="" src="'. $image_url.'" />
									<h2>'. get_the_title().'</h2>
								</a>';					
							}

							$place = esc_html(get_post_meta($id,'_place',true));
							$date = esc_html(get_post_meta($id,'_date',true));
							if($place != "" && $date != ""){
								echo '<p>' . $place .','. $date . '</p>';
							} else {
								echo '<p>' . $place . $date . '</p>';
							}
							echo '</div>';
						}
						echo '</div>';
					}
					?>

			<script>
				jQuery(function() {
					var jQuerycontainer = jQuery('#gallery');
					jQuerycontainer.isotope({
						itemSelector: '.element'
					});
					var jQueryoptionSets = jQuery('#options .option-set'),
						jQueryoptionLinks = jQueryoptionSets.find('a');

					jQueryoptionLinks.click(function() {
						var jQuerythis = jQuery(this);
						// don't proceed if already selected
						if (jQuerythis.hasClass('selected')) {
							return false;
						}
						var jQueryoptionSet = jQuerythis.parents('.option-set');
						jQueryoptionSet.find('.selected').removeClass('selected');
						jQuerythis.addClass('selected');

						// make option object dynamically, i.e. { filter: '.my-filter-class' }
						var options = {},
						key = jQueryoptionSet.attr('data-option-key'),
							value = jQuerythis.attr('data-option-value');
						// parse 'false' as false boolean
						value = value === 'false' ? false : value;
						options[key] = value;
						if (key === 'layoutMode' && typeof changeLayoutMode === 'function') {
							// changes in layout modes need extra logic
							changeLayoutMode(jQuerythis, options)
						} else {
							// otherwise, apply new options
							jQuerycontainer.isotope(options);
						}

						return false;
					});
				});
			</script>
		</section>
	</div>
</section>

<?php include("wrapper.php"); ?>
