WikiBookmarks = {

1: function() {

(function(wgScript, page) {
var action = wgScript+'?title=Special:Bookmarks';
var contentWindow = getBrowser().contentWindow;
var contentDocument = getBrowser().contentDocument;
var p = contentWindow.location.protocol;
var bookmarkPage = wgScript+'?title='+encodeURIComponent(page);
if (p!='http:' && p!='https:' && p!='ftp:') {
    getBrowser().loadURI(bookmarkPage);
    return;
}
PopupNotifications.show(
    getBrowser().selectedBrowser, "wikibookmarks-popup",
    "Добавляю страницу в ВикиЗакладки...", null, null, null
);
var url = ''+contentWindow.location.href;
var urltitle = ''+contentDocument.title;
var getSelection = function(){
    var u;
    var winWrapper = new XPCNativeWrapper(contentWindow, 'document', 'getSelection()');
    u = winWrapper.getSelection();
    if(u.getRangeAt){
        if(!u.rangeCount){
            return '';
        }
        u=u.getRangeAt(0)
    }
    if(u.cloneContents){
        u=u.cloneContents();
        d=contentDocument.createElement('div');
        d.appendChild(u);
        if(d.innerHTML==''){u=''}else{u='<html>'+d.innerHTML+'</html>'}
    }
    else if(u.text){
        u=u.text
    }
    return ''+u
};
var wbPopupInfo = function(result) {
    var popupAction = {
        label: 'Открыть закладки \u2192',
        accessKey: 'W',
        callback: function() {
            const newTab = getBrowser().addTab(bookmarkPage);
            getBrowser().selectedTab = newTab;
        },
    };
    var secondaryAction = null;
    if (result.status == 4) /* EDIT_DENIED */
    {
        secondaryAction = [ popupAction ];
        popupAction = {
            label: 'Авторизоваться',
            accessKey: 'A',
            callback: function() {
                const newTab = getBrowser().addTab(wgScript+'?title=Special:UserLogin');
                getBrowser().selectedTab = newTab;
            },
        };
    }
    var popup = PopupNotifications.show(
        getBrowser().selectedBrowser, "wikibookmarks-popup",
        result.msg, null, popupAction, secondaryAction
    );
    setTimeout(function() { popup.remove(); }, 5000);
};
var wbPopupError = function(e) {
    wbPopupInfo({
        status: -1,
        msg: 'Ошибка обращения к серверу',
    });
};
const XMLHttpRequest = Components.Constructor("@mozilla.org/xmlextras/xmlhttprequest;1");
// Send the request
var req = XMLHttpRequest();
req.open("POST", action+'&format=json', true);
req.onerror = wbPopupError;
req.onload = function(e) {
    var jsonData = req.responseText;
    var jsonIface = Components.classes["@mozilla.org/dom/json;1"]
        .createInstance(Components.interfaces.nsIJSON);
    var result;
    try {
        result = JSON.parse(jsonData);
        wbPopupInfo(result);
    } catch (e) {
        wbPopupError();
    }
};
req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
req.send(
    "page="+encodeURIComponent(page)+
    "&url="+encodeURIComponent(url)+
    "&urltitle="+encodeURIComponent(urltitle)+
    "&selection="+encodeURIComponent(getSelection())
);
}('https://your.wiki.url/wiki/index.php', 'User:Someone/Закладки'))

},

}
