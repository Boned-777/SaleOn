$(function () {
    // move scripts for action page somewhere

    var fullDescription      = $("#full-description"),
        fullDescriptionModal = $('#full-description-modal'),
        actionAddress        = $('#action-address'),
        actionAddressModal   = $('#action-address-modal'),
        imageWrapper         = $('.img-wrapper'),
        lngSwitcher          = $('#lng-switcher'),
        lngButton            = $('#lng-btn'),
        currentLng           = $('#current-lng'),
        
        logoLink             = $('#link');

    /* Hide video player*/    
    function hideVideo(modal) {
        var isImage = imageWrapper.find('img');
        if(!isImage.length) {
            imageWrapper.css("visibility", "hidden");
        }
        modal.on('hidden', function () {
            imageWrapper.css("visibility", "visible");
        })    
    }

    /*Show description for full action*/
	fullDescription.click(function(e){
		e.preventDefault();
		$.ajax({
			url: "/ad/getfullinfo?id=" + fullDescription.data("id"),
			cache: false
		}).done(function( html ) {
			fullDescriptionModal.find(".modal-body p").html(html);
			fullDescriptionModal.modal({show: true});
            hideVideo(fullDescriptionModal);
		});
	});

    /* Show map*/
    actionAddress.click(function(e){
        e.preventDefault();
        actionAddressModal.modal({show: true});
        hideVideo(actionAddressModal);

        var address = $("#full-address").val();
        $("#full-address").geocomplete({
            map             : ".map_canvas",
            location        : address,
            markerOptions   : {title: address}
        });
    });

    /* clear all cookies */
    logoLink.click(function(e){
        $.removeCookie("category");
        $.removeCookie("brands");
        $.removeCookie("products");
        $.removeCookie("geo");
    });

    /*Lng switcher*/
    var lng = currentLng.val(),
        lngName = lngSwitcher.find("#"+lng).attr("title");
    lngButton.attr("title", lngName).addClass(lng);
    lngSwitcher.click(function(e){
        e.preventDefault();
        var id = $(e.target).closest(".lng").attr("id");
        $.ajax({
            url: "/user/lang?lang=" + id,
            cache: false
        }).done(function( html ) {
            window.location.reload();
        });
    });

    /* Add browser depended classes */
    if (navigator.sayswho.search("Firefox") != -1) {
        $("body").addClass("firefox-slider");
    }
    if (navigator.sayswho == "MSIE 8.0") {
        $("body").addClass("ie-slider");
    }   


	/*Handle success and error message*/
	var successModal = $("#success-modal-block"),
		errorModal 	 = $("#error-modal-block"),
		successMsg 	 = successModal.find(".block-label").html(),
		errorMsg 	 = errorModal.find(".block-label").html();

	successMsg && successMsg.trim() && successModal.fadeIn( "slow" ).delay( 5000 ).fadeOut( "slow" );
	errorMsg && errorMsg.trim() && errorModal.fadeIn( "slow" ).delay( 5000 ).fadeOut( "slow" );
});
