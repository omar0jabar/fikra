$("#add-document").click(function(){
   addDocument();
});

const maxDocuments = $("#maxDocuments").val();
const maxPhotos = $("#maxPhotos").val();

function addDocument() {
   const inputConter = $("#document-counter");
   const divDocument = $("#project_form_documents");
   const index = +inputConter.val();
   const tmpl = divDocument.data('prototype').replace(/__name__/g,index);
   divDocument.append(tmpl);
   inputConter.val(index + 1);
   handleDeleteDocument();
   const count = $("#project_form_documents div.form-document").length;
   if (count >= maxDocuments) {
      $("#add-document").hide();
   }
}

function handleDeleteDocument(){
   $('button[data-action="delete-document"]').click(function(){
      const target = this.dataset.target;
      $(target).remove();
      const count = $("#project_form_documents div.form-document").length;
      if (count >= maxDocuments) {
         $("#add-document").hide();
      } else {
         $("#add-document").show();
      }
   });
}

function updateDocumentCounter(){
   const count = $("#project_form_documents div.form-document").length;
   $("#document-counter").val(count);
   if (count >= maxDocuments) {
      $("#add-document").hide();
   } else {
      $("#add-document").show();
   }
}

handleDeleteDocument();
updateDocumentCounter();


$("#add-photo").click(function(){
   addPhoto();
});

function addPhoto() {
   const inputConter = $("#photo-counter");
   const divPhoto = $("#project_form_galleryPhotos");
   const index = +inputConter.val();
   const tmpl = divPhoto.data('prototype').replace(/__name__/g,index);
   divPhoto.append(tmpl);
   inputConter.val(index + 1);
   handleDeletePhoto();
   const count = $("#project_form_galleryPhotos div.c-form-gallery").length;
   if (count >= maxPhotos) {
      $("#add-photo").hide();
   }
}

function handleDeletePhoto(){
   $('button[data-action="delete-photo"]').click(function(){
      const target = this.dataset.target;
      $(target).remove();
      const count = $("#project_form_galleryPhotos div.c-form-gallery").length;
      if (count >= maxPhotos) {
         $("#add-photo").hide();
      } else {
         $("#add-photo").show();
      }
   });
}

function updatePhotoCounter(){
   const count = $("#project_form_galleryPhotos div.c-form-gallery").length;
   $("#photo-counter").val(count);
   if (count >= maxPhotos) {
      $("#add-photo").hide();
   } else {
      $("#add-photo").show();
   }
}
handleDeletePhoto();
updatePhotoCounter();