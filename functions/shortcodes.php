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

	query_posts('post_type=portfolio&posts_per_page=' . $atts["count"]); 
	if(have_posts()){
		while(have_posts()){
			the_post();
			$id = get_the_id();
			$image_url = s_post_image(); //Use the function to fetch the portfolio image

			$place = esc_html(get_post_meta($id,'_place',true));
			$date = esc_html(get_post_meta($id,'_date',true));
			if($place != "" && $date != ""){
				$caption = '<p>' . $place .','. $date . '</p>';
			} else {
				$caption = '<p>' . $place . $date . '</p>';
			}

			$return .= '<div class="element">';
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