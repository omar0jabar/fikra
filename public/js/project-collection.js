$("#add-service").click(function(){
   addService();
});

function addService() {
   const inputConter = $("#widgets-counter-services");
   const divAvantage = $("#project_form_services");
   const index = +inputConter.val();
   const tmpl = divAvantage.data('prototype').replace(/__name__/g,index);
   divAvantage.append(tmpl);
   var inputAvantage = $('#txt-first-service');
   var avantage = inputAvantage.val();
   $('#project_form_services_'+index+'_name').val(avantage);
   inputAvantage.val(null);
   inputConter.val(index + 1);
   handleDeleteButtonsServices();
   const count = $("#project_form_services div.form-group").length;
   if (count >= 5) {
      $("#add-service").hide();
   }
}

function handleDeleteButtonsServices(){
   $('button[data-action="delete-service"]').click(function(){
      const target = this.dataset.target;
      $(target).remove();
      const count = $("#project_form_services div.form-group").length;
      if (count >= 5) {
         $("#add-service").hide();
         $("#txt-first-service").hide();


      } else {
         $("#add-service").show();
         $("#txt-first-service").show();
      }
   });
   const count = $("#project_form_services div.form-group").length;
   if (count >= 5) {
      $("#add-service").hide();
      $("#txt-first-service").hide();
   } else {
      $("#add-service").show();
      $("#txt-first-service").show();
   }
}

function updateCounterServices(){
   const count = $("#project_form_services div.form-group").length;
   $("#widgets-counter-services").val(count);
   if (count >= 5) {
      $("#add-service").hide();
   } else {
      $("#add-service").show();
   }
}

updateCounterServices();
handleDeleteButtonsServices();

////////////////////////////////////


$("#add-avantage").click(function(){
   addAvantage();
});

function addAvantage() {
   const inputConter = $("#widgets-counter");
   const divAvantage = $("#project_form_avantages");
   const index = +inputConter.val();
   const tmpl = divAvantage.data('prototype').replace(/__name__/g,index);
   divAvantage.append(tmpl);
   var inputAvantage = $('#txt-first-avantage');
   var avantage = inputAvantage.val();
   $('#project_form_avantages_'+index+'_name').val(avantage);
   inputAvantage.val(null);
   inputConter.val(index + 1);
   handleDeleteButtons();
   const count = $("#project_form_avantages div.form-group").length;
   if (count >= 5) {
      $("#add-avantage").hide();
   }
}

function handleDeleteButtons(){
   $('button[data-action="delete-avantage"]').click(function(){
      const target = this.dataset.target;
      $(target).remove();
      const count = $("#project_form_avantages div.form-group").length;
      if (count >= 5) {
         $("#add-avantage").hide();
         $("#txt-first-avantage").hide();


      } else {
         $("#add-avantage").show();
         $("#txt-first-avantage").show();
      }
   });
   const count = $("#project_form_avantages div.form-group").length;
   if (count >= 5) {
      $("#add-avantage").hide();
      $("#txt-first-avantage").hide();
   } else {
      $("#add-avantage").show();
      $("#txt-first-avantage").show();
   }
}

function updateCounter(){
   const count = $("#project_form_avantages div.form-group").length;
   $("#widgets-counter").val(count);
   if (count >= 5) {
      $("#add-avantage").hide();
   } else {
      $("#add-avantage").show();
   }
}

/*function addAvantagesrIfNotExist() {
   const count = $("#project_form_avantages div.form-group").length;
   if (count === 0) {
      addAvantage();
   }
   //$('#btn-delete-project_form_teamMembers_0').remove();
}

addAvantagesrIfNotExist();*/
updateCounter();
handleDeleteButtons();


$("#add-finance").click(function(){
   addFinance();
});

function addFinance() {
   const inputConter = $("#widgets-finance-counter");
   const divFinance = $("#project_form_projectFinances");
   const index = +inputConter.val();
   const tmpl = divFinance.data('prototype').replace(/__name__/g,index);
   divFinance.append(tmpl);
   inputConter.val(index + 1);
   handleDeleteFinanceButtons();
   const count = $("#project_form_projectFinances div.form-finance").length;
   var inputFinance = $('#txt-first-finance');
   var finance = inputFinance.val(); //
   $('#project_form_projectFinances_'+index+'_detail').val(finance);
   inputFinance.val(null);
   if (count >= 5) {
      $("#add-finance").hide();
      $("#txt-first-finance").hide();
   }
}

function handleDeleteFinanceButtons(){
   $('button[data-action="delete-finance"]').click(function(){
      const target = this.dataset.target;
      $(target).remove();
      const count = $("#project_form_projectFinances div.form-finance").length;
      if (count >= 5) {
         $("#add-finance").hide();
         $("#txt-first-finance").hide();
      } else {
         $("#add-finance").show();
         $("#txt-first-finance").show();
      }
   });
   const count = $("#project_form_projectFinances div.form-finance").length;
   if (count >= 5) {
      $("#add-finance").hide();
      $("#txt-first-finance").hide();
   } else {
      $("#add-finance").show();
      $("#txt-first-finance").show();
   }
}

function updateFinanceCounter(){
   const count = $("#project_form_projectFinances div.form-finance").length;
   $("#widgets-finance-counter").val(count);
   if (count >= 5) {
      $("#add-finance").hide();
      $("#txt-first-finance").hide();
   } else {
      $("#add-finance").show();
      $("#txt-first-finance").show();
   }
}

updateFinanceCounter();
handleDeleteFinanceButtons();

