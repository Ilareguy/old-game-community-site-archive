$(function(){
    $('.joinGroupLink[idGroupe]').each(function(){
        $(this).click(function(ev){
            ev.preventDefault();
            if(confirm('Are you sure you want to join this group?')){
                $.ajax({
                    cache : false,
                    type : "POST",
                    url : "ajax/joinGroup.php",
                    data : {
                        id : $(this).attr('idGroupe')
                    },
                    success : function(data){
                        if(data == '0'){
                            $('.joinGroupLink').each(function(){
                                $(this).remove();
                            });
                            showMessage('success', "You joined this group!<br />You might have to reload the page so the changes take place");
                        }else{
                            showMessage('error', 'Unable to join this group');
                        }
                    },
                    error : function(){
                        showMessage('error', 'An error occured while contacting the server');
                    }
                });
            }
        });
    });
});