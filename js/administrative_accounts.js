$(function(){
    $('#backButton').button().click(function(){
        window.location.href = 'administrative_accounts.php';
    });
    $('#saveButton').button().click(function(){
        document.forms['accountForm'].submit();
    });
    $('#AddToWhitelistButton[username]').button().click(function(){
        var username = $(this).attr('username');
        $(this).button('disable');
        SendCommandToServer('whitelist add ' + username, function(){
            $('#AddToWhitelistButton[username]').html("<span class=\"ui-button-text\">" + username + " has been white-listed</span>");
        });
    });
});