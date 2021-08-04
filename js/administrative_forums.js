$(function(){
    $("#backButton").button();
    $("#backButton").click(function(){
        window.location = "administrative_forums.php";
    });
    
    $("#saveSettingsButton").button();
    $("#saveSettingsButton").click(function(){
        document.forumForm.submit();
    });
    
    $("#createButtonConfirm").button();
    $("#createButtonConfirm").click(function(){
        document.createForm.submit();
    });
    
    $("#createButton").button();
    $("#createButton").click(function(){
        $("#addForumSectionDialog").dialog("open");
    });
    
    $("#deleteButton").button();
    $("#deleteButton").click(function(){
        if(confirm("Do you really want to remove this section?\nAll of its content will be deleted."))
            window.location = "administrative_forums.php?d=" + document.getElementById("id").value;
    });
    
    $("#addForumSectionDialog").dialog({
        modal: true,
        title: "Add a section",
        resizable: false,
        width: 400,
        height: 500,
        draggable: false,
        autoOpen: false
    });
});
