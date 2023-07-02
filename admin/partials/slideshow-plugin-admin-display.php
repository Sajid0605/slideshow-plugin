<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
//CSS
wp_enqueue_script('jquery');
wp_enqueue_style('msa-metabox', MSA_PLUGIN_URL . 'admin/css/metabox.css');

?>
<div class="row gallery-content-photo-wall">
    <div class="kwt-file">
		<div class="kwt-file__drop-area">
			<span class="kwt-file__choose-file">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor" width="24px">
					<path d="M67.508 468.467c-58.005-58.013-58.016-151.92 0-209.943l225.011-225.04c44.643-44.645 117.279-44.645 161.92 0 44.743 44.749 44.753 117.186 0 161.944l-189.465 189.49c-31.41 31.413-82.518 31.412-113.926.001-31.479-31.482-31.49-82.453 0-113.944L311.51 110.491c4.687-4.687 12.286-4.687 16.972 0l16.967 16.971c4.685 4.686 4.685 12.283 0 16.969L184.983 304.917c-12.724 12.724-12.73 33.328 0 46.058 12.696 12.697 33.356 12.699 46.054-.001l189.465-189.489c25.987-25.989 25.994-68.06.001-94.056-25.931-25.934-68.119-25.932-94.049 0l-225.01 225.039c-39.249 39.252-39.258 102.795-.001 142.057 39.285 39.29 102.885 39.287 142.162-.028A739446.174 739446.174 0 0 1 439.497 238.49c4.686-4.687 12.282-4.684 16.969.004l16.967 16.971c4.685 4.686 4.689 12.279.004 16.965a755654.128 755654.128 0 0 0-195.881 195.996c-58.034 58.092-152.004 58.093-210.048.041z"></path>
				</svg>
			</span>
			<span class="kwt-file__msg">ADD / UPLOAD ITEMS</span>
			<?php wp_nonce_field( 'msa_add_images', 'msa_add_images_nonce' ); ?>
			<input class="kwt-file__input add-new-images" id="upload_image_button" name="upload_image_button" value="Upload Image" >
			<div class="kwt-file__delete"></div>
		</div>
	</div>
</div>

<div class="row">
	<div class="d-flex align-items-start bhoechie-tab-container">

        <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 bhoechie-tab">
			<!-- flight section -->
			<div class="bhoechie-tab-content active">
				
                <h1><?php _e('Photos', 'slideshow-gallery'); ?></h1>

                <!--Photos from wordpress-->
				<div id="image-gallery">

                    <br>
					<ul id="remove-images" class="sbox">
                    <?php
						$allimagesetting = get_post_meta( $post->ID, 'slideshow_gallery'.$post->ID, true);

						if(isset($allimagesetting['image-ids'])) {
							$count = 0;
							foreach($allimagesetting['image-ids'] as $id) {
								$thumbnail = wp_get_attachment_image_src($id, 'thumbnail', true);
								$title = get_the_title($id);
								?>
								<li class="item image">
									<img class="new-image" src="<?php echo esc_url($thumbnail[0]); ?>" alt="<?php echo esc_attr($title); ?>" style="height: 150px; width: 98%; border-radius: 8px;">
									<input type="hidden"  name="image-ids[]" value="<?php echo esc_attr($id); ?>" />

									<input type="text" name="image-title[]"  style="width: 98%;" placeholder="Image Title" value="<?php echo esc_attr($title); ?>">
                                
									<a class="pw-trash-icon" name="remove-image" id="remove-image" href="#"><span class="dashicons dashicons-trash"></span></a>
								</li>

								<?php $count++; 
							} // end of foreach
						} //end of if
						?>
					</ul>

                </div>
            </div>
        </div>
    </div>
</div>
<?php 
// syntax: wp_nonce_field( 'name_of_my_action', 'name_of_nonce_field' );
wp_nonce_field( 'msa_save_settings', 'msa_save_nonce' );
?>