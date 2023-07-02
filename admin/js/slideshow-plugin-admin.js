(function ($) {
  "use strict";
  var file_frame,
    msa_image_gallery = {
      ul: "",
      init: function () {
        this.ul = jQuery(".sbox");
        this.ul.sortable({
          placeholder: "",
          revert: true,
        });

        /**
         * Add Slide Callback Funtion
         */
        jQuery(".add-new-images").on("click", function (event) {
          var msa_add_images_nonce = jQuery("#msa_add_images_nonce").val();
          event.preventDefault();
          if (file_frame) {
            file_frame.open();
            return;
          }
          file_frame = wp.media.frames.file_frame = wp.media({
            multiple: true,
          });

          file_frame.on("select", function () {
            var images = file_frame.state().get("selection").toJSON(),
              length = images.length;
            for (var i = 0; i < length; i++) {
              msa_image_gallery.get_thumbnail(
                images[i]["id"],
                "",
                msa_add_images_nonce
              );
            }
          });
          file_frame.open();
        });

        /**
         * Delete Slide Callback Function
         */
        this.ul.on("click", "#remove-image", function () {
          if (confirm("Are sure to delete this images?")) {
            jQuery(this)
              .parent()
              .fadeOut(700, function () {
                jQuery(this).remove();
              });
          }
          return false;
        });

        /**
         * Delete All Slides Callback Function
         */
        jQuery("#remove-all-images").on("click", function () {
          if (confirm("Are sure to delete all images?")) {
            msa_image_gallery.ul.empty();
          }
          return false;
        });
      },
      get_thumbnail: function (id, cb, msa_add_images_nonce) {
        cb = cb || function () {};
        var data = {
          action: "msa_gallery_js",
          MSAimageId: id,
          msa_add_images_nonce: msa_add_images_nonce,
        };
        jQuery.post(ajaxurl, data, function (response) {
          msa_image_gallery.ul.append(response);
          cb();
        });
      },
    };
  msa_image_gallery.init();
})(jQuery);
