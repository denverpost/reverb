/**
 * Created with JetBrains PhpStorm.
 * User: webonise
 * Date: 8/1/18
 * Time: 5:24 PM
 * To change this template use File | Settings | File Templates.
 */
/*Add Js script and function call when the iframe is loaded
*/
var iFrame = document.getElementById("SwizproIframe");
var quizId =  iFrame.getAttribute("data-name-quizid");
var url =  iFrame.getAttribute("data-name-url");
var iFrameSrc = url + '/?wp-pro-quiz=' + quizId + '&parentLocationURL=' + window.parent.location.href;


if (!checkUndefinedInput(quizId) && quizId != '') {
    document.write('<scri'+'pt type="text/javascript" src="' + url + '/wp-content/plugins/swiz-pro/js/iframeResizer.min.js"></scr'+'ipt>');
    document.write('<iframe id="swizProQuizIframe" src="' + iFrameSrc + '" width="100%" height="150" frameborder="0" scrolling="no"></iframe>');
    document.write('<scri'+'pt>iFrameResize();</scr'+'ipt>');
    document.write('<scri'+'pt type="text/javascript" src="' + url + '/wp-content/plugins/swiz-pro/js/iframe_scroll_receiver.js"></scr'+'ipt>');
}else{
    document.write('Quiz not found.');
}

/* Check whether input is defined or undefined
 *   return bool
 */
function checkUndefinedInput(input){
    return typeof input == 'undefined';
}
