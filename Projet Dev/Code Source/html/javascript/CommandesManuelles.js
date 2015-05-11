$(document).ready(function(data) {
      $.ajax({
            url: "php/getStatusModule.php",
            async : false, 
            type: "POST",
            success: 
            function(data) {
              $('#listCommand').append(data).trigger('create') ;
              $("#listCommand").trigger('create');
            },
      });
      $.post( "php/setValueInSession.php", { isInManualMode: "yes" } );
});



$(document.body).on('change',function(event) {
    id = event.target.id ;
    type = $("#"+id).get(0).tagName ;
    state = $("#"+id).val();
    idModule = $("#"+id).data("options").idModule;
    idBroche = $("#"+id).data("options").idBroche;

    $.ajax({
         url: "php/setValueInTable.php",
         async : true, 
         type: "POST",
         data: 'table='+"ValeurBroche"+'&idModule='+idModule+'&idBroche='+idBroche+'&attribute='+"saValeur"+'&value='+state,
         success: 
         function(data) {
         },
    });    
});

$('#AddInterrupt').click(function(){
   window.location.href='CreateInterrupt.html';
})

$('#DeleteInterrupt').click(function(){
   window.location.href='DeleteInterrupt.html';
})

$('#Accueil').click(function(){
    $.post( "php/setValueInSession.php", { isInManualMode : "no" } );
    window.location.href='index.html';
})

$(window).unload(function(){
  $.post( "php/setValueInSession.php", { isInManualMode : "no" } );
})


