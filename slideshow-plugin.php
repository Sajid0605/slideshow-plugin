<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * @wordpress-plugin
 * Plugin Name:       Slideshow Plugin
 * Plugin URI:        https://iamsajidansari.com
 * Description:       Plugin for slideshow on frontend
 * Version:           1.0.0
 * Author:            Mohammad Sajid Ansari
 * Author URI:        https://iamsajidansari.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       slideshow-plugin
 * Domain Path:       /languages
 */

if ( ! class_exists( 'SlideShow_Gallery' ) ) {

	class SlideShow_Gallery {
		public function __construct() {
			$this->_constants();
			$this->_hooks();
		}
		
		protected function _constants() {
			//Plugin Version
			define( 'MSA_PLUGIN_VER', '1.0.0' );

			//Plugin Slug
			define( 'MSA_PLUGIN_SLUG', 'slideshow_gallery' );

			//Plugin Directory Path
			define( 'MSA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

			//Plugin Directory URL
			define( 'MSA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			
		} // end of constructor function 

		protected function _hooks() {
			
			//Create Slideshow Gallery Custom Post
			add_action( 'init', array( $this, 'SlideShow_Gallery' ));
			
			//Add meta box to custom post
			add_action( 'add_meta_boxes', array( $this, 'admin_add_meta_box' ) );
			 
			//loaded during admin init 
			add_action( 'admin_init', array( $this, 'admin_add_meta_box' ) );

			add_action('wp_ajax_msa_gallery_js', array(&$this, '_ajax_msa_gallery'));
			
			// add msa cpt shortcode column - manage_{$post_type}_posts_columns
			add_filter( 'manage_slideshow_gallery_posts_columns', array(&$this, 'set_slideshow_gallery_shortcode_column_name') );
			
			// add msa cpt shortcode column data - manage_{$post_type}_posts_custom_column
			add_action( 'manage_slideshow_gallery_posts_custom_column' , array(&$this, 'custom_slideshow_gallery_shodrcode_data'), 10, 2 );
		
			// save custom post
			add_action('save_post', array(&$this, '_msa_save_settings'));

			//Shortcode Compatibility in Text Widgets
			add_filter('widget_text', 'do_shortcode');


			add_filter( 'wp_lazy_loading_enabled', '__return_false' );
		}
			
		public function SlideShow_Gallery() {
			$labels = array(
				'name'                => _x( 'Slide Show Gallery', 'Post Type General Name', 'slideshow-gallery' ),
				'singular_name'       => _x( 'Slide Show Gallery', 'Post Type Singular Name', 'slideshow-gallery' ),
				'menu_name'           => __( 'Slideshow Gallery', 'slideshow-gallery' ),
				'name_admin_bar'      => __( 'Slideshow Gallery', 'slideshow-gallery' ),
				'parent_item_colon'   => __( 'Parent Item:', 'slideshow-gallery' ),
				'all_items'           => __( 'All Gallery', 'slideshow-gallery' ),
				'add_new_item'        => __( 'Add New Gallery', 'slideshow-gallery' ),
				'add_new'             => __( 'Add New Gallery', 'slideshow-gallery' ),
				'new_item'            => __( 'New Slide Show Gallery', 'slideshow-gallery' ),
				'edit_item'           => __( 'Edit Slide Show Gallery', 'slideshow-gallery' ),
				'update_item'         => __( 'Update Slide Show Gallery', 'slideshow-gallery' ),
				'search_items'        => __( 'Search Slide Show Gallery', 'slideshow-gallery' ),
				'not_found'           => __( 'Slide Show Gallery Not found', 'slideshow-gallery' ),
				'not_found_in_trash'  => __( 'Slide Show Gallery Not found in Trash', 'slideshow-gallery' ),
			);
			$args = array(
				'label'               => __( 'Slide Show Gallery', 'slideshow-gallery' ),
				'description'         => __( 'Custom Post Type For Slide Show Gallery', 'slideshow-gallery' ),
				'labels'              => $labels,
				'supports'            => array('title'),
				'taxonomies'          => array(),
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'menu_position'       => 65,
				'menu_icon'           => 'dashicons-cover-image',
				'show_in_admin_bar'   => true,
				'show_in_nav_menus'   => true,
				'can_export'          => true,
				'has_archive'         => true,
				'exclude_from_search' => false,
				'publicly_queryable'  => true,
				'capability_type'     => 'page',
			);
			register_post_type( 'slideshow_gallery', $args );
		} // end of post type function
		
		public function admin_add_meta_box() {
			
			add_meta_box( __('Add Slideshow Gallery', 'slideshow-gallery'), __('Add Slideshow Gallery', 'slideshow-gallery'), array(&$this, 'msa_image_upload'), 'slideshow_gallery', 'normal', 'default' );
			add_meta_box( __('msa-shortcode', 'slideshow-gallery'), __('Copy Shortcode', 'slideshow-gallery'), array(&$this, 'MSA_Shortcode'), 'slideshow_gallery', 'side', 'default' );
		}
			
		public function msa_image_upload($post) {
			wp_enqueue_script('jquery');
			wp_enqueue_script('msa-bootstrap', MSA_PLUGIN_URL . 'admin/js/bootstrap.min.js');
			wp_enqueue_script('media-upload');
			wp_enqueue_script('slideshow-plugin-admin', MSA_PLUGIN_URL . 'admin/js/slideshow-plugin-admin.js', array('jquery'));
			wp_enqueue_style('msa-slideshow-uploader', MSA_PLUGIN_URL . 'admin/css/slideshow-plugin-admin.css');
			wp_enqueue_style('msa-bootstrap', MSA_PLUGIN_URL . 'admin/css/bootstrap.min.css');
			wp_enqueue_media();
			
			require_once('admin/partials/slideshow-plugin-admin-display.php');
		}// end of upload multiple image
		
		public function MSA_Shortcode($post) { ?>
			<div class="pw-shortcode">
				<input type="text" name="shortcode" id="shortcode" value="<?php echo "[MSA_Slideshow id=".$post->ID."]"; ?>" readonly style="height: 60px; text-align: center; font-size: 20px; width: 100%; border: 2px dotted;">
				<p id="pw-copt-code"><?php _e('Shortcode copied to clipboard!', 'slideshow-gallery'); ?></p>
				<p><?php _e('Copy & Embed shortcode into any Page/ Post / Text Widget to display your image gallery on site.', 'slideshow-gallery'); ?><br></p>
			</div>
			<span onclick="copyToClipboard('#shortcode')" class="pw-copy dashicons dashicons-clipboard"></span>
			<style>
			.pw-copy {
				position: absolute;
				top: 9px;
				right: 24px;
				font-size: 26px;
				cursor: pointer;
			}
			</style>
			<script>
			jQuery( "#pw-copt-code" ).hide();
			function copyToClipboard(element) {
				var $temp = jQuery("<input>");
				jQuery("body").append($temp);
				$temp.val(jQuery(element).val()).select();
				document.execCommand("copy");
				$temp.remove();
				jQuery( "#shortcode" ).select();
				jQuery( "#pw-copt-code" ).fadeIn();
			}
			</script>
			<?php
		}// end of gallery generation
		
		// Slideshow gallery cpt shortcode column before date columns
		public function set_slideshow_gallery_shortcode_column_name($defaults) {
			$new = array();
			$shortcode = $columns['_slideshow_gallery_shortcode'];  // save the tags column
			unset($defaults['tags']);	// remove it from the columns list

			foreach($defaults as $key=>$value) {
				if($key=='date') {  // when we find the date column
				   $new['_slideshow_gallery_shortcode'] = __( 'Shortcode', 'slideshow-gallery' );  // put the tags column before it
				}    
				$new[$key] = $value;
			}
			return $new;  
		}
		
		// Slideshow gallery cpt shortcode column data
		public function custom_slideshow_gallery_shodrcode_data( $column, $post_id ) {
			switch ( $column ) {
				case '_slideshow_gallery_shortcode' :
					echo "<input type='text' class='button button-primary' id='slideshow-gallery-shortcode-$post_id' value='[MSA_Slideshow id=$post_id]' style='font-weight:bold; background-color:#32373C; color:#FFFFFF; text-align:center;' />";
					echo "<input type='button' class='button button-primary' onclick='return FilterCopyShortcode$post_id();' readonly value='Copy' style='margin-left:4px;' />";
					echo "<span id='copy-msg-$post_id' class='button button-primary' style='display:none; background-color:#32CD32; color:#FFFFFF; margin-left:4px; border-radius: 4px;'>copied</span>";
					echo "<script>
						function FilterCopyShortcode$post_id() {
							var copyText = document.getElementById('slideshow-gallery-shortcode-$post_id');
							copyText.select();
							document.execCommand('copy');
							
							//fade in and out copied message
							jQuery('#copy-msg-$post_id').fadeIn('1000', 'linear');
							jQuery('#copy-msg-$post_id').fadeOut(2500,'swing');
						}
						</script>
					";
				break;
			}
		}

		public function _msa_ajax_callback_function($image_id) {
			
			if ( current_user_can( 'manage_options' ) ) {
				$thumbnail = wp_get_attachment_image_src($image_id, 'thumbnail', true);
				$attachment = get_post( $image_id ); // $image_id = attachment id
				
				$image_type = "image";				
				?>
				<li class="item image">
					<img class="new-image" src="<?php echo $thumbnail[0]; ?>" alt="<?php echo get_the_title($image_id); ?>" style="height: 150px; width: 98%; border-radius: 8px;">
					<input type="hidden"  name="image-ids[]" value="<?php echo esc_attr($image_id); ?>" />
					<input type="text" name="image-title[]"  style="width: 98%;" placeholder="Image Title" value="<?php echo get_the_title($image_id); ?>">
					<a class="pw-trash-icon" name="remove-image" id="remove-image" href="#"><span class="dashicons dashicons-trash"></span></a>
				</li>
				<?php
				exit;
			}
			
		}
		
		public function _ajax_msa_gallery() {
			if ( current_user_can( 'manage_options' ) ) {
				if (isset( $_POST['msa_add_images_nonce'] ) || wp_verify_nonce( $_POST['msa_add_images_nonce'], 'msa_add_images' ) ) {
					echo $this->_msa_ajax_callback_function(sanitize_text_field($_POST['MSAimageId']));
				} else {
					print 'Sorry, your nonce did not verify.';
					exit;
				}
			}
		}
		
		public function _msa_save_settings($post_id) {
			if ( current_user_can( 'manage_options' ) ) {
				if(isset($_POST['msa_save_nonce'])) {
					if (isset( $_POST['msa_save_nonce'] ) ||  wp_verify_nonce( $_POST['msa_save_nonce'], 'msa_save_settings' ) ) {
						
						$i = 0;
						$image_ids = array();
						$image_titles = array();
						 
						$image_ids_val = isset( $_POST['image-ids'] ) ? (array) $_POST['image-ids'] : array();
						$image_ids_val = array_map( 'sanitize_text_field', $image_ids_val );
						

						foreach($image_ids_val as $image_id) {
							
							$image_ids[]		= sanitize_text_field($_POST['image-ids'][$i]);
							$image_titles[]		= sanitize_text_field($_POST['image-title'][$i]);
							
							if(isset($filters[$image_id])) {
								$filters_new[$image_id] = array_map( 'sanitize_text_field', $filters[$image_id]);
							}
							$single_image_update = array(
								'ID'           => $image_id,
								'post_title'   => $image_titles[$i],
							);
							
							wp_update_post( $single_image_update );
							$i++;
						}
					
						$slideshow_post_setting = array (
							'image-ids'  						=> $image_ids,
							'image_title'  						=> $image_titles,
							
						);		
						$slideshow_shortcode_setting = "slideshow_gallery".$post_id;
						$echo  = update_post_meta($post_id, $slideshow_shortcode_setting, $slideshow_post_setting);
						
					} else {
						print 'Sorry, your nonce did not verify.';
						exit;
						
					}
				}
			}
		}// end save setting
	}
	$slideshow_gallery_object = new SlideShow_Gallery();
	require_once('admin/partials/slideshow-gallery-shortcode.php');
}