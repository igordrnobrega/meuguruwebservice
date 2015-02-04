$(document).ready(function() {

    $('#bt-jogar').off('click').on('click', function() {
      $.ajax({
         type: 'GET',
         url: '/jogar',
         dataType: 'html',
         beforeSend: function() {
              $('#load').removeClass('hide');
         },
          success: function(data) {
              $('#load').addClass('hide');

              if (data) {
                  $('body').html(data);
              }
          }
      });
    });

    $('#bt-executar').on('click', function() {

        if (!$("#senha").val() == "") {

            var selected = new Array();
            $("input:checkbox[id=fg]:checked").each(function() {
                selected.push($(this).val());
            });
            if(selected.length > 0){
                $.ajax({
                    type: 'GET',
                    url: '/retirado',
                    dataType: 'json',
                    assync: false,
                    data: 'senha='+ $("#senha").val() +'&list='+selected,
                    success: function(data) {
                        if (data) {
                            window.location.reload();
                        } else {
                            $(".bg-danger").removeClass("hide");
                        }
                    }
                });
            }else{
                $(".bg-danger").removeClass("hide");
            }

        } else {

            $(".bg-danger").removeClass("hide");

        }

        return false;

    });

    $('input[type="cpf"]').setMask('cpf');
    $('input[type="tel"]').setMask('phone');

    $('#bt-signin').off('click').on('click', function(e) {
        $.ajax({
               type: 'POST',
               url: '/signin',
               data: $('#signin').serialize(),
               dataType: 'html',
               beforeSend: function() {
                    $('#load').removeClass('hide');
               },
                success: function(data) {
                    $('#load').addClass('hide');

                    if (data) {
                        $('body').html(data);
                    } else {
                        $('.bg-danger').removeClass('hide');
                    }
                }
        });
        e.preventDefault();
        return false;
    });

    $('#bt-responder').off('click').on('click', function(e) {
        $.ajax({
               type: 'POST',
               url: '/resp',
               data: $('#pergunta').serialize(),
               dataType: 'json',
               beforeSend: function() {
                    $('#load').removeClass('hide');
               },
               success: function(data) {
                        $('#load').addClass('hide');
                       if (data) {
                           $('#pergunta-mobile').fadeOut(500, function() {
                                $('#acerto-mobile').fadeIn(500);
                           });
                       }else{
                           $('#pergunta-mobile').fadeOut(500, function() {
                                $('#erro-mobile').fadeIn(500);
                           });
                           $('#bt-voltar').on('click', function(){
                               $('#erro-mobile').fadeOut(500, function() {
                                    $('#pergunta-mobile').fadeIn(500);
                               });
                           })
                       };
                   }
        });
        e.preventDefault();
        return false;
    });

    $('#menu-mobile ul li a, #header .bt-rosa a').on('click', function(e) {

        var setId = $(this).attr("id");

        switch(setId) {
            case ("ir-instrucoes"):
                var destino = "instrucoes";
                break;
            case ("ir-regulamento"):
                var destino = "regulamento";
        }

        if ($('#menu-mobile').is(':visible')) {
            $('html, body').animate({
                scrollTop: $("#"+destino).offset().top - 100
            }, 1000);
        } else {
             $('html, body').animate({
                scrollTop: $("#"+destino).offset().top
            }, 1000);
        }
        e.preventDefault();
    });

});