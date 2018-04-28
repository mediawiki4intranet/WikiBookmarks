window.opener.postMessage('loaded', '*');

window.addEventListener('message', function(event)
{
    var data = JSON.parse(event.data);
    var f = document.createElement('form');
    f.method = 'POST';
    f.acceptCharset = 'UTF-8';
    f.action = 'index.php?title=Special:Bookmarks';
    var inf = function(k,v)
    {
        var i = document.createElement('input');
        i.type = 'hidden';
        i.name = k;
        i.value = v;
        f.appendChild(i);
    };
    inf('page', data.page);
    inf('url', data.url);
    inf('urltitle', data.urltitle);
    inf('selection', data.selection);
    document.body.appendChild(f);
    f.submit();
}, false);
