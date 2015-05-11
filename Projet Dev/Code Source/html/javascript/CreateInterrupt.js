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
		data: 'idModule='+idModule+'&disabledForAlreadyTaken='+"yes",
		success: 
		function(data) {
                  $('#listBroche').empty();
                  $('#listBroche').append("<option value=\"\">Please select a pin...</option>");
                  $('#listBroche').append(data) ;
      	},
      });
});

$('#listBroche').change(function(){
      idBroche = $("#listBroche").val();
});

$('#typeBroche').change(function(){
      idModule = $('#listModule').val() ;
      Module = $("#listModule option[value='"+idModule+"']").text();
      typeBroche = $('#typeBroche').val();

      $('#selectionModuleMeteo').empty();
      $('#selectionModuleMeteo').append("<option value=\"\">Aucun</option>");

      if (typeBroche == "INTERRUPTEUR" && (Module.match("COMMANDE") != null)) {

            $.ajax({
            url: "php/getListModule.php",
            async : true, 
            type: "POST",
            data: 'typeModule='+"METEO",
            success: 
            function(data) {
                  $('#selectionModuleMeteo').append(data) ;
            },
      });
            
      }
});

$('#submit').click(function() {

      idModule = $('#listModule').val();
      idBroche = $('#listBroche').val();
      typeBroche = $('#typeBroche').val();
      sonModuleMeteo = $('#selectionModuleMeteo').val();
      descriptionBroche = $('#saDescription').val();

      if ((idModule != "") && (idBroche != "") && (typeBroche != "" ) && (descriptionBroche != "")) {

            $.ajax({
            url: "php/addBrocheInModule.php",
            async : true, 
            type: "POST",
            data: 'idModule='+idModule+'&idBroche='+idBroche+'&typeBroche='+typeBroche+'&sonModuleMeteo='+sonModuleMeteo+'&descriptionBroche='+descriptionBroche,
            });
            window.location = "CommandesManuelles.html";
            alert("Rows Added succesfully !");
      }
      else {
            alert("Veuillez remplir tous les champs !!");
      }
});

$('#Retour').click(function(){
   window.location ='CommandesManuelles.html';
})
