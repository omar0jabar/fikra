$(document).ready( function () {
    var it = 5;
    var iter = $('#iter').val();
    const showMore = $('#showMore').val();
    $('#showMore').remove()
    $( "#btn-load-more").on( "click", function() {
        
        const showMore = $('#showMore').val();
        $('#showMore').remove()
        var it = 1;
        const iter = parseInt($('#iter').val());
        getData(it,iter+1);
    });


    function getData(iterr,it=0) {
        $('.no-result-found').hide();
        const search = 'search';
        const iter = parseInt($('#iter').val());
        const canSearch = $('#input_canSearch').val();
        const count = 0;//$('#count').val();

        
        //const showMore = $('#showMore').val();
        const montantMin = $('#montant').val();
        const montantMax = $('#montant').val();
        const fondType = $('#fondType').val();
        const financement = $('#financement').val();
        const gestionnaire = $('#gestionnaire').val();
        const phase = $('#phase').val();
        const secteurSelect = $('#secteur').val();
        const motCle = $('#motCle').val();
        var link = linkMore+'?fondType='+fondType+'&gestionnaire='+gestionnaire+'&financement='+financement;
        link+= '&secteur='+secteurSelect+'&phase='+phase+'&montantMin='+montantMin;
        link+= '&montantMax='+montantMax;
        link+= '&motCle='+motCle+'&iter=0&search='+search;




        console.log(link)
        $('#showMore').remove();
        //$('.load-more-project').remove();
        $('#gif').show();
        $.ajax({
            method: "GET",
            url: linkMore,
            data: 
            { 
                fondType: fondType, 
                gestionnaire: gestionnaire, 
                financement: financement, 
                secteur: secteurSelect, 
                phase: phase, 
                montantMin: montantMin, 
                montantMax: montantMax, 
                canSearch: canSearch, 
                count: count, 
                motCle: motCle, 
                iter: it,
                search: search,

            },
            success:function (results) {
                $('#results').append(results);
                $('#iter').val(iter + 1);
                var showMore = parseInt($('#showMore').val());
                if(showMore == 0) {
                    $('.load-more').hide();
                } else {
                    $('.load-more').show();
                }

                history.pushState(
                    {},"",
                    link
                );
                //$('#show').text('0');
                $('#gif').hide();
            }
        })
    }
})

