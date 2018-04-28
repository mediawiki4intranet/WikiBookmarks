function wb_bookmarklet(pg)
{
    var url = mw.config.get('wgServer')+mw.config.get('wgScriptPath');
    return "javascript:void((function(){\
var p=location.protocol;\
if(p!='http:'&&p!='https:'&&p!='ftp:'){return '<meta http-equiv=%22Refresh%22 content=%220; "+url+"/index.php?title="+encodeURI(encodeURI(pg))+"%22 />';}\
var w = window.open('about:blank','WikiBookmarks','width=400,height=150,menubar=no,location=no,resizable=yes,scrollbars=no');\
var f = w.document.createElement('form');\
f.method = 'POST';\
f.acceptCharset = 'UTF-8';\
f.action = '"+url+"/index.php?title=Special:Bookmarks';\
var inf = function(k,v){var i=w.document.createElement('input');i.type='hidden';i.name=k;i.value=v;f.appendChild(i)};\
inf('page','"+encodeURI(pg)+"');\
inf('url',''+location.href);\
inf('urltitle',''+document.title);\
inf('selection',(function(){var u;if(window.__proto__.getSelection){u=window.__proto__.getSelection.call(window)}else if(document.selection){u=document.selection.createRange()}else if(window.getSelection){u=window.getSelection()}else{return ''}if(u.getRangeAt){if(!u.rangeCount){return '';}u=u.getRangeAt(0)}if(u.cloneContents){u=u.cloneContents();d=document.createElement('div');d.appendChild(u);if(d.innerHTML==''){u=''}else{u='%3Chtml>'+d.innerHTML+'%3C/html>'}}else if(u.text){u=u.text}return ''+u}()));\
w.document.body.appendChild(f);\
alert('x');\
f.submit();\
return;\
})())";
}

function wb_make_link()
{
    var pg = document.getElementById("wb_page").value;
    var t = document.getElementById("wb_text").value;
    if (!t)
        t = mw.msg('wikibookmarks-default-text');
    if (pg == "")
    {
        alert(mw.msg('wikibookmarks-page-empty'));
        return;
    }
    var d = document.getElementById("wb_link_div");
    d.innerHTML = "";
    d.appendChild(document.createTextNode(mw.msg('wikibookmarks-drag-bookmarklet')));
    d.appendChild(document.createElement('br'));
    d.appendChild(document.createTextNode("[[ "));
    var a = document.createElement('a');
    a.href = wb_bookmarklet(pg);
    a.innerHTML = t;
    d.appendChild(a);
    d.appendChild(document.createTextNode(" ]]"));
}

$.ready(function()
{
    var u = mw.config.get('wgUserName');
    if (u)
    {
        var defp = "User:" + u + "/" + mw.msg('wikibookmarks-bookmarks-page');
        document.getElementById("wb_page").value = defp;
        document.getElementById("wb_default").innerHTML = mw.msg('wikibookmarks-default', defp);
    }
});
