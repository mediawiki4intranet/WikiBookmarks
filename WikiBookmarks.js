function wb_bookmarklet(pg)
{
return "javascript:(function(){\
var p=location.protocol;\
if(p!='http:'&&p!='https:'&&p!='ftp:'){return '<meta http-equiv=%22Refresh%22 content=%220; "+wgServer+wgScriptPath+"/index.php?title="+encodeURI(encodeURI(pg))+"%22 />';}\
var w = window.open('about:blank','WikiBookmarks','width=400,height=150,menubar=no,location=no,resizable=yes,scrollbars=no');\
var f = w.document.createElement('form');\
f.method = 'POST';\
f.acceptCharset = 'UTF-8';\
f.action = '"+wgServer+wgScriptPath+"/index.php?title=Special:Bookmarks';\
var inf = function(k,v){var i=w.document.createElement('input');i.type='hidden';i.name=k;i.value=v;f.appendChild(i)};\
inf('page','"+encodeURI(pg)+"');\
inf('url',''+location.href);\
inf('urltitle',''+document.title);\
inf('selection',(function(){var u;if(window.__proto__.getSelection){u=window.__proto__.getSelection.call(window)}else if(document.selection){u=document.selection.createRange()}else if(window.getSelection){u=window.getSelection()}else{return ''}if(u.getRangeAt){if(!u.rangeCount){return '';}u=u.getRangeAt(0)}if(u.cloneContents){u=u.cloneContents();d=document.createElement('div');d.appendChild(u);if(d.innerHTML==''){u=''}else{u='%3Chtml>'+d.innerHTML+'%3C/html>'}}else if(u.text){u=u.text}return ''+u}()));\
w.document.body.appendChild(f);\
f.submit();\
return;\
}())";
}
