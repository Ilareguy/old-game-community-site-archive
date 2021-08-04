<?php
if(!defined('__ROOT__'))
    define('__ROOT__', dirname(dirname(__FILE__)) . '/');
    
session_start();
require_once(__ROOT__.'includes/droits.php');

if(isset($_SESSION['id']) && (droit($_SESSION['id'], 'gameServerCommand') || droit($_SESSION['id'], 'whitelistGameServerCommand'))){
    ?>
    function SendCommandToServer(Command, SuccesHandle){
        $.ajax({
            cache: false,
            url: 'ajax/SendServerCommand.php',
            type: 'GET',
            data: {
                command: Command
            },
            success: SuccesHandle
        });
    }
    
    $(function(){
    
        $('div.items_menu a[data-send-server-command]').each(function(){
            var $this = $(this);
            var Icon = $this.find('img.icon');
            var Command = $this.attr('data-send-server-command');
            var SuccessMessage = $this.attr('data-success-message');
            
            $this.click(function(ev){
                ev.preventDefault();
                var OriginalIconSrc = Icon.attr('src');
                Icon.attr('src', 'images/icons/Clock.png');
                SendCommandToServer(Command, function(){
                    Icon.attr('src', OriginalIconSrc);
                    if(SuccessMessage)
                        showMessage("information", SuccessMessage);
                });
            });
        });
        
        $('[data-server-command]').button().each(function(){
            var $this = $(this);
            $this.click(function(){
                $this.button('disable');
                SendCommandToServer($this.attr('data-server-command'),
                function(){
                    $this.button('enable');
                });
            });
        });
        
    });
    <?php
}
?>