////////////////////////////////////

$('#company_RIB').on('input', function (event) {
   this.value = this.value.replace(/[^0-9]/g, '');
});

$("#add-fund").click(function(){
   addFund();
});

function addFund() {
   const inputConter = $("#widgets-counter");
   const divFund = $("#company_useOfFundsCollecteds");
   const index = + inputConter.val();
   const tmpl = divFund.data('prototype').replace(/__name__/g,index);
   divFund.append(tmpl);
   var inputFund = $('#txt-first-fund');
   var fund = inputFund.val();
   $('#company_useOfFundsCollecteds_'+index+'_description').val(fund);
   inputFund.val(null);
   inputConter.val(index + 1);
   handleDeleteButtons();
   const count = $("#company_useOfFundsCollecteds div.form-group.use-fund").length;
   if (count >= 5) {
      $("#add-fund").hide();
   }
}

function handleDeleteButtons(){
   $('button[data-action="delete-fund"]').click(function(){
      const target = this.dataset.target;
      $(target).remove();
      const count = $("#company_useOfFundsCollecteds div.form-group.use-fund").length;
      if (count >= 5) {
         $("#add-fund").hide();
         $("#txt-first-fund").hide();


      } else {
         $("#add-fund").show();
         $("#txt-first-fund").show();
      }
   });
   const count = $("#company_useOfFundsCollecteds div.form-group.use-fund").length;
   if (count >= 5) {
      $("#add-fund").hide();
      $("#txt-first-fund").hide();
   } else {
      $("#add-fund").show();
      $("#txt-first-fund").show();
   }
}

function updateCounter(){
   const count = $("#company_useOfFundsCollecteds div.form-group.use-fund").length;
   $("#widgets-counter").val(count);
   if (count >= 5) {
      $("#add-fund").hide();
   } else {
      $("#add-fund").show();
   }
}

updateCounter();
handleDeleteButtons();

function showURL(input) {
   //get the file name
   var fileName = $(input).val();
   var array = fileName.split("\\");
   //replace the "Choose a file" label
   $(input).next('.custom-file-label').html(array[array.length-1]);
   input.parentElement.classList.add("choosed");
}

$("#add-document").click(function(){
   addDocument();
});

function addDocument() {
   const documentCounter = $("#document-counter");
   const divFund = $("#company_documents");
   const index = + documentCounter.val();
   const tmpl = divFund.data('prototype').replace(/__name__/g,index);
   divFund.append(tmpl);
   documentCounter.val(index + 1);
   handleDeleteButtonsDocuments();
   const count = $("#company_documents div.form-document").length;
   if (count >= 4) {
      $("#add-document").hide();
   }
}

function handleDeleteButtonsDocuments(){
   $('button[data-action="delete-document"]').click(function(){
      const target = this.dataset.target;
      $(target).remove();
      const count = $("#company_documents div.form-document").length;
      if (count >= 4) {
         $("#add-document").hide();
      } else {
         $("#add-document").show();
      }
   });
   const count = $("#company_documents div.form-document").length;
   if (count >= 4) {
      $("#add-document").hide();
   } else {
      $("#add-document").show();
   }
}

function updateCounterDocument(){
   const count = $("#company_documents div.form-document").length;
   $("#document-counter").val(count);
   if (count >= 4) {
      $("#add-document").hide();
   } else {
      $("#add-document").show();
   }
}

updateCounterDocument();
handleDeleteButtonsDocuments();
