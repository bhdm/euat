$(document).ready(function(){
    $('#login').click(function(){
        $('#loginRegisterModal').modal();
    });

    $('.owl-carousel').owlCarousel({
        loop:true,
        margin:10,
        nav:true,
        navText: ['‹','›'],
        responsive:{
            0:{
                items:1
            },
            600:{
                items:1
            },
            1000:{
                items:1
            }
        }
    });

    //$(".university").chosen({disable_search_threshold: 10});


    //$(".county").chosen({disable_search_threshold: 10}).change(function(e){
    //    console.log($(this).val());
    //    $.ajax({
    //        type: 'GET',
    //        url: Routing.generate('get_city')+'?country='+$(this).val(),
    //        dataType: 'json'
    //    }, function (data) {
    //            $.each(data, function (i, val) {
    //                $(".city").html();
    //                $(".city").append('<option value="'+val.value+'">'+val.text+'</option>');
    //            });
    //            $(".city").trigger("chosen:updated");
    //    });
    //});


    //$(".city").chosen({disable_search_threshold: 10});

    //$(".city").chosen({
    //    type: 'GET',
    //    url: Routing.generate('get_city')+'?city='+$(this).val(),
    //    dataType: 'json'
    //}, function (data) {
    //    var results = [];
    //
    //    $.each(data, function (i, val) {
    //        results.push({ value: val.value, text: val.text });
    //    });
    //
    //    return results;
    //});


    $('.phone').mask('+(000) 000-00-00');
});