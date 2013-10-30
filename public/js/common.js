$(function () {

	var successModal = $("#success-modal-block"),
		errorModal = $("#error-modal-block");

	successModal.find(".block-label").html().trim() && successModal.fadeIn( "slow" ).delay( 5000 ).fadeOut( "slow" );
	errorModal.find(".block-label").html().trim() && errorModal.fadeIn( "slow" ).delay( 5000 ).fadeOut( "slow" );
	

});