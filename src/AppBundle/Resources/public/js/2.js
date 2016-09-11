$(function(){
    var links = {
        'vk' : 'aHR0cHM6Ly92ay5jb20vcHVibGljNzMwNzI1MDE=',
        'tw' : 'aHR0cHM6Ly90d2l0dGVyLmNvbS9FdWF0SW5mbw==',
        'ok' : 'aHR0cDovL29rLnJ1L2dyb3VwLzU3MDM0NTE5NDEyNzcw',
        'fb' : 'aHR0cHM6Ly93d3cuZmFjZWJvb2suY29tL2V1YXQub3JnLw=='
    };
    var $elements = $("a[data-key]");
    for(var i = 0; $elements.length ; i++) {
        var $element = $elements.eq(i);
        var key = $element.data("key");
        $element.attr("href", Base64.decode(links[key]));
    }
});