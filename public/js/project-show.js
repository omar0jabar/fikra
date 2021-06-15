var divMessage = $('#div-message');
$('#form-ask-documentation').submit(function (event) {
    event.preventDefault();
    if ($('#form-ask-documentation').valid()) {
        let url = $('#url').val();
        let token = $('#token').val();
        let message = $('#message-request').val();
        var searchIDs = $("#div-types input:checkbox:checked").map(function(){
            return $(this).val();
        }).get();
        var accept = $("#div-check input:checkbox:checked").map(function(){
            return $(this).val();
        }).get();
        var valAccept = null;
        if (accept.length > 0) {
            valAccept = 1;
        } else {
            valAccept = 0;
        }
        $.ajax({
            method: "GET",
            url: url,
            data: { token: token, message: message, ids: searchIDs, accept: valAccept },
            success : function(data) {
                divMessage.removeClass("alert alert-danger");
                divMessage.addClass("alert alert-success");
                $('#message').html(data.message);
                $('#form-ask-documentation').remove();
            },
            error : function(results) {
                divMessage.removeClass("alert alert-success");
                divMessage.addClass("alert alert-danger");
                $('#message').html(results.responseJSON.message);
            }
        });
    }
});

$('#form-send-message').submit(function (event) {
    event.preventDefault();
    if ($('#form-send-message').valid()) {
        let inputObj = $('#msg-object');
        let inputMsg = $('#msg-content');
        let url = $('#send-msg-url').val();
        let token = $('#msg-token').val();
        let object = inputObj.val();
        let message = inputMsg.val();
        $.ajax({
            method: "GET",
            url: url,
            data: {token: token, object: object, message: message},
            success: function (data) {
                inputObj.val("");
                inputMsg.val("");
                divMessage.removeClass("alert alert-danger");
                divMessage.addClass("alert alert-success");
                $('#message').html(data.message);
                $('#form-send-message').remove();

            },
            error: function (results) {
                divMessage.removeClass("alert alert-success");
                divMessage.addClass("alert alert-danger");
                $('#message').html(results.responseJSON.message);
            }
        });
    }
});

var divMessage2 = $('#div-message2');
$('#form-message2').submit(function (event) {
    event.preventDefault();
    if ($('#form-message2').valid()) {
        let inputObj = $('#msg-object2');
        let inputMsg = $('#msg-content2');
        let url = $('#send-msg-url2').val();
        let token = $('#msg-token2').val();
        let object = inputObj.val();
        let message = inputMsg.val();
        $.ajax({
            method: "GET",
            url: url,
            data: {token: token, object: object, message: message},
            success: function (data) {
                inputObj.val("");
                inputMsg.val("");
                divMessage2.removeClass("alert alert-danger");
                divMessage2.addClass("alert alert-success");
                $('#message2').html(data.message);
                $('#form-message2').remove();

            },
            error: function (results) {
                //console.log(results);
                divMessage2.removeClass("alert alert-success");
                divMessage2.addClass("alert alert-danger");
                $('#message2').html(results.responseJSON.message);
            }
        });
    }
});