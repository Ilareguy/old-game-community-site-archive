$(function(){
    $('#createGroupDialog').dialog({
        modal: true,
        width: 400,
        height: 360,
        title: "Create a Group",
        autoOpen: false,
        resizable: false
    });
        
    $('#creerGroupeButton').button().click(function(){
        // Créer un groupe
        $('#createGroupDialog').dialog('open');
    });
    
    $('#newGroup_createButton').button().click(function(){
        document.forms['newGroupForm'].submit();
    });
    
    $('#backButton').button().click(function(){
        window.location.href = "administrative_groupes.php";
    });
    
    $("#deleteButton[idGroupe]").button().click(function(){
        if(confirm("Are you sure you want to permanently delete this group?")){
            $.ajax({
                cache : false,
                url : 'ajax/deleteGroup.php',
                type : 'POST',
                data : {
                    id : $(this).attr('idGroupe')
                },
                success : function(data){
                    window.location.href = "administrative_groupes.php";
                },
                error : function(){
                    showMessage('error', 'Error while contacting the server');
                }
            });
        }
    });
    
    $('#saveButton').button().click(function(){
        $('#mainFrm').submit();
    });
});