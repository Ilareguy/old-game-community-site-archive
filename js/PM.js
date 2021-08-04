$(function(){
    $('#deleteButton').button().click(function(){
        if(confirm('Delete this Private Message?\nThis action cannot be undone.')){
            window.location.href = "PM.php?delete=" + $('#IDPM').val();
        }
    });
    
    $('#trButton').button().click(function(){
        window.location.href = 'PM.php?compose=' + $('#IDPM').val() + '&TR=1';
    });
    
    $('#replyButton').button().click(function(){
        window.location.href = 'PM.php?compose=' + $('#IDPM').val();
    });
    
    $('.backButton').each(function(){
        $(this).button().click(function(){
            window.location.href = 'PM.php';
        });
    });
    
    $('#composePMButton').button().click(function(){
        window.location.href = "PM.php?compose=0";
    });
    
    var title = $('#titleInput');
    var message = $('#messageInput');
    var to = $('#toInput');
    
    $('#sendButton').button().click(function(){
        var ok = true;
        if(title.val() == ''){
            ok = false;
            alert('Title is missing');
        }else if(to.val() == ''){
            ok = false;
            alert('Recipient is missing');
        }else if(message.val() == ''){
            ok = false;
            alert('Message is missing');
        }
        
        if(ok)
            document.forms.sendPMForm.submit();
    });
});