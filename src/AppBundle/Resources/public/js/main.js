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

    $(".university").chosen({disable_search_threshold: 10});

    $('.phone').mask('+7(000) 000-00-00');
});