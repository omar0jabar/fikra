$(document).ready(function() {
    // select 2 for metaTags
    $("#page_metaTags").select2({
        tags: [],
        tokenSeparators: [',']
    });

    updateRowValues();

    var selectorTotalRow = $('#totalRow');
    //
    $( document ).on( "click", ".addRow", function() {
        var totalRow = parseInt(selectorTotalRow.val()) + 1;

        $('.connectedSortable').attr('class', 'row style connectedSortable ui-sortable');
        const html = getRowHtml(totalRow,'', 'select');
        pageBlock.append(html);
        selectorTotalRow.attr('value', totalRow);

        $(".style").sortable({
            connectWith: ".connectedSortable",
        });
    });

    //select row
    $( document ).on( "click", ".connectedSortable", function() {
        var id = $(this)[0].id;
        $('.connectedSortable').attr('class', 'row style connectedSortable ui-sortable');
        $('#'+id).attr('class', 'row style connectedSortable ui-sortable select');
    });
    //Deselect row
    $( document ).on( "dblclick", ".connectedSortable", function() {
        $('.connectedSortable').attr('class', 'row style connectedSortable ui-sortable');
    });

    $( document ).on( "click", ".showBlock", function() {
        $(this).attr('class', 'btn btn-default btn-sm showBlock hide');
        $(this).next().removeClass('hide');
        var id = $(this).data('id');
        $('#toggle_'+id).removeClass('hide');
    });
    $( document ).on( "click", ".hideBlock", function() {
        $(this).attr('class', 'btn btn-default btn-sm hideBlock hide');
        $(this).prev().removeClass('hide');

        var id = $(this).data('id');
        $('#toggle_'+id).addClass('hide');
    });


    var pageBlock = $('#page_blocks');
    var rows = [];
    pageBlock.children().each(function( i, value ) {
        var id = value.id;
        var index = id.split('_');
        index = index[index.length-1];
        id = id.replace("block_", "#");
        var type = $(id+'_type').val();
        var fields = getFields(type);
        removeFields(index, type, fields);
        changeCol('page_blocks_'+index);
        $('#widgets-block-counter').attr('value', parseInt(index)+1);
        if (type === 'video') {
            showVideo(index);
        }
        rows.push($(id+'_row').val());
    });


    //create rows
    var totalRow = parseInt(selectorTotalRow.val()) ;
    if (totalRow > 0) {
        for ( var i = 1 ; i<= totalRow; i++) {
            const html = getRowHtml(i);
            pageBlock.append(html);
        }
        var rowValue = $('#rowsValue').val();
        data = $.parseJSON(rowValue);
        $.each(data, function( index, value ) {
            $('#idRow_'+index).val(value.id);
            $('#classRow_'+index).val(value.class);
        });
        //add blocks to rows
        pageBlock.children().each(function( i, value ) {
            var id = value.id;
            var idItem = id.replace("block_", "#");
            var row = $(idItem+'_row').val();
            $('#row_'+row).append( $('#'+id));
        });
    }

    //add clearFix
    updateClearFix();
    function updateClearFix() {
        $('.marginBottom').each(function( index ) {
            var id = $( this  )[0].id;
            var id2 = id.replace('block_', '#');
            var clearfix = $(id2+'_clearfix').is(':checked');
            $('#clearfix_'+id).remove();
            if (clearfix) {
                $('#'+id).after('<div id="clearfix_'+id+'" class="cms-col-12"></div>');
            } else {
                $('#'+id).after('<div id="clearfix_'+id+'" class=""></div>');
            }
        });
    }
    //events:

    $( document ).on( "click", ".editRow", function() {
        var row = $(this).data('id');
        $('#div-row-info_'+row).toggle();

    });

    $( document ).on( "click", ".toggle", function() {
        $('.fa-plus-circle').toggle();
        $('.fa-minus-circle').toggle();
        $('.listBlocks').toggle();
    });

    $( document ).on( "click", ".removeRow .glyphicon-trash", function() {
        if (confirm("Are you sure to remove this row?")) {
            $(this).parent().parent().remove();
        }
        return false;

    });

    $( document ).on( "click", ".collection-remove", function() {
        if (confirm("Are you sure to remove this block?")) {
            $(this).parent().parent().parent().remove();
        }
        return false;

    });

    $( document ).on( "click", ".collection-add", function() {
        var type = $(this).data('type');
        var id = $(this).data('id');
        var fields = getFields(type);
        addBlock(type, fields, 'after', $('#block_'+id));
    });

    $( document ).on( "change", ".clearfixChange", function() {
        var id = $(this)[0].id;
        id = id.replace("_clearfix", "");
        $('#clearfix_block_'+id).remove();
        if ($('#'+id+'_clearfix').is(':checked')) {
            $('#block_'+id).after('<div id="clearfix_block_'+id+'" class="col-md-12"></div>');
        } else {
            $('#clearfix_block_'+id).remove();
        }
    });

    $( document ).on( "change", ".selectCol", function() {
        let id = $(this).data('id');
        changeCol(id);
    });


    // add block Image
    $( document ).on( "click", "#add-block-image", function() {
        $('#addBlock').modal('toggle');
        addBlock('image', ['content', 'linkVideo', 'width', 'height']);
    });
    //End Block Image

    // add block video
    $( document ).on( "click", "#add-block-video", function() {
        $('#addBlock').modal('toggle');
        addBlock('video', ['alt', 'uploadImage', 'textImage', 'legend', 'linkImage', 'content']);
    });

    //End Block Video

    // add block texte
    $( document ).on( "click", "#add-block-text", function() {
        $('#addBlock').modal('toggle');
        addBlock('text', ['linkVideo', 'alt', 'uploadImage', 'textImage', 'legend', 'linkImage', 'width', 'height']);
    });
    //End Block Text
    $( document ).on( "change", ".clearfixChange", function() {
        if ($(this).is(':checked')) {
            $(this).parent().attr('class', 'icheckbox_square-blue checked');
        } else {
            $(this).parent().attr('class', 'icheckbox_square-blue');
        }
    });


    function addBlock(type='', fields,  methodAdd='append', object=null) {

        var idRow = $('.select')[0];
        var rowItem = parseInt(selectorTotalRow.val()) + 1;

        var elem = $('#widgets-block-counter');
        const inputConter = elem.val();
        const divBlockVideo = pageBlock;
        const index = +inputConter;
        const tmpl = divBlockVideo.data('prototype').replace(/__name__/g,index);

        if ($('.select')[0] === undefined) {
            const html = getRowHtml(rowItem, tmpl, 'select');
            var rowClearFix = rowItem;
            selectorTotalRow.attr('value', rowItem+1);
            pageBlock.first().append(html);
        } else {
            var row = $('.select')[0].id;
            const html = tmpl;
            $('#'+row).append(html);
            var rowClearFix = index;
        }
        updateDomCSS(rowClearFix);




        $(".style").sortable({
            connectWith: ".connectedSortable",
        });
        removeFields(index, type, fields);
        updateRowPos();

        elem.attr('value', parseInt(inputConter)+1);
    }

    var selectorPage_blocks = $("#page_blocks");
    //sortableRow
    selectorPage_blocks.sortable({
        handle: '.removeRow',
        stop: function( event, ui ) {
            console.log('sortableRow');
            updateRowPos();
            updateClearFix();
        }
    });
    selectorPage_blocks.disableSelection();

    //sortableBlock
    var selectorSortableBlock = $(".style");
    selectorSortableBlock.sortable({
        handle: '.handle',
        connectWith: ".connectedSortable",
        start: function (event, ui){
            var id_textarea = ui.item.find("textarea").attr("id");
            if (typeof id_textarea != 'undefined') {
                var editorInstance = CKEDITOR.instances[id_textarea];
                editorInstance.destroy();
                CKEDITOR.remove( id_textarea );
            }
        },
        stop: function( event, ui ) {
            var id_textarea = ui.item.find("textarea").attr("id");
            if (typeof id_textarea != 'undefined') {
                CKEDITOR.replace(id_textarea);
            }
            updateRowPos();
            updateClearFix();
        }
    });
    selectorSortableBlock.disableSelection();

    //functions
    function updateDomCSS(rowItem) {
        //add Style to checkBox after add Block
        var clearfixSelect = $('#page_blocks_'+rowItem+'_clearfix');
        clearfixSelect.after('<div class="icheckbox_square-blue" style="position: relative;"></div>');
        clearfixSelect.next().append(clearfixSelect);
        clearfixSelect.attr('style', 'position: absolute; opacity: 0;width: 100%; height: 100%;');
    }

    function updateRowValues() {
        var idCms = $('#idCms').val();

        if (idCms > 0) {
            $.ajax({
                url: linkGetRows,
                type: "GET",
                dataType: "JSON",
                data: {
                    type: 'page',
                    idCms : idCms
                },

                success: function (rows) {
                    $.each(rows, function (key, row) {
                        $('#classRow_'+row.id).attr('value', row.class);
                        $('#idRow_'+row.id).attr('value', row.idHtml);
                    });
                },
                error: function (err) {
                    alert("An error ocurred while loading attributes rows...");
                }
            });

        }

    }
    function updateRowPos() {
        var rows = $( ".connectedSortable" );
        var row = 1;
        rows.each(function( index ) {
            var id = rows[index].id;
            var blocks = $('#'+id).children();
            blocks.each(function( pos ) {
                var idBlock = blocks[pos].id;
                idBlock = idBlock.replace('block_', '#');
                $(idBlock+'_position').attr('value',pos+1);
                $(idBlock+'_row').attr('value',row);
            });
            row = row+1;
        });
    }

    function getIdUrlYoutube(url) {
        var videoid = url.match(/(?:https?:\/{2})?(?:w{3}\.)?youtu(?:be)?\.(?:com|be)(?:\/watch\?v=|\/)([^\s&]+)/);
        if(videoid != null) {
            return videoid[1];
        } else {
            return url;
        }
    }
    function showVideo(index) {
        var idVideo = $('#page_blocks_'+index+'_linkVideo').val();
        if (idVideo.length > 0) {
            $('#showVideo_page_blocks_'+index).removeClass( "hide" );
            var src = $('#showVideo_page_blocks_'+index).data('src');
            src += getIdUrlYoutube($('#page_blocks_'+index+'_linkVideo').val());
            $('#showVideo_page_blocks_'+index+' iframe').attr('src', src);
        }
    }

    function changeCol(id){
        let colLarge = 'cms-col-'+$('#'+id+'_colLarge').val();
        $('#block_'+id).attr('class',' marginBottom');
        var clearfix = $('#'+id+'_clearfix').is(':checked');
        var clearfixClass = colLarge;
        var colLargeClass = '';
        var separator = '';
        $('#clearfix_block_'+id).remove();
        if (clearfix === true) {
            clearfixClass = separator = 'col-md-12';
            $('#block_'+id).after('<div id="clearfix_block_'+id+'" class="'+separator+'"></div>');
        } else {
            //$('#block_'+id).after('<div id="clearfix_block_'+id+'" class=""></div>');
            //$('#clearfix_'+id).remove();
            $('#clearfix_block_'+id).remove();
        }


        $('#block_'+id).attr('class', 'marginBottom '+colLarge);
        $('#block_'+id+' .block').attr('class', 'form-group  block ');
        var width = $('#'+id+'_width').val();
        var height = $('#'+id+'_height').val();

        var iframe = $('iframe#'+id);
        if ($('#'+id+'_colLarge').val() === 3) {
            iframe.attr('width', '100%');
            iframe.attr('height', '70%');
        } else {
            iframe.attr('width', width);
            iframe.attr('height', height);
        }
    }
    function getFields(type) {
        var fields;
        if (type === 'video') {
            fields = ['alt', 'uploadImage', 'content', 'legend', 'linkImage', 'textImage'];
        } else if ( type === 'image') {
            fields = ['content', 'linkVideo', 'width', 'height'];
        } else if (type === 'text') {
            fields = ['linkVideo', 'alt', 'uploadImage', 'width', 'height', 'legend', 'linkImage', 'textImage'];
        }
        return fields;
    }

    function removeFields(index, type, fields) {
        var children = $('#block_page_blocks_'+index).children();
        $('#page_blocks_'+index+'_type').attr('value', type);
        $('#blockName_page_blocks_'+index).text(type);
        $.each(fields, function( i, value ) {
            var elem = $('#'+value+'_page_blocks_'+index)[0];
            $('#'+value+'_page_blocks_'+index).remove();
        });

    }

    function getRowHtml(rowNum, tpl='', select='') {
        const html = "<div class='sortableRow'>" +
            "<div class='div-infos-row w-100 p-2' id='div-row-info_"+rowNum+"'>" +
            "<h4>Row attributes</h4>" +
            "<input type='text' id='idRow_"+rowNum+"' name='idRow_"+rowNum+"' value='' " +
            "class='form-control mb-1 inputIdRow' placeholder='id'>"+
            "<input type='text' id='classRow_"+rowNum+"' name='classRow_"+rowNum+"'  value='' " +
            "class='form-control mb-1 inputClassRow' placeholder='class'>"+
            "</div>" +
            "<div class='removeRow' >" +
            "<i class='fa fa-arrows-v fa-2x'></i>" +
            "<span class='glyphicon glyphicon-trash btn btn-danger btn-sm'></span>" +
            "<span data-id='"+rowNum+"' class='glyphicon glyphicon-pencil editRow btn btn-success btn-sm mr-1'></span>"+
            "</div>" +
            "<div id='row_"+rowNum+"' class='row style connectedSortable ui-sortable "+select+" '>"+tpl+"</div>" +
            "</div>";
        return html;

    }

    //change langue
    var lang =  $('#page_lang').val();
    var categ =  $('#page_category').val();
    changeCategories(lang, categ);

    $( document ).on( "change", "#page_lang", function() {
        const citySelector = $(this);
        var l = citySelector.val();
        if (l == lang) {
            changeCategories(l,categ);
        } else {
            changeCategories(l);
        }

    });

    function changeCategories(lang, categ) {
        $.ajax({
            url: linkGetCategories,
            type: "GET",
            dataType: "JSON",
            data: {
                lang: lang
            },
            success: function (cats) {
                const select = $("#page_category");
                let span = $('#s2id_page_category a span').first();
                // Remove current options
                span.text('');
                select.html('');
                // Empty value ...
                var selected = '';
                $.each(cats, function (key, cat) {
                    if (cat.id === categ) {
                        selected = 'selected';
                    }
                    select.append('<option value="' + cat.id + '" selected="'+ selected +'" >' +cat.title +'</option>');
                });

                if (categ > 0) {
                    span.text($('#page_category option[value='+categ+']').text());
                } else {
                    select.append('<option selected="selected" value>-- Selectionner --</option>');
                    span.text('-- Selectionner --');
                }

            },
            error: function (err) {
                alert("An error ocurred while loading data ...");
            }
        });
    }

    reInitCk();
});

function reInitCk() {
    $(window).load(function() {
        $('textarea').each(function (i, val) {
            var id_textarea = $(this).attr("id");
            if (typeof id_textarea != 'undefined') {
                var editorInstance = CKEDITOR.instances[id_textarea];
                editorInstance.destroy();
                CKEDITOR.remove( id_textarea );
                CKEDITOR.replace(id_textarea);
            }
        })
    })
}