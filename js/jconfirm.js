(function( $ ) {
    
    /**
    * $.jconfirm("Titre", "Message", 
    * function(){
    *     // Action effectuée si l'utilisateur appuie sur "Confirmer"
    * },
    * function(){
    *     // Action effectuée si l'utilisateur appuie sur "Annuler"
    * });
    */

    $.jconfirm = function(title, message, acceptHandle, declineHandle) {
        var jconfirmDialog = $('#jconfirmDialog');
        
        // On vérifie si le dialog existe déjà
        if(jconfirmDialog.length == 0){
            // Il n'existe pas; on le crée
            jconfirmDialog = $('<div id="jconfirmDialog"></div>')
            .dialog({
                title: '',
                modal: true,
                autoOpen: false,
                resizable: false,
                closeOnEscape: false
            });
        }

        jconfirmDialog.dialog('option', 'title', title)
        .dialog('option', 'buttons', {
            'Confirm' : function(){
                $(this).dialog('close');
                acceptHandle();
            },
            'Cancel' : function(){
                $(this).dialog('close');
                declineHandle();
            }
        })
        .html(message)
        .dialog('open');
    };
    
})( jQuery );