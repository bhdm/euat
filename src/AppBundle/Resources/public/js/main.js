$(document).ready(function(){
    $('#login').click(function(){
        $('#loginRegisterModal').modal();
    });
    $('.login2').click(function(){
        $('#loginRegisterModal').modal();
        return false;
    });

    $('.enrolment').click(function(){
        $('#enrolmentModal').modal();
    });

    $('.owl-carousel').owlCarousel({
        loop:true,
        margin:10,
        nav: true,
        navText: [' ',' '],
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


    $(".county").chosen({disable_search_threshold: 10});


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


    $('.phone').mask('+999 (000) 000-00-00');


    $(window).scroll(function() {
        if($(this).scrollTop() != 0) {
            $('#toTop').fadeIn();
            $('#toTop2').css('display','block');
        } else {
            $('#toTop').fadeOut();
            $('#toTop2').css('display','none');
        }
    });
    $('#toTop').click(function() {
        $('body,html').animate({scrollTop:0},800);
    });
    $('#toTop2').click(function() {
        $('body,html').animate({scrollTop:0},800);
    });

    $(".link").on("click","a", function (event) {
        event.preventDefault();
        var id  = $(this).attr('href'),
        top = $(id).offset().top;
        $('body,html').animate({scrollTop: top}, 1500);
    });


});