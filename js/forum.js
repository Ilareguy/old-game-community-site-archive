$(function(){
    $("#createThreadDialog").dialog({
        modal: true,
        resizable: false,
        draggable: false,
        title: "Create a new thread",
        width: 520,
        height: 510,
        autoOpen: false
    });
    $("#BBcodesInfos").dialog({
        modal: false,
        title: "BBcodes",
        resizable: false,
        width: 500,
        height: 420,
        autoOpen: false
    });
    $("#smileysInfos").dialog({
        modal: false,
        title: "Smileys",
        resizable: false,
        width: 300,
        height: 400,
        autoOpen: false
    });
    $("#createThreadButton").button();
    $("#createThreadButton").click(function (){
        document.getElementById("createThreadForm").submit();
    });
    $("#postAnswerButton").button();
    $("#postAnswerButton").click(function (){
        document.getElementById("postAnswerForm").submit();
    });
    $("#postAnswerSectionButton").button();
    $("#postAnswerSectionButton").click(function (){
        if(document.getElementById("postAnswerSectionVisible").value == 1){
            $("#postAnswerSection").hide( "blind", {}, 400 );
            document.getElementById("postAnswerSectionVisible").value = 0;
        }else{
            $("#postAnswerSection").show( "blind", {}, 400 );
            document.getElementById("postAnswerSectionVisible").value = 1;
        }
        return false;
    });
    $("#BBcodesInfosButton").button();
    $("#BBcodesInfosButton").click(function (){
        $("#BBcodesInfos").dialog("open");
    });
    $("#smileysInfosButton").button();
    $("#smileysInfosButton").click(function (){
        $("#smileysInfos").dialog("open");
    });
    
    $("#messageEditPopup").dialog({
        modal: true,
        width: 420,
        height: 410,
        draggable: false,
        resizable: false,
        autoOpen: false,
        title: "Edit this message"
    });
    $("#messageEditPopup_saveButton").button();
    $("#messageEditPopup_saveButton").click(function (){
        $.ajax({
            url: 'subPages/forum/moderatorEditMessage.php',
            data: {
                thread : document.getElementById("moderatorsEditMessageForm_idThread").value,
                message : document.getElementById("moderatorsEditMessageForm_idMessage").value,
                newMessage : document.getElementById("moderatorsEditMessageForm_message").value,
                saveMessage : "1"
            },
            type: "POST",
            success: function(data){
               window.location.reload();
            }
        });
    });
});

function editIdForumForm(newVal){
    document.getElementById("idForum").value = newVal;
    $("#createThreadDialog").dialog("open");
}

function openEditMessageDialog(idMessage, idThread){
    $.ajax({
        url: 'subPages/forum/moderatorEditMessage.php',
        data: {
            thread : idThread,
            message : idMessage
        },
        type: "POST",
        success: function(data){
            $("#messageEditPopup_content").html(data);
            $("#messageEditPopup").dialog("open");
        }
    });
}

function removeEntireThread(thread){
    if(confirm("Do you really wish to remove this thread?"))
        window.location.href = "forum.php?r_all=" + thread;
}

function removeMessage(message, thread){
    if(confirm("Do you really wish to remove this message?"))
        window.location.href = "forum.php?r=" + message + "&t=" + thread;
}