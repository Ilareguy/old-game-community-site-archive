$(function(){
    
    $('a[href="#"]').click(function(ev){
        ev.preventDefault();
    });
    
    // Minitip
    $('[minitip]').each(function(){
        var title = "";
        var $this = $(this);
        title = $this.attr('minitiptitle');
        if(title == "")
            title = $this.attr('title');
        var anchor = $this.attr('minitipanchor');
        if(!anchor)
            anchor = 'e';
        var delay = $this.attr('minitipdelay');
        if(!delay)
            delay = 100;
        $this.miniTip({
            content: $this.attr('minitip'),
            title: title,
            anchor: anchor,
            delay: delay
        });
    });
    
    $(".colorPicker").each(function(){
        var $this = $(this);
        $this.ColorPicker({
            onChange: function(hsb, hex, rgb) {
                $this.val('#' + hex);
            }
        });
    });
    
    var AjaxGameServerInfo = $('.AjaxGameServerInfo');
    if(AjaxGameServerInfo.length > 0){
        
        $.ajax({
            cache: false,
            url: 'ajax/GameServerInfo.php',
            type: 'GET',
            success: function(data){
                
                AjaxGameServerInfo.each(function(){
                    $(this).html(data);
                });
                
            }
        });
        
    }
	
	// OverBox
	$('[data-overbox-target]').each(function(){
		var $this = $(this);
		var context = $('#' + $this.attr('data-overbox-target'));
		context.html();
		var anchor = $this.attr('data-overbox-anchor');
		if(!anchor || anchor == '')
			anchor = 't';
		var width = parseInt($this.attr('data-overbox-width'));
		if(!width || width == 0)
			width = 300;
		var height = parseInt($this.attr('data-overbox-height'));
		if(!height || height == 0)
			height = 200;
		
		$this.OverBox({
			context: context,
			anchor: anchor,
			width: width,
			height: height,
			activation : 'click',
			showms: 100,
			hidems: 100
		});
	});
    
});

function showMessage(type, message){
    /*
    * Affiche un message sur la page.
    * Le 'type' peut prendre quatre valeurs différentes:
    * 1 - 'information';
    * 2 - 'warning';
    * 3 - 'error';
    * 4 - 'success'
    */
    var messages = $('#messageBoxes');
    var newMessage = $('<div></div>').html("<p>" + message + "</p>");
    newMessage.hide();
    switch(type){
        case 'success':
            newMessage.addClass('successBox');
            messages.append(newMessage);
            newMessage.show('fade', 500).delay(5000).hide('fade');
            break;
        case 'error':
            newMessage.addClass('errorBox');
            messages.append(newMessage);
            newMessage.show('fade', 500).delay(5000).hide('fade');
            break;
        case 'information':
            newMessage.addClass('informationBox');
            messages.append(newMessage);
            newMessage.show('fade', 500).delay(5000).hide('fade');
            break;
        case 'warning':
            newMessage.addClass('warningBox');
            messages.append(newMessage);
            newMessage.show('fade', 500).delay(5000).hide('fade');
            break;
    }
}

function ExecuteNotificationAction(ParamStr){
	$.ajax({
		url: 'ajax/NotificationAction.php?' + ParamStr,
		type: 'GET'
	}).done(function(data){
		//alert(data);
	});
}

function RemoveNotification(notifElement){
	var Activator = $(notifElement[0]);
	var Content = $('#' + Activator.attr('data-overbox-target'));
	try{
		Activator.OverBox('hide');
		Activator.slideUp(200, function(){
			$(this).remove();
		});
	}catch(err){alert(err);}
}

function FB_GO(){
    /*
     * Facebook ready.
     * All JavaScript related to Facebook should go there since
     * not everyone wants to link his account to Facebook.
     */
    
    $('[data-post-facebook][data-post-caption][data-post-name]').click(function(ev){
        
        /*
         * Share a link on Facebook.
         */
        
        var $this = $(this);
        var Caption = $this.attr('data-post-caption');
        var Name = $this.attr('data-post-name');
        
        var Link = $this.attr('data-post-link');
        if(!Link || Link == undefined || Link == "")
            Link = "http://www.diamondcraft.org/";
            
        var Picture = $this.attr('data-post-picture');
        if(!Picture || Picture == undefined || Picture == "")
            Picture = "http://www.diamondcraft.org/images/dc128.png";
         
        var Description = $this.attr('data-post-description');
        if(!Description || Description == undefined || Description == "")
            Description = "Diamond Craft";
        
        FB.ui({
            method: 'feed',
            name: Name,
            caption: Caption,
            description: (Description),
            link: Link,
            picture: Picture
        },
        function(response) {
            if (response && response.post_id) {
            } else {
            }
        }
        );
        
    });
    
}