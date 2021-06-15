$(document).ready( function () {
    var it = 0;
    // $( "#btn-load-more").on( "click", function() {
    //     console.log('more');
    //     var it = 1;
    //     getData(it);
    // });

    


    $( "#button_search").on( "click", function() {
        
        //$('#button_submit').click();
        getData(it);
    });

    $( "#secteur, #gestionnaire, #financement, #phase, #fondType, #montant").on( "change", function() {
        //$('#button_submit').click();
        
        getData(it);
    });

    // $( "#button_search").on( "click", function() {
    //     console.log('button_search');
    //     const motCle = $('#motCle').val();
    //     $('.content').remove();
    //     $('#input_motCle').attr('value', motCle);
    //     getData(it,0);
    // });


    // $( "#secteur").on( "change", function() {
    //     $('#iter').attr('value', 0);
    //     var values = $(this).val();
    //     var secteurs = values.join(',');
    //     console.log(secteurs);
    //     $('#input_secteurSelect').val(secteurs);
    //     $('.content').remove();
    //     getData(it);
    // });

    // $( "#gestionnaire").on( "change", function() {
    //     $('.content').remove();
    //     var value = $(this).val();
    //     $('#input_gestionnaire').val(value);
    //     getData(it);
    // });

    // $( "#financement").on( "change", function() {
    //     $('.content').remove();
    //     var value = $(this).val();
    //     $('#input_financement').val(value);
    //     getData(it);
    // });

    // $( "#phase").on( "change", function() {
    //     $('.content').remove();
    //     var value = $(this).val();
    //     $('#input_phase').val(value);
    //     getData(it);
    // });

    // $( "#fondType").on( "change", function() {
    //     $('.content').remove();
    //     var value = $(this).val();
    //     $('#input_fondType').val(value);
    //     getData(it);
    // });

    // $( "#montant").on( "change", function() {
    //     $('.content').remove();
    //     var value = $(this).val();
    //     $('#input_montantMin').val(value);
    //     $('#input_montantMax').val(value);
    //     getData(it);
    // });

    function getData(iterr,showMore=0) {
        $('.no-result-found').hide();
        $('#noResult').hide();
        $(".count-result").show();
        $('.content').show();
        $('#showMore').remove();

        $('.content').remove();
        $('#itemTotal').remove();
        const search = 'search';
        //const fondType = $('#input_fondType').val();
        //const gestionnaire = $('#input_gestionnaire').val();
        const iter = parseInt($('#iter').val());
        //const financement = $('#input_financement').val();
        //const secteurSelect = $('#input_secteurSelect').val();
        //const phase = $('#input_phase').val();
        //const montantMin = $('#input_montantMin').val();
        //const montantMax = $('#input_montantMax').val();
        const canSearch = $('#input_canSearch').val();
        //const motCle = $('#input_motCle').val();
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
        //link+= '&iterr='+iterr;
        // if(search == 'search') {
        //     window.location.href = link;
        // }
        // alert(link);
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
                iter: 0,
                search: search,
                iterr: iterr

            },
            success:function (results) {
                if(iterr == 1) {
                    $('#results').append(results);
                } else {
                    $('#results').append(results);
                }
                var total = $('#itemTotal').val();
                console.log('total '+total);
                $('#show').text(total);
                $('#iter').val(0);
                $('#itemTotal').remove();

                if(total == 0) {
                    $(".count-result").hide();
                    $('#noResult').show();
                    $('.content').hide();
                }
                console.log($('#showMore').children)
                var showMore = parseInt($('#showMore').val());
                if(showMore == 0) {
                    $('.load-more').hide();
                } else {
                    console.log('showMore')
                    $('.load-more').show();
                }

                history.pushState(
                    {},"",
                    link
                );

                $('#gif').hide();
            }
        })
    }
})

