const linkAll = $('#link-all');
const inputCategories =  $('#categories');
function allArticles() {
    inputCategories.val("");
    $('.nav-link').removeClass("active");
    linkAll.addClass("active");
    filterArticles();
}
function searchByCategory(idCategory) {
    linkAll.removeClass('active');
    const currentLink = $('#link-'+idCategory);
    currentLink.addClass('active');
    const value = inputCategories.val();
    if (value !== null && value !== '') {
        const exist = value.includes(idCategory);
        if (exist === true) {
            currentLink.removeClass("active");
            const res = value.replace(idCategory, '');
            const arr = res.split(',');
            const lastResults = arr.filter(function( element ) {
                return element !== '';
            });
            inputCategories.val(lastResults);
            if (lastResults == "") {
                linkAll.addClass('active');
            }
        } else {
            inputCategories.val(value+","+idCategory);
        }
        filterArticles();
    } else {
        inputCategories.val(idCategory);
        filterArticles();
    }
}

function filterArticles() {
    const categories = $('#categories').val();
    const path = $('#path').val();
    const arrayCategories = categories.split(',');
    const page = 1;
    $('#msg').text('');
    $.ajax({
        method: "GET",
        url: path,
        data: { categories: arrayCategories, page: page },
        success:function( results ) {
            $('#events').html(results);
            $('#page').val(2);
            /*history.pushState(
                {},"",
                link
            );*/
        },
        error: function(xhr) {
            //alert("an error occurred while loading data ...");
        }
    });
}


$(window).scroll(function () {
    if($(window).scrollTop() >= $(document).height() - $(window).height() - $('footer').height()) {
        var load = $('#more').val();
        if (load === "1") {
            $('#more').val("0");
            const categories = $('#categories').val();
            const path = $('#path').val();
            const arrayCategories = categories.split(',');
            const page = parseInt($('#page').val());
            $('#gif').show();
            $.ajax({
                method: "GET",
                url: path,
                data: { categories: arrayCategories, page: page },
                success:function (results) {
                    $('#events').append(results);
                    $('#page').val(page+1);
                    //$('#gif').hide();
                }
            })
        }
    }
});