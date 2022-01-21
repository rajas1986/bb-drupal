 var hostOrigin = document.location.origin;
 var localhost = true;
 var development = false;
 var preproduction = false;
 var production = false;
 var ajaxApiUrlOrigin = document.location.origin;
 if(production===true){
   ajaxApiUrlOrigin = document.location.origin;
 } else if(preproduction===true){
   ajaxApiUrlOrigin = document.location.origin;
 } else if(development===true){
   ajaxApiUrlOrigin = document.location.origin;
 } else if(localhost===true){
   ajaxApiUrlOrigin = "http://localhost/test-drupal";
 }

 jQuery(document).ready(function(){
  const url = "https://api.ipify.org/?format=json"
  fetch(url)
    .then(response => response.json())
    .then(data => {
      document.getElementById("ipaddress").value = data.ip;
      var keyVals = {};
      keyVals['ipaddress'] = jQuery("#ipaddress").val();
      keyVals['nodeid'] = jQuery("#nodeid").val();
      jQuery.ajax({
          type: 'POST',
          url: ajaxApiUrlOrigin+"/mapi/nodeviewcount.json",
          data: JSON.stringify(keyVals),
          contentType:"application/json; charset=utf-8",
          dataType: "json",
          beforeSend: function() {
             /*jQuery('.loader').show();*/
          },
          complete: function(){
             /*jQuery('.loader').hide();*/
          },
          success: function(result){      
            if(parseInt(result.head.statusCode)==0){
              //location.href = ajaxApiUrlOrigin;
              
            } 
          }
        });
    });
  

 });