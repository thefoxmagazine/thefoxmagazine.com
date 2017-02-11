jQuery(document).ready(function() {

    var setting_substitute_player_setting_elem = jQuery('#setting_substitute_player_setting');
    var setting_enable_monetization_elem = jQuery('#setting_enable_monetization');

    var   setting_substitute_player_setting = setting_substitute_player_setting_elem.attr('checked') ? true: false;
    var   setting_enable_monetization = setting_enable_monetization_elem.attr('checked') ? true: false;
    var editable = false;


    jQuery('#disconnect-videe').click(function(){
        if(confirm('Are you sure you want to disconnect your Videe.tv account and stop monetizing videos on your website?')) {
            jQuery.post(location.href,{
                'action': 'enter-key',
                'key': '',
                'user': ''
            }, function(){
                location.reload()
            });
            return false;
        }

    });

    setting_enable_monetization_elem.change(function(){
        if (setting_enable_monetization_elem.is(':checked') == true &&
            setting_substitute_player_setting_elem.is(':checked') == false) {

            setting_substitute_player_setting_elem.prop('checked', true);
        }
    });

    setting_substitute_player_setting_elem.change(function(){
        if(setting_substitute_player_setting_elem.is(':checked') == false &&
            setting_enable_monetization_elem.is(':checked') == true){

            setting_enable_monetization_elem.prop('checked', false);
        }
    });

    jQuery( "form#settings" ).submit(function(event) {
        
        var   new_setting_substitute_player_setting = jQuery('#setting_substitute_player_setting').attr('checked') ? true: false;
        var   new_setting_enable_monetization = jQuery('#setting_enable_monetization').attr('checked') ? true: false;
        
        if((new_setting_enable_monetization === false && setting_enable_monetization === true)
           || (setting_substitute_player_setting === true && new_setting_substitute_player_setting === false 
           && setting_enable_monetization === true)) {
            var choice = confirm("This will disable ads for videos published from your media library.\n Are you sure you want to stop monetizing your content?");

            if(choice == false) {
                return false;
            }            
        }

        jQuery("#settings .spinner_update").show();        
    });


    jQuery( "a#editmail" ).click(function(event) {
        editable = !editable;
        
        if(editable === true) {
            jQuery("form#paymentinfo input").prop('disabled', false);
            jQuery('form#paymentinfo input[type="submit"]').prop('disabled', false);
        } else {
            jQuery("form#paymentinfo input").prop('disabled', true);
            jQuery('form#paymentinfo input[type="submit"]').prop('disabled', true);
        }       
        return false;  
    });


    jQuery( "form#paymentinfo" ).submit(function(event) {
        
        jQuery("#paymentinfo .spinner_update").show();        
    });
    
    
    
});
