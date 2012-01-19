// ----------
// Safe console
// ----------
if (typeof console === 'undefined') console = {};
if (typeof console.log === 'undefined') console.log = function(){};


// ----------
// lignited definitions
// ----------
var lignited = lignited || {};


//----------
// Master init
//----------
lignited.init = function() {
    $(document).trigger("lignited.onDocumentLoad");
};


//----------
// Custom Event Listeners
//----------
$(document).bind("lignited.onDocumentLoad", function() {
    console.log("----- application init -----");
    lignited.flash.load();
});


//----------
// Boot up
//----------
$(document).ready(function() {
    lignited.init();
});