$(function () {
    var hash = window.location.hash;
    hash && $('#ad a[href="'+hash+'"]').tab('show');

    $('#ad a').click(function (e) {
        $(this).tab('show');
    });
    $('#start_dt').datepicker({format: 'yyyy-mm-dd'});
    $('#end_dt').datepicker({format: 'yyyy-mm-dd'});
    $('#public_dt').datepicker({format: 'yyyy-mm-dd'});
});