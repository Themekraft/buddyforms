jQuery(document).ready(function (jQuery) {
    //Remove submission default page notification
    jQuery(document.body).on('click', '#buddyforms_submission_default_page button.notice-dismiss', function () {
        jQuery.ajax({
            type: 'POST',
            dataType: "json",
            url: buddyformsGlobal.admin_url,
            data: {"action": "buddyforms_close_submission_default_page_notification", "nonce": buddyformsGlobal.ajaxnonce},
        })
    });
    jQuery(document.body).on('click', '#btn-compile-custom', function() {
        jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            url: buddyformsGlobal.admin_url,
            data: {
                'action': 'buddyforms_custom_form_template',
                'nonce': buddyformsGlobal.ajaxnonce,
            },
            success: function(data) {
                console.log(data);
            },
            error: function(request, status, error) {
                console.log(data);
            },
        });
    });

    jQuery(".bf-welcome-accordion_tab").click(function(){

        jQuery(".bf-welcome-accordion_tab").not(this).each(function(){

            jQuery(this).parent().removeClass("active");
            jQuery(this).removeClass("active");

        });

        jQuery(this).parent().toggleClass("active");
        jQuery(this).toggleClass("active");
        jQuery(this).animate({ color:'#509699' }, 500);
    });

    var activeTab = jQuery('.bf-welcome-accordion.active');
    var videoHeight = activeTab.width();
    jQuery('#bf-welcome-video-youtube').css('height', videoHeight*0.5);

    var goPro = jQuery('a[href="edit.php?post_type=buddyforms&page=buddyforms_bundle_screen"]');
    goPro.css('color', '#fca300' );
    goPro.parent().insertAfter('#menu-posts-buddyforms > ul > li:last-child');
});


