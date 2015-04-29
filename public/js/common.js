$(function () {
    // move scripts for action page somewhere

    var lngSwitcher          = $('#lng-switcher'),
        lngButton            = $('#lng-btn'),
        currentLng           = $('#current-lng'),
        logoLink             = $('#link'),
        contactBtn           = $('#contact-btn');

    /* clear all cookies */
    logoLink.click(function(e){
        var cookieOptions  = { expires: 7, path: '/' };
        $.removeCookie("geo", cookieOptions);
        $.removeCookie("category", cookieOptions);
        $.removeCookie("brand", cookieOptions);
        $.removeCookie("product", cookieOptions);
        $.removeCookie("sort", this.cookieOptions);
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

    $('.seo-text-show').on("click", function() {
        var text = $('#seo-text');
        if (text.is(":visible")){
            text.hide();
            $("body").animate({ scrollTop: 0 }, 1000);             
        } else {
            text.show();
            $("body").animate({ scrollTop: $(document).height() }, 1000);
        }      
    });


	/*Handle success and error message*/
	var successModal = $("#success-modal-block"),
		errorModal 	 = $("#error-modal-block"),
		successMsg 	 = successModal.find(".block-label").html(),
		errorMsg 	 = errorModal.find(".block-label").html();

	successMsg && successMsg.trim() && successModal.fadeIn( "slow" ).delay( 5000 ).fadeOut( "slow" );
	errorMsg && errorMsg.trim() && errorModal.fadeIn( "slow" ).delay( 5000 ).fadeOut( "slow" );
});
