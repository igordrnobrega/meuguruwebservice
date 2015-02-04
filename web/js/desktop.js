$(document).ready(function() {

    var img      = $('img');
    var iterator = 0;
    var srcArray = [
        '../img/bg-header.jpg',
        '../img/icon-1.png',
        '../img/icon-2.png',
        '../img/icon-3.png',
        '../img/icon-facebook.png',
        '../img/icon-instagram.png',
        '../img/icon-pinterest.png',
        '../img/icon-twiter.png',
        '../img/img-coelho.png',
        '../img/img-logomarca-cnb.png',
        '../img/logo-ancar.png'
    ];

    $.each(srcArray, function(index, src) {

        $('<img src="' + src + '" />').load(function() {

            iterator++;

            if(iterator === srcArray.length) {
                $.unblockUI();
            }
        });

    });

    $('.close').modal('hide');

});
