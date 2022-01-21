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
    jQuery( "#targetup" ).click(function() {
      voteUpDown("up");
    });
    jQuery( "#targetdown" ).click(function() {
      voteUpDown("down");
    });
    const url = "https://api.ipify.org/?format=json"
    fetch(url)
    .then(response => response.json())
    .then(data => {
      var keyVals = {};
      keyVals['ipaddress'] = data.ip;
      keyVals['nodeid'] = jQuery("#nodeid").val();
      jQuery.ajax({
          type: 'POST',
          url: ajaxApiUrlOrigin+"/mapi/totalvotecount.json",
          data: JSON.stringify(keyVals),
          contentType:"application/json; charset=utf-8",
          dataType: "json",
          beforeSend: function() {
             //jQuery('.loader').show();
          },
          complete: function(){
             //jQuery('.loader').hide();
          },
          success: function(result){      
            if(parseInt(result.head.statusCode)==200){
              //location.href = ajaxApiUrlOrigin;
              jQuery('span#totalvotes').html(result.head.totalvotes);
              if(result.head.votecount==1){
                jQuery('#targetup').addClass('disabled');
                jQuery('#targetdown').removeClass('disabled');
              } else if(result.head.votecount==-1){
                jQuery('#targetdown').addClass('disabled');
                jQuery('#targetup').removeClass('disabled');
              }
            } 
          }
        });
    });
 });

  function voteUpDown(call){
    if(call=="up"){
      if (jQuery('#targetup').hasClass('disabled')) return;
    } else if(call=="down"){
      if (jQuery('#targetdown').hasClass('disabled')) return;
    }
    const url = "https://api.ipify.org/?format=json"
  fetch(url)
    .then(response => response.json())
    .then(data => {
      document.getElementById("ipaddress").value = data.ip;
      var keyVals = {};
      keyVals['ipaddress'] = jQuery("#ipaddress").val();
      keyVals['nodeid'] = parseInt(jQuery("#nodeid").val());
      keyVals['vote'] = call;
      jQuery.ajax({
          type: 'POST',
          url: ajaxApiUrlOrigin+"/mapi/votingupdown.json",
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
            if(parseInt(result.head.statusCode)==200){
              if(result.head.votecount==1){
                jQuery('#targetup').addClass('disabled');
                jQuery('#targetdown').removeClass('disabled');
                jQuery('#totalvotes').html(result.head.totalcount);
              } else if(result.head.votecount==-1){
                jQuery('#targetdown').addClass('disabled');
                jQuery('#targetup').removeClass('disabled');
                jQuery('#totalvotes').html(result.head.totalcount);
              }
              
            } 
          }
        });
    });
  }