function CheckComNumFree(id){
    jQuery.ajax({
        type: "GET",
        url: "https://graph.facebook.com/"+id+"/comments?limit=5000&fields=id",
        async: true,
        success: function(resp) {
            
            num = resp.data.length;
            jQuery("#countcomm_"+id).html('['+num+']');
        }
    });
}
jQuery("document").ready(function() {
    jQuery( ".hideme" ).hide();
});

function ShowHideRows(){
    jQuery( ".hideme" ).toggle();
}