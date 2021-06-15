function showIMG(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#img_cover_project').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
        $('#div-cover').remove();
        showURL(input);
    }
}

function showURL(input) {
    //get the file name
    var path = $(input).val();
    //replace the "Choose a file" label
    var array = path.split("\\");
    var fileName = array[array.length - 1];
    $(input).next('.custom-file-label').html(fileName);
}