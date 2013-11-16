$(function () {
	/*Show description for full action*/
	var fullDescription 	 = $("#full-description"),
		fullDescriptionModal = $('#full-description-modal'),
        imageWrapper         = $('.img-wrapper');

	fullDescription.click(function(e){
		e.preventDefault();
		$.ajax({
			url: "/ad/getfullinfo?id=" + fullDescription.data("id"),
			cache: false
		}).done(function( html ) {
			fullDescriptionModal.find(".modal-body p").html(html);
			fullDescriptionModal.modal({show: true});
            var isImage = imageWrapper.find('img');
            if(!isImage.length) {
                imageWrapper.css("visibility", "hidden");
            }
            fullDescriptionModal.on('hidden', function () {
                imageWrapper.css("visibility", "visible");
            })
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
