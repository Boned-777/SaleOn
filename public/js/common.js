$(function () {
	/*Show description for full action*/
	var fullDescription 	 = $("#full-description"),
		fullDescriptionModal = $('#full-description-modal');

	fullDescription.click(function(e){
		e.preventDefault();
		$.ajax({
			url: "/ad/getfullinfo?id=" + fullDescription.data("id"),
			cache: false
		}).done(function( html ) {
			fullDescriptionModal.find(".modal-body p").html(html);
			fullDescriptionModal.modal({show: true});
		});
	});

	/*Handle success and error message*/
	var successModal = $("#success-modal-block"),
		errorModal 	= $("#error-modal-block"),
		successMsg 	= successModal.find(".block-label").html(),
		errorMsg 	= errorModal.find(".block-label").html();

	successMsg && successMsg.trim() && successModal.fadeIn( "slow" ).delay( 5000 ).fadeOut( "slow" );
	errorMsg && errorMsg.trim() && errorModal.fadeIn( "slow" ).delay( 5000 ).fadeOut( "slow" );

    textAreaSymCalculator($("#description"));
    textAreaSymCalculator($("#full_description"));

    $("#description").keyup(function(e) {
        textAreaSymCalculator(e.target);
    });

    $("#description").on('paste',function(e){
        textAreaSymCalculator(e.target);
        setTimeout($(this).paste, 250);
    });

    $("#full_description").keyup(function(e) {
        textAreaSymCalculator(e.target);
    });

    $("#full_description").on('paste',function(e){
        textAreaSymCalculator(e.target);
        setTimeout($(this).paste, 250);
    });
});

function textAreaSymCalculator(obj) {
    var counter = $($(obj).parent()).find(".symb_counter");
    if (!$(counter).html()) {
        var counter = $('<div class="symb_counter" style="text-align: right"></div>');
        $(obj).after(counter);
    }
    if ($(obj).val().length > $(obj).attr("max_length"))
        $(counter).css("color", "red");
    else
        $(counter).css("color", "grey");
    $(counter).html($(obj).val().length + "/" + $(obj).attr("max_length"));
}

function changePassword() {

}