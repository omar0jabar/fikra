$(document).ready(function() {

      var pageBlock = $('#page_blocks');
      function changeCol(id){
         console.log(id);
         let colLarge = 'col-lg-'+$('#'+id+'_colLarge').val();
         $('#block_'+id).attr('class', 'form-group block row '+colLarge);
      }

      $( ".selectCol" ).change(function() {
         let id = $(this).data('id');
         changeCol(id);
      });
      
      pageBlock.children().each(function( i, value ) {
         var id = value.id;
         var index = id.split('_');
         var index = index[index.length-1];
         var id = id.replace("block_", "#");
         var type = $(id+'_type').val();
         if (type === 'video') {
            fields = [10, 11, 12];//10=>alt  //11=>uploadImage //12=>content
         } else if ( type === 'image') {
            fields = [12, 9];//12=>content //9=>link video
         } else if (type === 'text') {
            fields = [9, 10, 11];//9=> link video //10=>alt  //11=>uploadImage
         }
         removeFields(index, type, fields);
         changeCol('page_blocks_'+index);
         $('#widgets-block-counter').attr('value', parseInt(index)+1);
      });

      //remove block
      $('button[data-action="delete-block"]').click(function(){
         const target = this.dataset.target;
         
         $(target).remove();
      });

      // add block Image
         $("#add-block-image").click(function(){
            $('#addBlock').modal('toggle');
            //12=>content //9=>link video
            addBlock('image', [12, 9]);

            
         });
      //End Block Image

      // add block video
         $("#add-block-video").click(function(){
            $('#addBlock').modal('toggle');
            //10=>alt  //11=>uploadImage //12=>content
            addBlock('video', [10, 11, 12]);
         });
      //End Block Video


      // add block texte
         $("#add-block-text").click(function(){
            $('#addBlock').modal('toggle');
            //9=> link video //10=>alt  //11=>uploadImage 
            addBlock('text', [9, 10, 11]);
         });
      //End Block Text


      function addBlock(type='', fields) {
         var elem = $('#widgets-block-counter');
         const inputConter = elem.val();
         const divBlockVideo = pageBlock;
         const index = +inputConter;
         const tmpl = divBlockVideo.data('prototype').replace(/__name__/g,index);
         pageBlock.first().append(tmpl);

         removeFields(index, type, fields);

         elem.attr('value', inputConter+1);
      }


      function removeFields(index, type, fields) {
         var children = $('#block_page_blocks_'+index).children();
         $('#page_blocks_'+index+'_type').attr('value', type);
         $('#blockName_page_blocks_'+index).text(type);
         $.each(fields, function( index, value ) {
           children[value].remove()
         });
         
      }

      //Sortable block
      pageBlock.sortable({
         stop: function( event, ui ) {
            var itemOrder = pageBlock.sortable("toArray");
            for (let i = 0; i < itemOrder.length; i++) {
              $('#'+itemOrder[i]+' .position').val((i+1));
            }

         }
      });

    
});