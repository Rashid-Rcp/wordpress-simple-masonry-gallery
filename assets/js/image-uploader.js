jQuery(document).ready(function($){


  $(function(){

    var frame;    
    var addImagesSec =  $('#masonry_gallery_add_images');
    var addImages = addImagesSec.find('.add-images');
    var imageContainer = addImagesSec.find('.img-container');   
    var selectedImages = addImagesSec.find('.selected-images');
    
    // ADD IMAGE LINK
    addImages.on( 'click', function( event ){
      
      event.preventDefault();
      var selectedImageCollection = selectedImages.val()?JSON.parse(selectedImages.val()):[];
      // If the media frame already exists, reopen it.
      if ( frame ) {
        frame.open();
        return;
      }
      
      // Create a new media frame
      frame = wp.media({
        title: 'Select or Upload images',
        button: {
          text: 'Add images'
        },
        multiple: true  // Set to true to allow multiple files to be selected
      });
  
      // When an image is selected in the media frame...
      frame.on( 'select', function() {
        
        // Get media attachment details from the frame state
        var attachment = frame.state().get('selection').toJSON();
        var imagesIDs= [];
        attachment.forEach(img=>{
        
          var thumbnail = '<div class="img-thumbnail"><img src="'+img.sizes.thumbnail.url+'"><span class="mg-item-remove" data="'+img.id+'">X</span></div>';
          imageContainer.append(thumbnail);
          imagesIDs.push(img.id)
        });
        selectedImageCollection = [...selectedImageCollection,...imagesIDs];
        selectedImages.val(JSON.stringify(selectedImageCollection));
      });
      // Finally, open the modal on click
      frame.open();
    });

    $("#masonry_gallery_add_images").on('click','.mg-item-remove',function(){
      var id = $(this).attr('data');
      $(this).parent().hide();
      var selectedImageCollection = selectedImages.val()?JSON.parse(selectedImages.val()):[];
      var index = selectedImageCollection.indexOf(Number(id));
      if (index > -1) {
        selectedImageCollection.splice(index, 1);
      }
      selectedImages.val(JSON.stringify(selectedImageCollection));
    });
  
  });

 

});



