let ServerUrl = "https://orange-jaguar-922960.hostingersite.com/peso-quanta/server/";

SetHeader(document.querySelector(".-script__header"));
SetButtons(document.querySelector(".-script__buttons"));

function SetCookie(cname, cvalue, exseconds) {
    let d = new Date();
    d.setTime(d.getTime() + (exseconds * 1000));
    let expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function GetCookie(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');

    for(let i = 0; i <ca.length; i++) {
        let c = ca[i];

        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }

        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }

    return "";
}

function DeleteCookie(cname) {
    document.cookie = cname + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/;";
}

function BtnHome(element) {
    location.href = "home/";
}

function BtnCamera(element) {
    location.href = "camera/";
}

function BtnHistory(element) {
    location.href = "history/";
}

function BtnMenu(element) {
    location.href = "menu/";
}

function SetHeader(element) {
    if (element == null) {
        return;
    }

    element.innerHTML = /* html */`
        <div class="-script__header__logo">
            <img src="assets/logo.png">
        </div>
        <div></div>
        <div class="-script__header__menu -center__flex" onclick="BtnMenu(this)">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M3 4H21V6H3V4ZM3 11H21V13H3V11ZM3 18H21V20H3V18Z"></path></svg>
        </div>
    `;
}

function SetButtons(element) {
    if (element == null) {
        return;
    }

    element.innerHTML = /* html */`
        <div class="-script__buttons__home -center__flex" onclick="BtnHome(this)">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M19 21H5C4.44772 21 4 20.5523 4 20V11L1 11L11.3273 1.6115C11.7087 1.26475 12.2913 1.26475 12.6727 1.6115L23 11L20 11V20C20 20.5523 19.5523 21 19 21ZM13 19H18V9.15745L12 3.7029L6 9.15745V19H11V13H13V19Z"></path></svg>
        </div>
        <div class="-script__buttons__camera -center__flex" onclick="BtnCamera(this)">
            <img src="assets/camera.png">
        </div>
        <div class="-script__buttons__history -center__flex" onclick="BtnHistory(this)">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M19 22H5C3.34315 22 2 20.6569 2 19V3C2 2.44772 2.44772 2 3 2H17C17.5523 2 18 2.44772 18 3V15H22V19C22 20.6569 20.6569 22 19 22ZM18 17V19C18 19.5523 18.4477 20 19 20C19.5523 20 20 19.5523 20 19V17H18ZM16 20V4H4V19C4 19.5523 4.44772 20 5 20H16ZM6 7H14V9H6V7ZM6 11H14V13H6V11ZM6 15H11V17H6V15Z"></path></svg>
        </div>
    `;
}