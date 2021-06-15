$("#add-member").click(function(){
    addMember();
});

function addMember() {
    const inputConter = $("#member-counter");
    const divMember = $("#project_form_teamMembers");
    const index = +inputConter.val();
    const tmpl = divMember.data('prototype').replace(/__name__/g,index);
    divMember.append(tmpl);
    inputConter.val(index + 1);
    handleDeleteMember();
    $('#btn-delete-project_form_teamMembers_0').remove();
    const count = $("#project_form_teamMembers div.form-team").length;
    if (count >= 5) {
        $("#add-member").hide();
    }
}

function handleDeleteMember(){
    $('button[data-action="delete-member"]').click(function(){
        const target = this.dataset.target;
        $(target).remove();
        const count = $("#project_form_teamMembers div.form-team").length;
        if (count >= 5) {
            $("#add-member").hide();
        } else {
            $("#add-member").show();
        }

    });
}

function updateMemberCounter(){
    const count = $("#project_form_teamMembers div.form-team").length;
    $("#member-counter").val(count);
    if (count >= 5) {
        $("#add-member").hide();
    } else {
        $("#add-member").show();
    }
}

function addMemberIfNotExist() {
    const count = $("#project_form_teamMembers div.form-team").length;
    if (count === 0) {
        addMember();
    }
    $('#btn-delete-project_form_teamMembers_0').remove();
}

addMemberIfNotExist();
handleDeleteMember();
updateMemberCounter();