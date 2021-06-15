$('.open-modal').click(function (e) {
    e.preventDefault();
    var block = this.name;
    $('#block').val(block);
    $('#modalOfferRequest').modal('show');
});
var divMessage = $('#div-message');
$('#form-offer-request').submit(function (event) {
    event.preventDefault();
    if ($('#form-offer-request').valid()) {
        let url = $('#url').val();
        let lastName = $('#l-name').val();
        let firstName = $('#f-name').val();
        let email = $('#email').val();
        let block = $('#block').val();
        const type = $(".type:checked").val();
        let messageText = $('#messageText').val();
        $.ajax({
            method: "GET",
            url: url,
            data: { lastName: lastName, firstName: firstName, email: email, type: type, block: block, message: messageText },
            success : function(data) {
                divMessage.removeClass("alert alert-danger");
                divMessage.addClass("alert alert-success");
                $('#message').html(data.message);
                $('#form-offer-request').remove();
            },
            error : function(results) {
                divMessage.removeClass("alert alert-success");
                divMessage.addClass("alert alert-danger");
                $('#message').html(results.responseJSON.message);
            }
        });
    }
});