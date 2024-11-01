jQuery(document).ready(function(){

    jQuery('.woo-bbh-datepicker').datepicker({ dateFormat: 'yy-mm-dd'
    
    });

    //jQuery('.woo-bbh-datepicker').datepicker('setDate', new Date()); 
         
});

jQuery(document).ready(function(){

    jQuery('.woo-bbh-datepicker-2').datepicker({ dateFormat: 'yy-mm-dd',        

    });
             
});


jQuery(document).ready(function($){
    $('.woo-datetime-bbh-reserve').each(function(){
        $(this).datetimepicker({
            timeFormat: "HH:mm",
            dateFormat : 'yy-mm-dd',
            minDate: 0
        });
    });
});