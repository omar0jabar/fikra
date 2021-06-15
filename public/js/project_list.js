const linkAll = $('#link-all');
const inputSectors =  $('#sectors');
function allProjects() {
    inputSectors.val("");
    $('.nav-link').removeClass("active");
    linkAll.addClass("active");
    filterProject();
}
function searchBySector(idSector) {
    linkAll.removeClass('active');
    const currentLink = $('#link-'+idSector);
    currentLink.addClass('active');
    const value = inputSectors.val();
    if (value !== null && value !== '') {
        const exist = value.includes(idSector);
        if (exist === true) {
            currentLink.removeClass("active");
            const res = value.replace(idSector, '');
            const arr = res.split(',');
            const lastResults = arr.filter(function( element ) {
                return element !== '';
            });
            inputSectors.val(lastResults);
            if (lastResults == "") {
                linkAll.addClass('active');
            }
        } else {
            inputSectors.val(value+","+idSector);
        }
        filterProject();
    } else {
        inputSectors.val(idSector);
        filterProject();
    }
}

function filterProject() {
    const sectors = $('#sectors-select-picker').val();
    const offset = 0;
    const order = $('#order').val();
    const verified = $('#verified').val();
    const raised = $('#raised').val();
    const language = $('#language').val();
    const search = $('#search').val();
    $('#msg').text('');
    var link = "/" +locale + "/search/projects?";
    for (var i = 0; i < sectors.length; i++) {
        link += "sectors[]=" + sectors[i] + "&";
    }
    link += "offset=" + offset + "&order=" + order + "&verified=" + verified + "&raised=" + raised
        + "&language=" + language + "&search=" + search;
    $.ajax({
        method: "GET",
        url: linkMore,
        data: { sectors: sectors, offset: offset, order: order, verified: verified, raised: raised, language: language, search: search},
        success:function( results ) {
            $('#results').html(results);
            $('#offset').val(6);
            history.pushState(
                {},"",
                link
            );
        },
        error: function(xhr) {
            //alert("an error occurred while loading data ...");
        }
    });
}

$( "#order" ).change(function() {
    filterProject();
});
$( "#verified" ).change(function() {
    filterProject();
});
$( "#raised" ).change(function() {
    filterProject();
});
$( "#language" ).change(function() {
    filterProject();
});

var inputSearch = document.getElementById("search");
inputSearch.addEventListener("keyup", function(event) {
    if (event.keyCode == 13) {
        filterProject();
    }
});

$('#sectors-select-picker').on('change',function() {
    filterProject();
});

if ($(window).width() < 768) {
    $('#btn-load-more').hide();
    $(window).scroll(function () {
        if($(window).scrollTop() >= $(document).height() - $(window).height() - $('footer').height()) {
            loadMore();
        }
    });
} else {
    $('#btn-load-more').show();
}

$('#btn-load-more').click(loadMore);

function loadMore() {
    var load = $('#loadMore').val();
    if (load === "1") {
        $('#loadMore').val("0");
        /*const sectors = $('#sectors').val();
        const arraySectors = sectors.split(',');*/
        const sectors = $('#sectors-select-picker').val();
        const offset = parseInt($('#offset').val());
        const order = $('#order').val();
        const verified = $('#verified').val();
        const raised = $('#raised').val();
        const language = $('#language').val();
        const search = $('#search').val();
        $('#gif').show();
        $.ajax({
            method: "GET",
            url: linkMore,
            data: { sectors: sectors, offset: offset, order: order, verified: verified, raised: raised, language: language, search: search},
            success:function (results) {
                $('#results').append(results);
                $('#offset').val(offset + 6);
                $('#gif').hide();
            }
        })
    }
}
