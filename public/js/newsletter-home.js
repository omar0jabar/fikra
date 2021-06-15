$("#newsletter-insc").change(function() {
    if(this.checked) {
        $("#check-news").val(1);
    } else {
        $("#check-news").val(0);
    }
});
$("#receive-projects").change(function() {
    if(this.checked) {
        $("#check-receive").val(1);
    } else {
        $("#check-receive").val(0);
    }
});
$('#form-newsletter').submit(function( event ){
    event.preventDefault();
    const email = $('#email-newsletter').val();
    if ($('#form-newsletter').valid()) {
        $.ajax({
            method: "GET",
            url: "/register-newsletter",
            data: { email: email, type: null, newsletter_insc: 1, receive_projects: 0, id_list: 10137233, IsExcludedFromCampaigns: true },
            success : function(data) {
                $('#response-newsletter').html(data.message);
                $('#email-newsletter').val(null);
            },
            error : function(results) {
                $('#response-newsletter').html(results.responseJSON.message);
                $('#email-newsletter').val(null);
            }
        });
    }
});

$('#form-newsletter-footer').submit(function( event ){
    event.preventDefault();
    const email = $('#email-newsletter-footer').val();
    const newsletter = $("#check-news").val();
    const receive_projects = $("#check-receive").val();
    if ($('#form-newsletter-footer').valid()) {
        $.ajax({
            method: "GET",
            url: "/register-newsletter",
            data: { email: email, type: null, newsletter_insc: newsletter, receive_projects: receive_projects, id_list: 515, IsExcludedFromCampaigns: true },
            success : function(data) {
                $('#response-newsletter-footer').html(data.message);
                $('#email-newsletter-footer').val(null);
            },
            error : function(results) {
                $('#response-newsletter-footer').html(results.responseJSON.message);
                $('#email-newsletter-footer').val(null);
            }
        });
    }
});