<?php /*
   Plugin Name: Masonry Gallery
   Plugin URI: #
   description: Simple Masonry Gallery
   Version: 1.2
   Author: RCP
   Author URI: #
   License: GPL2
   */
if(! class_exists('Simply_Masonry_gallery')){
   class Simply_Masonry_gallery {

      public function __construct()
      {
         $this->masonry_hooks();
      }

      public function masonry_hooks(){

        //Create Image Gallery Custom Post
         add_action( 'init', array( $this, '_New_Masonry_Gallery' ));
         
         //Add meta box to custom post
		add_action( 'add_meta_boxes', array( $this, '_admin_add_meta_box' ) );

		//save meta box data
		add_action('save_post', array($this, '_save_masonry_gallery'));

		// column for custom post type - manage_{$post_type}_posts_columns
		add_filter( 'manage_masonry_gallery_posts_columns', array($this, 'masonry_gallery_columns') );

		// column content for custom post type - manage_{$post_type}_posts_custom_column
		add_action( 'manage_masonry_gallery_posts_custom_column' , array($this, 'masonry_gallery_shortcode_column'), 10, 2 );
			
      }
      /**
		 * Image Gallery Custom Post
		 * Create gallery post type in admin dashboard.
		 * @access    private
		 * @return    void      Return custom post type.
		 */
		public function _New_Masonry_Gallery() {
			$labels = array(
				'name'                => __( 'Masonry Gallery'),
				'singular_name'       => __( 'Masonry Gallery'),
				'menu_name'           => __( 'Masonry Gallery'),
				'name_admin_bar'      => __( 'Masonry Gallery'),
				'parent_item_colon'   => __( 'Parent Item:'),
				'all_items'           => __( 'All Masonry Gallery'),
				'add_new_item'        => __( 'Add New Masonry Gallery'),
				'add_new'             => __( 'Add Masonry Gallery'),
				'new_item'            => __( 'New Masonry Gallery'),
				'edit_item'           => __( 'Edit Masonry Gallery'),
				'update_item'         => __( 'Update Masonry Gallery'),
				'search_items'        => __( 'Search Masonry Gallery' ),
				'not_found'           => __( 'Masonry Gallery Not found' ),
				'not_found_in_trash'  => __( 'Masonry Gallery Not found in Trash' ),
			);
			$args = array(
				'label'               => __( 'Masonry Gallery' ),
				'description'         => __( 'Custom Post Type For Masonry Gallery'),
				'labels'              => $labels,
				'supports'            => array( 'title'),
				'taxonomies'          => array(),
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'menu_position'       => 65,
				'menu_icon'           => 'dashicons-images-alt2',
				'show_in_admin_bar'   => true,
				'show_in_nav_menus'   => true,
				'can_export'          => true,
				'has_archive'         => true,		
				'exclude_from_search' => false,
				'publicly_queryable'  => true,
				'capability_type'     => 'page',
			);
			register_post_type( 'masonry_gallery', $args );
			
      } // end of post type function
      
      /**
		 * Adds Meta Boxes
		 * @access    private
		 * @return    void
		 */
		public function _admin_add_meta_box() {
			// Syntax: add_meta_box( $id, $title, $callback, $screen, $context, $priority, $callback_args );
			add_meta_box( '_masonry_gallery_shortcode_', __('Copy The Shortcode'), array($this, 'masonry_shortcode_metabox'), 'masonry_gallery', 'side', 'default' );
			add_meta_box( '_masonry_gallery_images_', __('Add Images'), array($this, 'masonry_add_images'), 'masonry_gallery', 'normal', 'default' );
			add_meta_box( '_masonry_gallery_settings_', __('Gallery Settings'), array($this, 'masonry_gallery_settings'), 'masonry_gallery', 'normal', 'default' );
      }
      
      	// image gallery copy shortcode meta box under publish button
		public function masonry_shortcode_metabox($post) { 
         echo "<p>[MASONRY_GALLERY id=".$post->ID."]</p>"; 
	  }
	  
	  public function _save_masonry_gallery($post_id){
		if ( array_key_exists( 'selected_images', $_POST ) ) {
			update_post_meta(
				$post_id,
				'_masonry_gallery_images',
				$_POST['selected_images']
			);
			$desktop = $_POST['masonry_gallery_desktop']?:4;
			$tab = $_POST['masonry_gallery_tab']?:3;
			$phone = $_POST['masonry_gallery_phone']?:2;
			$lightbox = isset($_POST['masonry_gallery_lightbox'])?true:false;

			update_post_meta($post_id,'_masonry_gallery_desktop',$desktop);
			update_post_meta($post_id,'_masonry_gallery_tab',$tab);
			update_post_meta($post_id,'_masonry_gallery_phone',$phone);
			update_post_meta($post_id,'_masonry_gallery_lightbox',$lightbox);

		}
	  }

	  public function masonry_gallery_settings($post){
		
		$desktop = metadata_exists('post',$post->ID,'_masonry_gallery_desktop')? get_post_meta($post->ID,'_masonry_gallery_desktop',true):4;
		$tab = metadata_exists('post',$post->ID,'_masonry_gallery_tab')? get_post_meta($post->ID,'_masonry_gallery_tab',true):3;
		$phone = metadata_exists('post',$post->ID,'_masonry_gallery_phone')? get_post_meta($post->ID,'_masonry_gallery_phone',true):2;
		$lightbox = metadata_exists('post',$post->ID,'_masonry_gallery_lightbox')? get_post_meta($post->ID,'_masonry_gallery_lightbox',true):true;
		?>
			<h3>Number of columns</h3>
			<p><label for="masonry_gallery_desktop">Desktop </label> <br>
			<input type="text" id="masonry_gallery_desktop" name="masonry_gallery_desktop" value="<?=$desktop?>"></p>
			<p><label for="masonry_gallery_tab">Tab </label><br>
			 <input type="text" id="masonry_gallery_tab" name="masonry_gallery_tab" value="<?=$tab?>"></p>
			<p><label for="masonry_gallery_phone">Phone </label><br> 
			<input type="text" id="masonry_gallery_phone" name="masonry_gallery_phone" value="<?=$phone?>"></p>
			<h3>Enable lightbox / popup</h3>
			<p><label for="masonry_gallery_lightbox">Enable </label><input id="masonry_gallery_lightbox" type="checkbox" name="masonry_gallery_lightbox"<?=$lightbox?'checked':''?> ></p>
		<?php
	  }
	  
      public function masonry_add_images($post) { 
		
		$imgIds = get_post_meta( $post->ID, '_masonry_gallery_images', true )?:'[]';
		$imgIds_array = json_decode($imgIds);
		
        wp_enqueue_script('media-upload');
        wp_enqueue_script('masonry-gallery-img-uploader', plugin_dir_url( __FILE__ ) . 'assets/js/image-uploader.js', array('jquery'));
        wp_enqueue_style('masonry-gallery-admin-css', plugin_dir_url( __FILE__ )  . 'assets/css/masonry-gallery-admin.css');
         wp_enqueue_media();
         ?>
         <div class="row">
         <!--Add New Image Button-->
            <div id="masonry_gallery_add_images">
               <div class="img-container">
				   <?php
				 foreach($imgIds_array as $id){
					 $img = wp_get_attachment_image_src($id, 'thumbnail', true);
					echo '<div class="img-thumbnail"><img src="'.$img[0].'"><span class="mg-item-remove" data="'.$id.'">X</span></div>';
				 }
				   ?>
			   </div>
			   <input type="button" value="Add Images" class="add-images">
			   <input type="hidden" name="selected_images" value="<?=$imgIds?>" class="selected-images">
            </div>
         </div>
         <div style="clear:left;"></div>
         <?php
         //require_once('include/gallery-settings.php');
   } // end of upload multiple image

   public function masonry_gallery_columns($columns){
	   $columns['short_code']= 'Short Code';
	   return $columns;
   }

   public function masonry_gallery_shortcode_column( $column, $post_id){
	   switch ($column){
		   case  'short_code' :
				echo '<p>[MASONRY_GALLERY id="'.$post_id.'"]</p>';
		   break;

	   }

   }

   }
   $masonry_gallery = new Simply_Masonry_gallery();
   require_once('shortcode.php');
}