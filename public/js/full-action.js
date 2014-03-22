$(function () {
    // move scripts for action page somewhere

    var fullDescription      = $("#full-description"),
        fullDescriptionModal = $('#full-description-modal'),
        actionAddress        = $('#action-address'),
        actionAddressModal   = $('#action-address-modal'),
        imageWrapper         = $('.img-wrapper'),
        favoritesLink        = $('#favorites-link');

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

    function showError() {
        var errorModal = $("#error-modal-block");
            errorModal.find(".block-label").html(window.messages.serverError);
            errorModal.fadeIn( "slow" ).delay( 5000 ).fadeOut( "slow" ); 
    }

    favoritesLink.click(function(e){
        e.preventDefault();
        var target = $(e.target),
            link = target.data("link"),
            status = target.data("status");
        if (link == "/auth") {
            window.location.href = link;
        } else {
            //this.dom.lockLayer.show();
            $.ajax({
                dataType: "json",
                url     : link,
                cache   : false
            }).done(_.bind(function(data) {
                if (data.success) {
                    if (status=="on") {
                        target.data("link", link.replace("add", "remove"));
                        target.data("status", "off");
                        target.attr("title", window.messages.removeFromFavorites);
                        target.toggleClass("favorites-icon-off favorites-icon-on")
                    } else {
                        target.data("link", link.replace("remove", "add"));
                        target.data("status", "on");
                        target.attr("title", window.messages.addToFavorites);
                        target.toggleClass("favorites-icon-on favorites-icon-off")
                    }  
                } else {
                    showError();
                }
                //this.dom.lockLayer.hide();
            }, this)).fail(_.bind(function(data) {
                showError();
                //that.dom.lockLayer.hide();
            }, this));      
        }

                

    });

});
