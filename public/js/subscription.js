/*main menu tooltip*/
$(".btn-large").add("#link").add("#contact-btn").add("#lng-btn").add("#btn-subs-manager").add("#btn-subs-brand").add("#btn-logout.btn-logout").tooltip({placement: "bottom"});

/*Redirect to auth page unregistered/unauthorized users and create "subscription=true" property*/
var subscription = $.cookie("subscription");
if ($("#btn-logout").length<1){
    $('a#btn-subs-brand').attr("href","/user/new").attr("id","");
    $('a.btn-subs-brand').click(function(){
        var subscription = $.cookie("subscription", true );
    });
    var sign_out = $.cookie("sign_out", true);
}else {
    $.removeCookie("sign_out", { path: '/' });
}

/*Show subscription modal window for users who click at "Subscribe to brand" button when they were unregistered/unauthorized */
if ((subscription != null) && !(sign_out)) {
    $.removeCookie("subscription", { path: '/' });
    $('.subscription-form-modal').modal({show: true});
}
/*Show subscription-manager modal window if "subscription_manager" property exist*/
if ($.cookie('subscription_manager') != null) {
    $.removeCookie("subscription_manager", { path: '/' });
    $('.subscription-manager').modal({show: true});
}

/*show subscription modal window*/
$('#btn-subs-brand, #adDetailsSubscribeLink').click(function(e){
    e.preventDefault();

    $('input#subscription-brand_name').val('');
    $('#subscriptionFormMsg').html('');
    $('#subscription-description-label,#subscription-description-element').hide();
    $('textarea#subscription-description').val('');
    $('.subscription-form-modal').modal({show: true});

    if (e.currentTarget.dataset.brand) {
        var brand_id = e.currentTarget.dataset.id;
        var brand_name = e.currentTarget.dataset.brand;
        $('input#subscription-brand_id').val(brand_id);
        $('input#subscription-brand_name').val(brand_name);
    }
});

/*Show subscription-manager modal window*/
$("#btn-subs-manager, .subscription-form-modal #subscriptionManagerBtn").click( function(e) {
    e.preventDefault();
    $('.brand-wrapper').remove();
    $.getJSON('/subscription/list', function(brand) {
        for(var i=0;i<brand.list.length;i++){
            $('.subscription-manager form.brand-list').prepend('<div class="brand-wrapper" title="Відписатись"><input type="checkbox" name="brand['+brand.list[i].id +']" id="brand_'+brand.list[i].id +'" class="check-brand" checked/>'+'<label for="brand_'+brand.list[i].id +'" class="name-brand">'+brand.list[i].name+'</label></div>');
        }
        if (brand.list.length == 0){
            $('.subscription-manager h2:first-of-type').hide();
            $(".save-brands").hide();
            $('.subscription-manager h2:last-of-type').show();
            $(".subscription-manager a").show();

        }else {
            $('.subscription-manager h2:last-of-type').hide();
            $(".subscription-manager a").hide();
            $('.subscription-manager h2:first-of-type').show();
            $(".save-brands").show();
        }
    });
    $('.subscription-manager').modal({show: true});
});

/*Switch between subscription and subscription-manager modal windows*/
$('.subscription-form-modal #subscriptionManagerBtn').click(function(e){
    e.preventDefault();
    $('.subscription-form-modal').modal('hide');
    $('.subscription-manager').modal({show: true});
});

/*Switch between subscription-manager and subscription modal windows*/
$('.subscription-manager a').click(function(e){
    e.preventDefault();
    $('input#subscription-brand_name').val('');
    $('#subscription-description-label,#subscription-description-element').hide();
    $('textarea#subscription-description').val('');
    $('.subscription-manager').modal('hide');
    $('.subscription-form-modal').modal({show: true});
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

/*Catch json success on submit subscription form */
$("form.subscription #subscription_submit").click( function(e) {
    e.preventDefault();
    if ($.trim($('input#subscription-brand_name').val()) != ""){
        var data = $("form.subscription").serialize();
        $.ajax({
            type: "POST",
            url: "/subscription/index",
            dataType: "json",
            data: data,
            success: function(data) {
                $("#subscriptionFormMsg").html(data.msg);
            }
        });
    }
});

$(document).ready(function() {
    $('#subscription-description-label').hide();
    $('#subscription-description-element').hide();

    $("#subscription-brand_name").autocomplete({"source":"\/brands\/autocomp","response":function (e, data) {
        $("#subscription-brand_id").val("");
        if (data.content.length) {
            $("#subscription-description-label").hide();
            $("#subscription-description-element").hide();
        } else {
            $("#subscription-description-label").show();
            $("#subscription-description-element").show();
        }
        return false;
    },"select":function (e, data) {
        $("#subscription-brand_name").val(data.item.label);
        $("#subscription-brand_id").val(data.item.value);
        return false;
    }});
});