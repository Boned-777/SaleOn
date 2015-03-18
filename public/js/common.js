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

    /*main menu tooltip*/
    $(".btn-large").add("#link").add("#contact-btn").add("#lng-btn").add("#btn-subs-manager").add("#btn-subs-brand").tooltip({placement: "bottom"});

    /*show subscription modal window*/
    $('#btn-subs-brand').click(function(e){
        e.preventDefault();
        $('.subscription-form-modal').modal({show: true});
    });

    /*Show subscription-manager modal window*/
    $("#btn-subs-manager").click( function(e) {
        e.preventDefault();
        $('.subscription-manager').modal({show: true});
        var brands = $('input.check-brand').length;
        if (brands == 0){
            $('.subscription-manager h2:first-of-type').hide();
            $(".save-brands").hide();
            $('.subscription-manager h2:last-of-type').show();
            $(".subscription-manager a").show();
        }
    });

    /*Switch between subscription and subscription-manager modal windows*/
    $('.subscription-form-modal a').click(function(e){
        e.preventDefault();
        $('.subscription-form-modal').modal('hide');
        $('.subscription-manager').modal({show: true});
    });

    /*Switch between subscription-manager and subscription modal windows*/
    $('.subscription-manager a').click(function(e){
        e.preventDefault();
        $('.subscription-manager').modal('hide');
        $('.subscription-form-modal').modal({show: true});
    });

    /*Get brand list from JSON and show it in subscription-manager modal*/
     $.getJSON('subscription/list', function(brand) {
            for(var i=0;i<brand.list.length;i++){
            $('.subscription-manager form.brand-list').prepend('<input type="checkbox" name="brand['+brand.list[i].id +']" id="brand_'+brand.list[i].id +'" class="check-brand" checked/>'+'<label for="brand_'+brand.list[i].id +'" class="name-brand">'+brand.list[i].name+'</label>');
            }
         if (brand.list.length == 0){
             $('.subscription-manager h2:first-of-type').hide();
             $(".save-brands").hide();
         }else {
             $('.subscription-manager h2:last-of-type').hide();
             $(".subscription-manager a").hide();
         }
     });

    /*Subscription-manager form. JSON success check*/
     $(".save-brands").click( function(e) {
         e.preventDefault();
         var data = $("form.brand-list").serialize();
         $.ajax({
             type: "POST",
             url: "/subscription/manager",
             dataType: "json",
             data: data,
             cache: false,
             success: function(data) {
                 if(data.success == true ){
                     $('.subscription-manager').modal('hide');
                     $('input.check-brand:not(:checked)').next().remove();
                     $('input.check-brand:not(:checked)').remove();
                 }
             }
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
