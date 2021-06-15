const selectCity =  $('#select-city');

$( "#order" ).change(function() {
    filterCompany();
});
$( "#verified" ).change(function() {
    filterCompany();
});

const inputSearch = document.getElementById("search");
inputSearch.addEventListener("keyup", function(event) {
    if (event.keyCode == 13) {
        filterCompany();
    }
});

domainSelectPicker.on('change',function() {
    filterCompany();
});

selectCity.on('change',function() {
    filterCompany();
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

function filterCompany() {
    const domains = domainSelectPicker.val();
    const offset = 0;
    //const order = $('#order').val();
    //const verified = $('#verified').val();
    const search = $('#search').val();
    const city = selectCity.val();
    $('#msg').text('');
    var link = "/" + locale + "/crowdfunding-lancement-campagne-dons/search?";
    for (var i = 0; i < domains.length; i++) {
        link += "domains[]=" + domains[i] + "&";
    }
    link += "offset=" + offset + "&city=" + city + "&search=" + search;
    $.ajax({
        method: "GET",
        url: linkMore,
        data: { domains: domains, offset: offset, city: city, search: search},
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

function loadMore() {
    var load = $('#loadMore').val();
    if (load === "1") {
        $('#loadMore').val("0");
        const domains = domainSelectPicker.val();
        const offset = parseInt($('#offset').val());
        //const order = $('#order').val();
        //const verified = $('#verified').val();
        const search = $('#search').val();
        const city = selectCity.val();
        $('#gif').show();
        $.ajax({
            method: "GET",
            url: linkMore,
            data: { domains: domains, offset: offset, city: city, search: search},
            success:function (results) {
                $('#results').append(results);
                $('#offset').val(offset + 6);
                $('#gif').hide();
            }
        })
    }
}
