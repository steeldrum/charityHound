/**
mailme.jquery.js 
tjs 091009
file version 1.01 
release version 1.10
*/
/**
Based on this article:      
    http://www.html-advisor.com/javascript/hide-email-with-javascript-jquery/

    Example markup:
    ---------------
    
    <span class="mailme" title="Send me a letter!">me at mydomain dot com</span>

    Example code:
	-------------
	
	// Replaces all the matching elements with a <a href="mailto:..> tag.
		
	$('span.mailme').mailme();
*/
(function($) {
$.fn.mailme = function() {
    var at = / at /;
    var dot = / dot /g;
	//alert("mailme at " + at + " dot " + dot);
    this.each( function() {
        var addr = jQuery(this).text().replace(at,"@").replace(dot,".");
        //tjs 101018 temp hack
        //var addrHack = 'tandmsoucy@verizon.net';
        var addrHack = 'collogistics.collaborator@verizon.net';
        var title = jQuery(this).attr('title');
	//alert("mailme addr " + addr + " title " + title);
/*        
        $(this)
            .after('<a href="mailto:'+addr+'" title="'+title+'">'+ addr +'</a>')
            .remove();
*/
        $(this)
            .after('<a href="mailto:'+addrHack+'" title="'+title+'">'+ addr +'</a>')
            .remove();
            
    });
};
})(jQuery);