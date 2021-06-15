$('#ModalNewsletter').on('hidden.bs.modal', function () {
    //window.location.href = $('#linkDownload').val();
});
$("#newsletter").change(function() {
    if(this.checked) {
        $("#check-news").val(1);
    } else {
        $("#check-news").val(0);
    }
});
function getLinkDownload(a) {
    const link = a.href;
    $('#linkDownload').val(link);
}

$('#form-newsletter-docs').submit(function( event ){
    event.preventDefault();
    const email = $('#email').val();
    const type = $(".type:checked").val();
    const newsletter = $("#check-news").val();
    if ($('#form-newsletter-docs').valid()) {
        $.ajax({
            method: "GET",
            url: "/register-newsletter",
            data: { email: email, type: type, newsletter_insc: newsletter, receive_projects: null, id_list: 10137233, IsExcludedFromCampaigns: true },
            success : function(data) {
                $('#message').html(data.message);
                setTimeout(function(){
                    $('#ModalNewsletter').modal('toggle');
                    window.location.href = $('#linkDownload').val();
                    setTimeout(function(){
                        location.reload();
                    }, 1000);
                }, 3000);
            },
            error : function(results) {
                console.log(results.responseJSON);
                $('#message').html(results.responseJSON.message);
            }
        });
    }
});