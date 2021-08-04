$(function(){
    $("#backButton").button();
    $("#backButton").click(function (){
        window.location.href="administrative_news.php";
    });
    $("#addNewButton").button();
    $("#addNewButton").click(function (){
        window.location.href="administrative_news.php?add=1";
    });
    $("#saveEditNewButton").button();
    $("#saveEditNewButton").click(function (){
        document.editNewForm.submit();
    });
    $("#saveNewButton").button();
    $("#saveNewButton").click(function (){
        document.addNewForm.submit();
    });
    $("#deleteNewButton").button();
    $("#deleteNewButton").click(function (){
        if(confirm("Do you really wish to remove this new?"))
            window.location.href="administrative_news.php?d=" + $("#idNew").val();
    });
});