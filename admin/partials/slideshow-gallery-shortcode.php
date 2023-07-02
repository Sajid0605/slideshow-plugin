<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
add_shortcode('MSA_Slideshow', 'msa_slideshow_gallery_shortcode');

function msa_slideshow_gallery_shortcode($post_id) {
	ob_start();

    $msa_gallery_settings = get_post_meta( $post_id['id'], 'slideshow_gallery'.$post_id['id'], true);
    $msa_gallery_id = $post_id['id'];
		
    //js
	wp_enqueue_script('jquery');    
    wp_enqueue_script( 'owl-carousel', MSA_PLUGIN_URL .'public/js/owl.carousel.min.js', array(), '2.3.4', true );		
    wp_enqueue_script( 'slideshow-public-admin', MSA_PLUGIN_URL .'public/js/slideshow-plugin-public.js', array(), '1.0.0', true );		

    //css
    wp_enqueue_style( 'owl-carousel', MSA_PLUGIN_URL .'/public/css/owl.carousel.min.css', true, '2.3.4');
    wp_enqueue_style( 'owl-carousel-theme', MSA_PLUGIN_URL .'/public/css/owl.theme.default.min.css', true, '2.3.4');
    wp_enqueue_style( 'slideshow-public-admin', MSA_PLUGIN_URL .'/public/css/slideshow-plugin-public.css', true, '2.3.4');
    
	
	// Load Content
	require('gallery-content.php');
		
	return ob_get_clean();
}