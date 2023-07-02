<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$allimages = array(  'p' => $msa_gallery_id, 'post_type' => 'slideshow_gallery', 'orderby' => 'ASC');
$loop = new WP_Query( $allimages );

while ( $loop->have_posts() ) : $loop->the_post();
    if(isset($msa_gallery_settings['image-ids'])) { ?>
    
        <div class="owl-carousel owl-theme">
            <?php $count = 0;
            foreach($msa_gallery_settings['image-ids'] as $id) {
                $thumbnail = wp_get_attachment_image_src($id, true);
                $title = get_the_title($id);
                ?>
                    <img class="owl-lazy" data-src="<?php echo esc_url(current($thumbnail)) ?>" data-src-retina="<?php echo esc_url(current($thumbnail)) ?>" alt="<?php echo $title; ?>">
            <?php $count++; 
            } // end of foreach
        ?> </div>
    <?php } //end of if
    ?>

<?php
endwhile;
wp_reset_query();