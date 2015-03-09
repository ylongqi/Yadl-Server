var img;
var img_src;
var progress_index = 1;
var img_loaded = true;

var q1, q2;
var detail_state = 0;

var cl_id;

/*
function render() {

    var additionalParams = {
        'clientid': '937959719490-ppkjp7tmlc742ufk7ronkovk5ont4e2g.apps.googleusercontent.com',
        'scope': "https://www.googleapis.com/auth/plus.login",
        'cookiepolicy': 'single_host_origin',
        'callback': signinCallback,
        'requestvisibleactions': 'http://schema.org/AddAction',
        'theme': 'light',
        'width': 'wide',
        'height': 'tall'
    };

    gapi.signin.render('myButton', additionalParams);
}

function signinCallback(authResult) {

    if (authResult.status.signed_in) {
        var args = {
            path: "https://www.googleapis.com/plus/v1/people/me",
            callback: requestCallback
        };

        gapi.client.request(args);
    } else if (authResult.error === "user_signed_out") {
        user_id = "";
        $("#yadl").hide();
        $("#front").show();
    }
}

function requestCallback(jsonResp, rawResp) {

    if (jsonResp) {
        $("#front").slideUp();
        $("#yadl").slideDown();
        user_id = jsonResp.id;

        $.post("adl_backend/img_query.php", {
            client_id: cl_id,
            user_id: user_id
        }, update_pair);

        if (typeof jsonResp.name.givenName !== 'undefined') {
            $("#dispname").html(jsonResp.name.givenName);
        } else if (typeof jsonResp.displayName !== 'undefined') {
            $("#dispname").html(jsonResp.displayName);
        }

        $("#load_area").hide();
        $("#result").hide();
        $("#progresslist").html("");
    }

}
*/
function init_handle(data, status) {
    if(data === "true"){
        $("#front").slideUp();
        $("#yadl").slideDown();
        
        $.post("adl_backend/img_query.php", {
            client_id: cl_id
        }, update_pair);
        
        $("#load_area").hide();
        $("#result").hide();
        $("#progresslist").html("");
    }
}

function send_text() {

    var inputText = document.getElementById("inputactivity");
    var activity = inputText.value;
    inputText.value = "";
    if (activity != "") {
        $("#resultlist").append(getResultTextElement(activity));
    }
    $.post("adl_backend/text_store.php", {
        word: activity,
        client_id: cl_id
    });

}
function image_loaded() {
    $("#load_area").hide();
    $("#picture").parent().addClass("loaded");
    $("#progress_area").show();
    img_loaded = true;
}

function getProgressElement(elementID, imgSrc) {
    var progressImg = document.createElement("img");
    progressImg.setAttribute("class", "img-circle img-thumbnail progressbar");
    progressImg.setAttribute("id", "progress" + elementID.toString());
    progressImg.setAttribute("src", imgSrc);

    return progressImg;
}

function getProgressTextElement(elementID, centerText) {

    var divText = document.createElement("div");
    var pText = document.createElement("p");
    pText.setAttribute("class", "lead");
    pText.innerHTML = centerText;

    divText.setAttribute("id", "progress" + elementID.toString());
    divText.setAttribute("class", "progressbox progresstext");
    divText.appendChild(pText);

    return divText;

}

function getResultTextElement(centerText) {

    var divText = document.createElement("div");
    var pText = document.createElement("p");
    pText.setAttribute("class", "lead");
    pText.innerHTML = centerText;

    divText.setAttribute("class", "activitybox activitytext");
    divText.appendChild(pText);

    return divText;
}

function getResultImgElement(src) {
    var imgElement = document.createElement("img");
    imgElement.setAttribute("src", src);
    imgElement.setAttribute("class", "img-thumbnail activitybox");

    return imgElement;
}

function update_pair(data, status) {
    var JSONobj = JSON.parse(data);

    if (JSONobj.previous_image_src) {
        if(JSONobj.previous_image_src.indexOf("edu-cornell-cs-sdl-yadl") >= 0){
            $("#progresslist").append(getProgressElement(progress_index, JSONobj.previous_image_src));
        }else {
            $("#progresslist").append(getProgressTextElement(progress_index, JSONobj.previous_image_src));
        }
        
        progress_index = progress_index + 1;
    }

    if (JSONobj.image_number) {

        img = JSONobj.image_number;
        img_src = JSONobj.image_src;

        $("#currentIndex").html(img);
        $("#progress_area").hide();
        $("#load_area").show();

        if (img_src.indexOf("edu-cornell-cs-sdl-yadl") >= 0) {
            $(".choicebox").hide();
            $(".choice").show();
            if ($("#picture").parent().hasClass("loaded")) {
                $("#picture").parent().removeClass("loaded");
            }

            $("#picture").attr("src", JSONobj.image_src);
        } else {
            $(".choice").hide();
            $(".choicebox").show();
            $("#pictureText").html(img_src);
            $("#progress_area").show();
            $("#load_area").hide();
            img_loaded = true;
        }

    }

    if (JSONobj.total_image_number) {
        $("#totalNumber").html(JSONobj.total_image_number);
    }

    if (JSONobj.hard_list) {
        var hardList = JSONobj.hard_list;

        for (var i = 0; i < hardList.length; i++) {
            if(hardList[i].indexOf("edu-cornell-cs-sdl-yadl") >= 0){
                $("#resultlist").append(getResultImgElement(hardList[i]));
            } else{
                $("#resultlist").append(getResultTextElement(hardList[i]));
            }
        }

        $("#game").slideUp();
        $("#result").slideDown();
    }
}

/*
 * getMonth function returns 0~11
 * @returns {String}
 */
function two_digit(num){
    return num > 9 ? num: "0" + num;
}

function get_datetime() {
    var dayObject = new Date();
    var timezone;
    var offset;
    
    timezone = Math.abs(dayObject.getTimezoneOffset()) / 60;
    if(dayObject.getTimezoneOffset() >= 0){
        offset = "-";
    } else {
        offset = "+";
    }

    return dayObject.getFullYear() + "-" +
            (dayObject.getMonth() + 1) + "-" +
            dayObject.getDate() + " " +
            dayObject.getHours() + ":" +
            dayObject.getMinutes() + ":" +
            dayObject.getSeconds() + "." + 
            dayObject.getMilliseconds() + offset +
            two_digit(timezone) + ":00";
}

$(document).ready(function () {

    cl_id = Math.random();

    $.post("adl_backend/init.php", {
        client_id: cl_id
    }, init_handle);

    $("#signout").click(function () {
        $.get("adl_backend/logout.php", function(data){
            if(data === "success"){
                location.reload();
            }
        });
    });
    
    $("#ohmage").click(function(){
        $.get("adl_backend/auth_callback.php", function(data) {
            window.location.href = data;
        });
    });
    
    $("#finallogout").click(function(){
        $.get("adl_backend/logout.php", function(data){
            if(data === "success"){
                location.reload();
            }
        });
    });

    $(".easy").click(function () {
        if (img_loaded) {
            img_loaded = false;
            //console.log(get_datetime());
            $.post("adl_backend/img_query.php", {
                choice: "0",
                img: img,
                src: img_src,
                client_id: cl_id
            }, update_pair);
        }
    });

    $(".moderate").click(function () {
        if (img_loaded) {
            img_loaded = false;
            //console.log(get_datetime());
            $.post("adl_backend/img_query.php", {
                choice: "1",
                img: img,
                src: img_src,
                client_id: cl_id
            }, update_pair);
        }
    });

    $("#picture").load(image_loaded);

    $(".hard").click(function () {
        if (img_loaded) {
            img_loaded = false;
            //console.log(get_datetime());
            $.post("adl_backend/img_query.php", {
                choice: "2",
                img: img,
                src: img_src,
                client_id: cl_id
            }, update_pair);
        }
    });

    $("#add").click(send_text);

    $("#inputactivity").keypress(function (e) {
        if (e.which == 13 || e.keyCode == 13) {
            send_text();
        }
    });
});