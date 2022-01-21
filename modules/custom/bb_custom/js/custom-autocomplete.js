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
    var options = {

        url: function(phrase) {
          return ajaxApiUrlOrigin+"/mapi/searchautocomplete.json";
        },

        getValue: function(element) {
          return element.name;
        },

        list: {

          onClickEvent: function() {
            var value = jQuery("#easyautocompletesearch").getSelectedItemData().path;
            //alert(value);
            window.location.href = ajaxApiUrlOrigin+value;
          }
        },

        ajaxSettings: {
          dataType: "json",
          method: "POST",
          data: {}
        },

        preparePostData: function(data) {
          data.phrase = jQuery("#easyautocompletesearch").val();
          return data;
        },

        requestDelay: 400
      };

    jQuery("#easyautocompletesearch").easyAutocomplete(options);
 });