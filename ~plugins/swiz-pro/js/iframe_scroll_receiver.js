var eventMethod = window.addEventListener? "addEventListener": "attachEvent";
var eventer = window[eventMethod];
var messageEvent = eventMethod === "attachEvent"? "onmessage": "message";
eventer(messageEvent, function (e) {
    if (e.data === "iframetopscroll" || e.message === "iframetopscroll"){
        jQuery("html, body").animate({
            scrollTop: jQuery('#swizProQuizIframe').offset().top
        }, 300);
    }
});
