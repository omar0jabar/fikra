showOrHideAnotherSalesChannels();
showOrHideAnotherBusinessModel();

$('#project_form_moreSalesChannels').hide();
$('#project_form_moreBusinessModel').hide();
$('#project_form_foreignCountry').hide();


function showOrHideAnotherSalesChannels() {
   if ($("#project_form_otherSalesChannels").is(":checked") === true) {
      $('#project_form_moreSalesChannels').show();
   } else {
      $('#project_form_moreSalesChannels').hide();
   }
}

function showOrHideAnotherBusinessModel() {
   if ($("#project_form_otherBusinessModel").is(":checked") === true) {
      $('#project_form_moreBusinessModel').show();
   } else {
      $('#project_form_moreBusinessModel').hide();
   }
}

/*function showOrHideForeignCountry() {
   if (document.getElementById("project_form_otherCountry").checked === true) {
      $('#project_form_foreignCountry').show();
   } else {
      $('#project_form_foreignCountry').hide();
   }
}*/

$('.denomination').hide();
$('.creating-date').hide();
$('.form-rc').hide();
$('.city').hide();

if ($('#project_form_startup_0').is(":checked")) {
   $('.denomination').show();
   $('.creating-date').show();
   $('.form-rc').show();
   $('.city').show();
}
$("#project_form_startup_0").click(function () {
   if ($(this).is(":checked")) {
      $('.denomination').show();
      $('.creating-date').show();
      $('.form-rc').show();
      $('.city').show();
   }
});

$("#project_form_startup_1").click(function () {
   if ($(this).is(":checked")) {
      $('.denomination').hide();
      $('.creating-date').hide();
      $('.form-rc').hide();
      $('.city').hide();
   }
});

var amountInput = $('#project_form_amount');
var unspecifiedInput = $('#project_form_hasNotAmount');

if (unspecifiedInput.is(":checked")) {
   amountInput.val(0);
   amountInput.attr('disabled', true);
   amountInput.addClass('disabled');
} else {
   amountInput.attr('disabled', false);
   amountInput.removeClass('disabled');
}

unspecifiedInput.click(function () {
   if ($(this).is(":checked")) {
      amountInput.val(0);
      amountInput.attr('disabled', true);
      amountInput.addClass('disabled');
   } else {
      amountInput.attr('disabled', false);
      amountInput.removeClass('disabled');
   }
});

function showIMGMember(input) {
   if (input.files && input.files[0]) {
      var id = input.id;
      var index = id.slice(25, -15);
      var block = $('#project_form_teamMembers_'+index);
      var divImgShoosed = $('#div-img-project_form_teamMembers_'+index);
      var reader = new FileReader();
      reader.onload = function (e) {
         divImgShoosed.attr('style', 'display: block');
         divImgShoosed.addClass('show');
         $('#img_project_form_teamMembers_'+index).attr('src', e.target.result);
         input.parentElement.classList.add("edited");
         $("#col_project_form_teamMembers_"+index+" > div.c-upload-img > fieldset.form-group > img.vich-img").hide();
         //console.log($("fieldset.form-group").last());
      };
      reader.readAsDataURL(input.files[0]);
      showURLTeam(input);
   }
}

function showURLTeam(input) {
   //get the file name
   var fileName = $(input).val();
   var array = fileName.split("\\");
   //replace the "Choose a file" label
   $(input).next('.custom-file-label').html(array[array.length-1]);
}

function showIMG(input) {
   if (input.files && input.files[0]) {
      var id = input.id;
      var index = id.slice(27, -15);
      //alert(str);
      var reader = new FileReader();
      reader.onload = function (e) {
         $('#img_project_form_galleryPhotos_'+index).attr('src', e.target.result);
         $("#col_project_form_galleryPhotos_"+index+" > div.c-section-upload-file > img.vich-img").hide();
         $("#col_project_form_galleryPhotos_"+index+" > div.c-section-gallery").show();
      };
      reader.readAsDataURL(input.files[0]);
      showIMGGallery(input);
   }
}

function showIMGGallery(input) {
   //get the file name
   var fileName = $(input).val();
   var array = fileName.split("\\");
   //replace the "Choose a file" label
   $(input).next('.custom-file-label').html(array[array.length-1]);
   input.parentElement.classList.add("edited");
}

function showURL(input) {
   //get the file name
   var fileName = $(input).val();
   var array = fileName.split("\\");
   //replace the "Choose a file" label
   $(input).next('.custom-file-label').html(array[array.length-1]);
   input.parentElement.classList.add("choosed");
}


function deselectPorteurs(input) {
   const inputId = input.id;
   const index = inputId.slice(25, -8);
   const array = [0, 1, 2, 3, 4];
   array.forEach(function(element) {
      if (element != index) {
         $('#project_form_teamMembers_' + element + '_porteur').prop('checked', false);
      }
   });
}