<?php

//Highlight
function shortcode_highlight($atts, $content=null){
	extract(
		shortcode_atts(
			array(
				'color' => '#000',
				'textcolor' => '#fff'
			),
			$atts
		)
	);

	$style = ' style="color:' . $textcolor . '; background-color:' . $color . '; padding: 2px 4px;"';
	return '<span' . $style . '">'.do_shortcode($content).'</span>';

}
add_shortcode('hilite', 'shortcode_highlight');

//portfolio
function shortcode_portfilio($atts){
	$return = '<div class="gallery clearfix">';
 
	global $paged;
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1; 

	$query = 'post_type=portfolio';
	if($atts['count']){
		$query .=' &posts_per_page=' . $atts["count"];
	}
	if($atts['orderby']){
		$query .= 'orderby=' . $atts['orderby'];
	}
	query_posts($query);

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

			$id = get_the_id();
			$image_url = s_post_image(); //Use the function to fetch the portfolio image

			$place = esc_html(get_post_meta($id,'_place',true));
			$date = esc_html(get_post_meta($id,'_date',true));
			if($place != "" && $date != ""){
				$caption = '<p>' . $place .','. $date . '</p>';
			} else {
				$caption = '<p>' . $place . $date . '</p>';
			}

			$return .= '<div class="element '. $item_classes . '">';
			if($image_url){
				$image_url = s_build_image($image_url, 180, 220);
				$return .=	'
				<a href="'. get_permalink() .'">
					<img class="image align-left" alt="" src="'. $image_url.'" />
					<h2>'. get_the_title().'</h2>
					' . $caption . '
				</a>';					
			}

			$return .= '</div>';
		}
		$return .= '</div>';
	} else { //If no posts are present 

	}

	wp_reset_query(); 
	return $return;
}

add_shortcode('portfolio', 'shortcode_portfilio');


?>			