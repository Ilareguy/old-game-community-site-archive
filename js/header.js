$(function(){
    $( "#specialPagesBox" ).dialog({
        height: ((55 * Math.ceil($('#specialPagesBox div[href]').length)) + 110),
        width: 300,
        modal: true,
        resizable: false,
        draggable: true,
        title: "Member Features",
        autoOpen: false
    });
    
    $("#specialPagesBox_open").click(function(ev){
        ev.preventDefault();
        $( "#specialPagesBox" ).dialog("open");
    });
    
    $('li.mustBlink').each(function(){
        header_blinkElement($(this).attr('id'));
    });
    
    $('#specialPagesBox div[href]').button().click(function(){
        window.location.href = $(this).attr('href');
    });
});

function header_blinkElement(IDElement){
    setInterval("_header_blink($('#" + IDElement + "'))", 700);
}

function _header_blink(el){
    el.toggleClass('active');
}