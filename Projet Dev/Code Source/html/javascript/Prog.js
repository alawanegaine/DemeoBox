$(document).ready(function(data) {
      $.ajax({
            url: "php/getListModule.php",
            async : false,
            success: 
            function(data) {
                  $('#listModule').append(data) ;
            },
      });
});

$('#listModule').change(function(data) {
      idModule = $("#listModule").val();

      $.ajax({
        url: "php/getListBrocheForModule.php",
        async : true, 
        type: "POST",
        data: 'idModule='+idModule+'&disabledForAlreadyTaken='+'no',
        success: 
        function(data) {
                  $('#listBroche').empty();
                  $('#listBroche').append("<option value=\"\">Please select a pin...</option>");
                  $('#listBroche').append("<option value=\"ALL\">Toutes les broches</option>");
                  $('#listBroche').append(data) ;
        },
      });
});

$('#ValidationHumidite').change(function(){
      HumiditeOk = $("#ValidationHumidite").val();
     if (HumiditeOk == "on")
     {
     	alert("attention la durée d'arrosage ne sera pas pris en compte");
     }
});

$('#valider').click(function() { // attend la fin du chargement de la page
	var jours = [];
    idModule = $('#listModule').val();
    idBroche = $('#listBroche').val();
    HumiditeOk = $("#ValidationHumidite").val();
    TauxHumidite =$("#tauxHumidite").val();
    HeureArrosage =$("#HeureArrosage").val();
    DureeArrosage =$("#DureeArrosage").val();
    $.each($(".select option:selected"), function(){            
            jours.push($(this).val());
    });

    if ((jours != "") && (HeureArrosage != "") && (DureeArrosage != "") && (idModule != "") && (idBroche != "")) {
        if (HumiditeOk == "off")
        {
          TauxHumidite = "NULL" ;
        }

        $.ajax({
                url: "php/Prog.php", // appel la page php
                async : false, // attend la fin de l'execution pour continué
                type: "POST", // transparent pour l'utilisateur
                data: "idModule="+idModule+"&idBroche="+idBroche+"&JourArrosage="+jours+"&HumiditeOk="+HumiditeOk+"&TauxHumidite="+TauxHumidite+"&HeureArrosage="+HeureArrosage+"&DureeArrosage="+DureeArrosage, // on envoit au script php idModule=2
                success: 
                function(data) {
                  $('form #listCommand').append(data) ; // ça insert le contenu du script php dans la balise listCommande
                  alert("Rows updated successfuly !!") ;
                  window.location.href = 'index.html' ;
                },
        });
    }
    else 
    {
        alert("Veuillez renseigner tous les champs !!");
    }
});

$('#reset').click(function(){
   $.ajax({
      url: "php/deleteTableContent.php", // appel la page php
      async : true, // attend la fin de l'execution pour continué
      type: "POST", // transparent pour l'utilisateur
      data: "table="+"ProgrammationJour", // on envoit au script php idModule=2
      success: 
        alert("Rows deleted !"),
    });
})

$('#Accueil').click(function(){
   window.location.href='index.html';
})
