$("a.reply-comment").on('click', function(event){
    event.stopPropagation();
    event.preventDefault();
    var action = $(this).attr("href");
    var form = $('.company_comment_response');
    form.attr('action', action);
    divResponses = $(this).parent().next('.response-form-form');
    divResponses.append(form);
    form.toggle(300)
});
