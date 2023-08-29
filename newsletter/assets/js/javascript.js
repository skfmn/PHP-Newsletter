$(document).ready(function () {
    $(".iframe").fancybox();
    $(".picimg").fancybox({
        openEffect: "fade",
        closeEffect: "fade",
        openSpeed: "slow",
        closeSpeed: "slow"
    });
    $(".urlimg").fancybox({
        openEffect: "fade",
        closeEffect: "fade",
        openSpeed: "slow",
        closeSpeed: "slow",
        padding: 0,
        border: 1,
        margin: 10,
        autoDimensions: false,
        height: 'auto',
        width:'auto'
    });
    $("#textmsg").fancybox({padding:0,border:1,margin:10});
    $("#textmsg").trigger('click');
    $("#accordion").accordion({
        active: !1,
        collapsible: !0,
        heightStyle: "content",
        header: "h3",
        icons: {
            header: "fa fa-caret-right fa-fw",
            activeHeader: "fa fa-caret-down fa-fw"
        }
    })
});

function selectText(containerid) {
    if (document.selection) { // IE
        var range = document.body.createTextRange();
        range.moveToElementText(document.getElementById(containerid));
        range.select();

        //range.setSelectionRange(0, 99999);

        navigator.clipboard.writeText(range.value);

        alert("Copied the text: " + range.value);

    } else if (window.getSelection) {
        var range = document.createRange();
        range.selectNode(document.getElementById(containerid));
        window.getSelection().removeAllRanges();
        window.getSelection().addRange(range);

        navigator.clipboard.writeText(range.value);

        alert("Copied the text: " + range.value);
    }


}

var xmlhttp;
function selectAll(id) {
    var x = document.getElementById(id);
    $count = x.options.length;
    for (var i = 0; i < $count; i++) {
        x.options[i].selected = true;
    }
}

function loadTemplate(tid) {
    var inttid = tid;
    xmlhttp = null;
    if (window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest();
    } else if (window.ActiveXObject) {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    if (xmlhttp != null) {
        var url = 'loadtemplate.php?tid=' + inttid;
        xmlhttp.onreadystatechange = state_Change;
        xmlhttp.open("GET", url, true);
        xmlhttp.send(null);
    } else {
        alert("Your browser does not support XMLHTTP.");
    }
}

function state_Change() {
    if (xmlhttp.readyState == 4) {
        if (xmlhttp.status == 200) {
            var stuff = xmlhttp.responseText;
            var morestuff = stuff.split(",");
            document.getElementById('temptitle').value = morestuff[0];
            document.getElementById('tempdescr').value = morestuff[1].replace("~", ",");
            document.getElementById('tempid').value = morestuff[3];
            CKEDITOR.instances.tempbody.setData(morestuff[2].replace(/~/g, ","));
        } else {
            alert("Problem retrieving data:" + xmlhttp.statusText + " Status: " + xmlhttp.status);
        }
    }
}

function confirmSubmit(imsg, ihref) {
    var smsg = confirm(imsg);
    if (smsg == true) {
        window.location = ihref;
    } else {
        return false;
    }
}

function togglePass(arg1, arg2) {
    var x = document.getElementById(arg1);
    var y = document.getElementById(arg2);
    if (x.type === "password") {
        x.type = "text";
        y.className = "fa fa-eye shpwd";
    } else {
        x.type = "password";
        y.className = "fa fa-eye-slash shpwd";
    }
}