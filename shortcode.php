<?php
/**
 * Slider Responsive Premium Shortcode
 *
 * @access    public
 *
 *
 * @return   String
 */
add_shortcode('MASONRY_GALLERY', 'masonry_gallery_shortcode');


function masonry_gallery_shortcode($post_id){
    ob_start();

    wp_enqueue_script('masonry_pkgd', plugin_dir_url( __FILE__ ) . 'assets/js/masonry.pkgd.min.js', array('jquery'));
    wp_enqueue_script('imagesloaded_pkgd', plugin_dir_url( __FILE__ ) . 'assets/js/imagesloaded.pkgd.min.js', array('jquery'));

    $masonryGalleryImgIds = get_post_meta($post_id['id'],'_masonry_gallery_images',true);
    $desktop = metadata_exists('post',$post_id['id'],'_masonry_gallery_desktop')? get_post_meta($post_id['id'],'_masonry_gallery_desktop',true):4;
	$tab = metadata_exists('post',$post_id['id'],'_masonry_gallery_tab')? get_post_meta($post_id['id'],'_masonry_gallery_tab',true):3;
	$phone = metadata_exists('post',$post_id['id'],'_masonry_gallery_phone')? get_post_meta($post_id['id'],'_masonry_gallery_phone',true):2;
    $lightbox = metadata_exists('post',$post_id['id'],'_masonry_gallery_lightbox')? get_post_meta($post_id['id'],'_masonry_gallery_lightbox',true):true;
    
    if($lightbox){
        wp_enqueue_script('magnific_popup', plugin_dir_url( __FILE__ ) . 'assets/js/magnific.min.js', array('jquery'));
        wp_enqueue_style('magnific_popup', plugin_dir_url( __FILE__ )  . 'assets/css/magnific.min.css');    
    }
		
    $masonryGalleryImgIds = $masonryGalleryImgIds?json_decode($masonryGalleryImgIds):[];
    echo '<div class="grid magnific-holder">';
    echo '<div class="grid-sizer"></div>';
    foreach($masonryGalleryImgIds as $imgIds){
        $img = wp_get_attachment_image_src($imgIds, ' medium', true);
        $imgFull =  wp_get_attachment_image_src($imgIds, ' full', true);
       ?>
            <div class="grid-item">
            <a href="<?php echo $imgFull[0]; ?>">
                <img src="<?php echo $img[0]; ?>">
            </a>
            </div>
       <?php
    }
    echo '</div>';

        ?>
        <style>
            @media(min-width:768px){
                .grid-sizer,.grid-item { width:<?=100/(int)$desktop.'%'?>; }
            }
            @media(max-width:767px){
                .grid-sizer,.grid-item { width:<?=100/(int)$tab.'%'?>; }
            }
            @media(max-width:500px){
                .grid-sizer,.grid-item { width:<?=100/(int)$phone.'%'?>; }
            }
           
        </style>
        <script>
            jQuery(document).ready(function ($) {
                // init Masonry
                var $grid = $('.grid').masonry({
                    itemSelector: '.grid-item',
                    // use element for option
                    columnWidth: '.grid-sizer',
                    percentPosition: true
                });
                // layout Masonry after each image loads
                $grid.imagesLoaded().progress( function() {
                $grid.masonry('layout');
                });
   
            <?php 
            if($lightbox){
                ?>
                $('.magnific-holder').magnificPopup({
                                delegate: 'a', // child items selector, by clicking on it popup will open
                                type: 'image',
                                gallery:{
                                    enabled:true
                                }
                 });
                <?php
            }
            ?>
            });

        </script>
        

    <?php


    return ob_get_clean();
}



