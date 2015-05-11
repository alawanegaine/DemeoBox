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
		data: 'idModule='+idModule+'&disabledForAlreadyTaken='+"no",
		success: 
		function(data) {
                  $('#listBroche').empty();
                  $('#listBroche').append("<option value=\"\">Please select a pin...</option>");
                  $('#listBroche').append(data) ;
      	},
      });
});

$('#submit').click(function() {

      idModule = $('#listModule').val();
      idBroche = $('#listBroche').val();
      brocheBusy = $("#listBroche option[value='"+idBroche+"']").text();

      	if ((idModule != "") && (idBroche != "")) {
      		if (brocheBusy.match("--") != "") {
	            $.ajax({
	            url: "php/deleteBrocheInModule.php",
	            async : true, 
	            type: "POST",
	            data: 'idModule='+idModule+'&idBroche='+idBroche,
	            });
	            window.location = "CommandesManuelles.html";
	            alert("Rows deleted succesfully !");
	        }
	        else
	        {
	        	alert("Veuillez choisir une broche occupée pour la supprimer !");
	        }
      }
      else {
            alert("Veuillez remplir tous les champs !!");
      }
});

$('#Retour').click(function(){
   window.location.href='CommandesManuelles.html';
})
