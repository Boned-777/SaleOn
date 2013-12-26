$(function () {
    // move scripts for action page somewhere

    var fullDescription      = $("#full-description"),
        fullDescriptionModal = $('#full-description-modal'),
        actionAddress        = $('#action-address'),
        actionAddressModal   = $('#action-address-modal'),
        imageWrapper         = $('.img-wrapper');

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


	/*Handle success and error message*/
	var successModal = $("#success-modal-block"),
		errorModal 	 = $("#error-modal-block"),
		successMsg 	 = successModal.find(".block-label").html(),
		errorMsg 	 = errorModal.find(".block-label").html();

	successMsg && successMsg.trim() && successModal.fadeIn( "slow" ).delay( 5000 ).fadeOut( "slow" );
	errorMsg && errorMsg.trim() && errorModal.fadeIn( "slow" ).delay( 5000 ).fadeOut( "slow" );
});
