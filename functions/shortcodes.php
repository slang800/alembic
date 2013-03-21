<?php


//Highlight
function shortcode_highlight($atts, $content=null){

	 extract( shortcode_atts( array(
	  'color' => '#000',
	  'textcolor' => '#484747'
      ), $atts ) );
	
$style = ' style="color:' . $textcolor . '; background-color:' . $color . '; padding: 2px 4px;"';
return '<span' . $style . '">'.do_shortcode($content).'</span>';

}
add_shortcode('hilite', 'shortcode_highlight');




//portfolio
function shortcode_portfilio($atts){
extract( shortcode_atts( array(
		'count' => 'something',
	), $atts ) );

$return = '<div class="gallery clearfix">';
 
			global $paged;
			$paged = (get_query_var('paged')) ? get_query_var('paged') : 1; //For pagination

			query_posts('post_type=portfolio&posts_per_page='.$count); //Make sure we let WordPress know we need posts ONLY from the portfolio post type
			if(have_posts()) : while(have_posts()) : the_post();
			
			$image_url = s_post_image(); //Use the function to fetch the portfolio image
			if($image_url)
				$image_url = s_build_image($image_url, 180, 220);

			$return .= '<div class="element"><div class="data">';
				
			if($image_url): 
			$return .=	'<div class="overlay-wrap"><img class="image align-left" alt="" src="'. $image_url.'" /><span class="overlay"><a class="con" href="'. get_permalink().'">Enter</a></span></div>
					<div>
					<h2><a href="'. get_permalink() .'">'. get_the_title().'</a></h2>';											
					 
			$return .=	'</div>';
				 endif; 
			$return .= '</div></div>';				
			endwhile; 
			$return .= '</div>';
			else: //If no posts are present 
			
			endif; wp_reset_query(); 
			
			return $return;


}

add_shortcode('portfolio', 'shortcode_portfilio');


?>			