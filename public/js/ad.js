$(function () {
    var emptyString = "",
        settingsTabLoaded = false,
        datesTabLoaded    = false,
        mainTabLoaded     = false;

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
        //var clearText = $(obj).html().replace(/\s+/g, emptyString);
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

    function initScreen (tabName) {
        (tabName == "#dates")    && initDatapickers();    
        (tabName == "#settings") && initDynamicSelectboxes();
        (tabName == "#main")     && initTextCounter();
    }

    /* tabs on add/edit action screen */
    var hash = window.location.hash;
    hash && $('#ad a[href="'+hash+'"]').tab('show');
    initScreen(hash);
    
    $('#ad a').click(function (e) {
        $(this).tab('show');
        var href = $(this).attr("href");
        initScreen(href);
    });

});



