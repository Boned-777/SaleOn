$(function () {
    var emptyString = "",
        spaceString = " ",
        mainTabLoaded     = false,
        settingsTabLoaded = false,
        datesTabLoaded    = false,
        contactsTabLoaded = false,
        mediaTabLoaded    = false;
    /* Helper functions */
    function getGeo(term, target, getEmpty) {
        getEmpty ? (getEmpty = 1) : (getEmpty = 0);
        $.ajax({
            url: "/geo/get",
            data: {
                term: term,
                empty: getEmpty
            },
            success: function (res) {
                $("#" + target).html(emptyString);
                if (res.length < 2) {
                    // $("#" + target + "-label").add($("#" + target + "-element")).hide();
                } else{
                    $("#" + target + "-label").add($("#" + target + "-element")).show();
                }
                $.each(res, function (id, data) {
                    $("#" + target).append($('<option value="' + data["value"] + '">' + data["option"] + '</option>'));
                });
                $("#" + target).select2();
            },
            dataType: "json"
        })
    }

    function clearAutocompleter (select, hiddenInput) {
        select.val(emptyString);
        hiddenInput.val("0");    
    }

    function textAreaSymCalculator(obj) {
        // use it to clear line breaks and double spaces
        //var clearText = $(obj).html().replace(/(\r\n|\n|\r)/gm, spaceString).replace(/\s+/g, spaceString);
        //$(obj).html(clearText);
        var counter = $($(obj).parent().find(".symb_counter"));
        if (!counter.html()) {
            counter = $('<div class="symb_counter" style="text-align: right"></div>');
            $(obj).after(counter);
        }
        var textAreaVal = $(obj).val(),
            maxLength = $(obj).attr("max_length");
        (textAreaVal.length > maxLength) ? counter.css("color", "red") : counter.css("color", "grey");
        counter.html(textAreaVal.length + "/" + maxLength);
    }
    /* Helper functions */

    /*Date Tab*/
    function initDatapickers () {
        if(datesTabLoaded) return;
        $('#start_dt').datepicker({format: 'yyyy-mm-dd'});
        $('#end_dt').datepicker({format: 'yyyy-mm-dd'});
        $('#public_dt').datepicker({format: 'yyyy-mm-dd'});   
        datesTabLoaded = true; 
    }
    
    /* Region Tab */
    function initDynamicSelectboxes () {
        if(settingsTabLoaded) return;
        var categorySelect  = $("#category"),
            countrySelect   = $("#country"),
            regionSelect    = $("#region"),
            districtSelect  = $("#district"),
            geoInput        = $("#geo"),
            brandName       = $("#brand_name"),
            productName     = $("#product_name");

        countrySelect.change(function() {
            getGeo(countrySelect.val(), "region");
            geoInput.val(countrySelect.val() + ".0.0");
        });
        regionSelect.change(function() {
            getGeo(regionSelect.val(), "district");
            geoInput.val(regionSelect.val()  + ".0");
        });
        districtSelect.change(function() {
            geoInput.val(districtSelect.val());
        });    
        categorySelect.select2();
        countrySelect.select2();
        regionSelect.select2();
        districtSelect.select2();

        brandName.click(function() {
            clearAutocompleter(brandName, $("#brand"));
        });
        productName.click(function() {
            clearAutocompleter(productName, $("#product"));
        });
        
        settingsTabLoaded = true;
    }

    /* Main text Tab */
    function initTextCounter () {
        if(mainTabLoaded) return;
        
        var description = $("#description"),
            fullDescription = $("#full_description")

        description.keyup(function(e) {
            textAreaSymCalculator(e.target);
        });

        description.on('paste', function(e){
            setTimeout(function(){textAreaSymCalculator(e.target)}, 250);
        });

        fullDescription.keyup(function(e) {
            textAreaSymCalculator(e.target);
        });

        fullDescription.on('paste',function(e){
            setTimeout(function(){textAreaSymCalculator(e.target)}, 250);
        });
        textAreaSymCalculator(description);
        textAreaSymCalculator(fullDescription);

        mainTabLoaded = true;
    }

    /* Media Tab */
    function initPopover () {
        if(mediaTabLoaded) return;

        var smallBannerHelp = '&nbsp;<i class="icon-info-sign" id="small-banner-help" data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus."\
        data-placement="top" data-toggle="popover" data-trigger="hover"></i>',
        videoHelp = '&nbsp;<i class="icon-info-sign" id="video-help" data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus."\
        data-placement="top" data-toggle="popover" data-trigger="hover"></i>';

        $('#banner_file-label').find("label").append(smallBannerHelp);
        $('#video-label').find("label").append(videoHelp);
        $("#small-banner-help").add("#video-help").popover();

        mediaTabLoaded = true; 
    }

    function initInputMask () {
        if(contactsTabLoaded) return;
        $("#phone").add("#phone1").add("#phone2").mask("(999) 999-9999");

        contactsTabLoaded = true;
    }


    function initScreen (tabName) {
        (tabName == "#main")     && initTextCounter();
        (tabName == "#dates")    && initDatapickers();    
        (tabName == "#settings") && initDynamicSelectboxes();
        (tabName == "#contacts") && initInputMask();
        (tabName == "#media")    && initPopover();
    }

    /* tabs on add/edit action screen */
    var hash = window.location.hash;
    hash && $('#ad a[href="'+hash+'"]').tab('show');
    $("#address").geocomplete();
    initScreen(hash);
    
    $('#ad a').click(function (e) {
        $(this).tab('show');
        var href = $(this).attr("href");
        initScreen(href);
    });

});



