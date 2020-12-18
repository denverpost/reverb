jQuery(function($) {
    //create swipe galleries
    var mySwiper = new Swiper ('.swiper-container', {
        // Optional parameters
        direction: 'horizontal',
        loop: true,
        preventInteractionOnTransition: true,
        // Navigation arrows
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },

    })
    //used for the in and outs of the flags
    AOS.init({
        duration: 400,
        easing: 'ease-in-out-back'
    });

    //submit a city modal popup
    $( ".modaltrigger, .modaltriggerMobile" ).on( "click", function() {
        $(".modalSubmitCity").css("display","block");
        $(".modalSubmitCity").removeClass("scale-out-vertical");
        $(".modalSubmitCity").addClass("scale-in-ver-center");
    });
    $( ".md-close" ).on( "click", function() {
        $(".modalSubmitCity").removeClass("scale-in-ver-center");
        $(".modalSubmitCity").addClass("scale-out-vertical");
    });

    //toggle function for alternatives
    $(".qt_alternativeTitle").on('click' ,function() {
        $(this).siblings().fadeToggle( "fast");
        $(this).children('.qa_alternativeTitleText').toggleClass("arrowUp");
        var myAltSwiper = new Swiper ('.swiper-alt-container', {
            // Optional parameters
            direction: 'horizontal',
            loop: true,
            preventInteractionOnTransition: true,
            // Navigation arrows
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },

        })
     });
});

//city submit button
var submitCity = document.getElementById('citySubmit');
//listen for click and send to ajax function
submitCity.addEventListener("click", sendEmail, false);
//comment submit button
var submitComment = document.getElementById('commentSubmit');
//listen for click and send to ajax function
submitComment.addEventListener("click", sendEmail, false);

//setting globals for the handleEvents function to know what form to manipulate
var whatForm = '';

function handleEvent(e) {
    console.log( e.type +'--'+ e.loaded +' bytes transferred\n' );
    if (e.type === "loadend"){
        if (whatForm === "city") {
            //manipulate city modal
            var closeModal = document.getElementById("modalSubmitCity");
            var cityMessage = document.getElementById("cityMessage");
            if (this.responseText === "true") {
                cityMessage.innerHTML = "<span style='color:green;font-weight:bold;font-size:30px;'>Got it! Thanks!</span>";
                setTimeout(function() {
                    closeModal.classList.remove("scale-in-ver-center");
                    closeModal.classList.add("scale-out-vertical");
                    cityMessage.innerHTML = "";
                    var getCity = document.getElementById('getCity');
                    getCity.value = "";
                }, 1500);
            } else {
                cityMessage.innerHTML = "<span style='color:red'>Whoops, something went wrong.<br/>Try again, or shoot us an email.</span>";
                setTimeout(function() {
                    closeModal.classList.remove("scale-in-ver-center");
                    closeModal.classList.add("scale-out-vertical");
                    cityMessage.innerHTML = "";
                }, 2500);
            }
        } else {
            //manipulate comment form
            var hideComment = document.getElementById("hideForm");
            var cityMessage = document.getElementById("commentMessage");
            if (this.responseText === "true") {
                cityMessage.innerHTML = "<span style='color:green;font-weight:bold;font-size:30px;'>Thanks for the feedback!</span>";
                hideComment.style.display = "none";
            } else {
                cityMessage.innerHTML = "<span style='color:red;font-weight:bold;font-size:14px;'>Whoops, something went wrong.<br/>Try again, or shoot us an email.</span>";
            }
        }
    }

}

function addListeners(xhr) {
    xhr.addEventListener('loadstart', handleEvent);
    xhr.addEventListener('load', handleEvent);
    xhr.addEventListener('loadend', handleEvent);
    xhr.addEventListener('progress', handleEvent);
    xhr.addEventListener('error', handleEvent);
    xhr.addEventListener('abort', handleEvent);
}

//send email from either quick trip form
function sendEmail(event) {
    event.preventDefault();
    var XHR = new XMLHttpRequest();
    var clickId = event.target.id;

    //get form data
    //get email from input
    var getCity = document.getElementById('getCity');
    var city = getCity.value;

    //comment form data
    var getEmail = document.getElementById('getEmail');
    var email = getEmail.value;
    var getName = document.getElementById('getName');
    var name = getName.value;
    var getComment = document.getElementById('getComment');
    var comment = getComment.value;
    comment = comment.replace(/(<([^>]+)>)/ig,"");



    var getStory = document.getElementById('reading');
    var story = getStory.value;
    var commentErrors = document.getElementById("commentErrors");
    addListeners(XHR, city);

    //set POST params
    var send_data = "";

    if (clickId === "citySubmit") {
        if (city) {
            whatForm = "city";
            //create city send data for xhr request
            send_data = "type=city&reading="+story+"&city="+city;
        } else {
            var cityMessage = document.getElementById("cityMessage");
            cityMessage.innerHTML = "<span style='color:red;font-size:20px;'>Enter a city!</span>";
        }

    }
    if (clickId === "commentSubmit") {
        //create comment send data for xhr request
        whatForm = "comment";
        validationResult = validateEmail(email);
        updateErrorMessage(validationResult);
        //console.log("is it a valid email?"+validationResult.valid);
        if (true === validationResult.valid) {
            send_data = "type=comment&reading="+story+"&name="+name+"&email="+email+"&comment="+comment;
        }
    }

    // Set up our request
    //source at 1mamp/php-email/quicktrip.php
    XHR.open('POST', 'https://plus.denverpost.com/mailer_v2/quicktrip/quicktrip.php');

    // Add the required HTTP header for form data POST requests
    XHR.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    // Finally, send our data.
    if (send_data) {
        XHR.send(send_data);
    } else {
        console.log("no form data to send");
    }

}

function validateEmail(email) {
    var result,
        emailPattern = new RegExp(
            /^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$/i
        );
    if (!email) {
        return {
            valid: false,
            message: "Please, enter an email."
        }
    }
    if (!emailPattern.test(email)) {
        return {
            valid: false,
            message: "Please, enter a valid email."
        }
    }
    //prevent double submits and show loading text
    jQuery(".keyweeAllInOne .submit").text("submiting...");
    return {
        valid: true,
        message: ""
    }
}

function errorMessageHandler(message, isShow) {
    if(isShow){
        commentErrors.innerHTML = message;
    }
    else {
        commentErrors.innerHTML = "";
    }
}

function updateErrorMessage(data) {
    if (!data.valid) {
        errorMessageHandler(data.message, true);
    } else {
        errorMessageHandler("", false);
    }
}
