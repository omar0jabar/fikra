function search(q, date, limit, id, type, offset) {
    $.ajax({
        method: "GET",
        url: "/documents/search",
        data: { q: q, date: date, type: type, offset: offset, limit: limit}
    })
    .done(function( results ) {
        $('#'+id).append(results);
    });
}

function init(id, idLimit) {
    $('#'+id).text('');
    $('#'+idLimit).attr('value', 10);
}

//PUBLIC DOCUMENT
$( "#morePublicDoc" ).click(function() {
    var fromLimit =parseInt($('#limitPublicDoc').val());
    var offset =parseInt($('#offsetPublicDocument').val());
    var limit = offset + 10;
    $('#limitPublicDoc').attr('value', limit);
    var q = $("#searchPublicDoc" ).val();
    var date = $('#searchPublicDocDate').val();
    var type = 'public';
    var id = 'docPub';
    search(q, date, limit, id, type, offset);
});

var input = document.getElementById("searchPublicDoc");

input.addEventListener("keyup", function(event) {
    if (event.keyCode == 13) {
        event.preventDefault();
        searchPublicDocsOnclick();
    }
});

function searchPublicDocsOnclick() {
    var q = $('#searchPublicDoc').val();
    var date = $('#searchPublicDocDate').val();
    var limit = $('#limitPublicDoc').val();
    var id = 'docPub';
    var type = 'public';
    var idLimit = 'limitPublicDoc';
    init(id, idLimit);
    search(q, date, limit, id, type);
}

/*
$( "#searchPublicDoc" ).keyup(function() {
    var q = $(this).val();
    var date = $('#searchPublicDocDate').val();
    var limit = $('#limitPublicDoc').val();
    var id = 'docPub';
    var type = 'public';
    var idLimit = 'limitPublicDoc';
    var idLimit = 'limitPublicDoc';
    init(id, idLimit);
    search(q, date, limit, id, type);
    
});
 */

$( "#searchPublicDocDate" ).change(function() {
    var date = $(this).val();
    var q = $('#searchPublicDoc').val();
    var limit = $('#limitPublicDoc').val();
    var id = 'docPub';
    var type = 'public';
    var idLimit = 'limitPublicDoc';
    init(id, idLimit);
    search(q, date, limit, id, type);
});


//auto production
$( "#moreAutoDoc" ).click(function() {
    var fromLimit =parseInt($('#limitAutoProduction').val());
    var offset =parseInt($('#offsetAutoProduction').val());
    var limit = offset + 10;
    $('#limitAutoProduction').attr('value', limit);
    var q = $("#searchPublicDoc" ).val();
    var date = $('#searchAutoDate').val();
    var type = 'auto_production';
    var id = 'autoProduction';
    console.log(fromLimit);
    search(q, date, limit, id, type, offset);
});

var input = document.getElementById("searchAutoProduction");

input.addEventListener("keyup", function(event) {
    if (event.keyCode == 13) {
        event.preventDefault();
        searchAutoProdOnclick();
    }
});

function searchAutoProdOnclick() {
    var q = $('#searchAutoProduction').val();
    var date = $('#searchAutoDate').val();
    var limit = $('#limitAutoProduction').val();
    var id = 'autoProduction';
    var type = 'auto_production';
    var idLimit = 'limitAutoProduction';
    init(id, idLimit);
    search(q, date, limit, id, type);
}
/*
$( "#searchAutoProduction").keyup(function() {
    var q = $(this).val();
    var date = $('#searchAutoDate').val();
    var limit = $('#limitAutoProduction').val();
    var id = 'autoProduction';
    var type = 'auto_production';
    var idLimit = 'limitAutoProduction';
    init(id, idLimit);
    search(q, date, limit, id, type);
    
});
*/
$( "#searchAutoDate" ).change(function() {
    var date = $(this).val();
    var q = $('#searchPublicDoc').val();
    var limit = $('#limitAutoProduction').val();
    var id = 'autoProduction';
    var type = 'auto_production';
    var idLimit = 'limitAutoProduction';
    init(id, idLimit);
    search(q, date, limit, id, type);
});

function loadMoreTools() {
    var offset = $('#offsetTools').val();
    $.ajax({
        method: "GET",
        url: "/documents/load-more-tools",
        data: { offset: offset },
        success : function(data) {
            $('#tools').append(data);
            //$('#offsetTools').val(offset + 3);
        },
        error : function(error) {
            alert('error when load more tools! '+ error);
        }
    });
}

function active(input) {
    var tabTest = $(input).data('tab');
    $('#myTabs a[href="#'+tabTest+'"]').tab('show');
}