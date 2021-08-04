$(function(){
    $('#logoutButton').show().button().click(function(){
        window.location.href = "login.php?logout=1";
    });
});