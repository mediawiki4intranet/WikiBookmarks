<?php
/**
 * WikiBookmarks.i18n.php -- Simple bookmarklet support for managing bookmarks using MediaWiki
 * Copyright 2009 Vitaliy Filippov <vitalif@mail.ru>
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @file
 * @ingroup Extensions
 * @author Vitaliy Filippov <vitalif@mail.ru>
 */

$messages = array();

/* English
 * @author Vitaliy Filippov <vitalif@mail.ru>
 */
$messages['en'] = array(
    /* Special page */
    'bookmarks' => 'WikiBookmarks',

    /* Edit summary template */
    'bookmarks-edit-summary' => 'Added a bookmark: $2',

    /* Help title */
    'bookmarks-no-params' => 'WikiBookmarks: Usage',

    /* Help text */
    'bookmarks-no-params-text' =>
'\'\'\'WikiBookmarks\'\'\' is a \'\'\'MediaWiki\'\'\' extensions helping
you to manage shared bookmark lists inside a Wiki article.

It seems that now you simply opened [[Special:Bookmarks]].

Actually \'\'\'WikiBookmarks\'\'\' are used in the form of a tiny bookmarklet
([http://en.wikipedia.org/wiki/Bookmarklet what is a bookmarklet?]).
To install such a bookmarklet in your browser, you need to enter the title
of Wiki Article which you want to maintain as your bookmark list, click
\'\'Make bookmarklet\'\' and drag-and-drop displayed link onto your browser\'s
panel or add it into browser bookmarks.

<html><input type="text" id="wb_page" size="20" value="$1/Bookmarks" />&nbsp;<input type="button" value="Make bookmarklet" onclick="wb_make_link()" />
<div id="wb_link_div" style="font-weight: bold"></div>
<script language="JavaScript">
function wb_make_link()
{
 pg = document.getElementById("wb_page").value;
 if (pg == "")
 {
  alert("Page title must not be empty!");
  return;
 }
 document.getElementById("wb_link_div").innerHTML =
  "Drag-and-drop the following link onto your browser\'s panel:<br />[[ <a href=\"javascript:void("+
  "window.open(\'http://</html>{{SERVERNAME}}{{SCRIPTPATH}}<html>/index.php/Special:Bookmarks?page="+encodeURI(encodeURI(pg))+
  "&url=\'+encodeURI(location)+\'&urltitle=\'+encodeURI(document.title),\'WikiBookmarks\',\'width=400,height=150,menubar=no,location=no,resizable=yes,scrollbars=no\')"+
  ")\">WikiBookmark it</a> ]]";
}
</script>
</html>',

    /* Close window */
    'bookmarks-close-window' => 'Close window',

    /* Edit access denied */
    'bookmarks-edit-access-denied' => 'You are forbidden to edit page [[$1]]. Maybe you need to [[Special:UserLogin|login]]?',

    /* Bookmark added */
    'bookmarks-bookmark-added' => 'Bookmark $1 added onto page [[$2]].',

    /* It's already there */
    'bookmarks-bookmark-already-present' => 'Bookmark $1 is already on page [[$2]].',
);

/* Русский
 * @author Vitaliy Filippov <vitalif@mail.ru>
 */
$messages['ru'] = array(
    /* Спецстраница */
    'bookmarks' => 'ВикиЗакладки',

    /* Комментарий к правкам */
    'bookmarks-edit-summary' => 'Добавлена закладка $2',

    /* Заголовок справки */
    'bookmarks-no-params' => 'ВикиЗакладки: справка',

    /* Справка */
    'bookmarks-no-params-text' =>
'\'\'\'ВикиЗакладки\'\'\' — расширение \'\'\'MediaWiki\'\'\', позволяющее управлять публичными
списками закладок с помощью Wiki-статьи.

По всей видимости, Вы просто перешли на страницу [[Служебная:Bookmarks]].

А вообще-то \'\'\'ВикиЗакладки\'\'\' используются в виде небольшого букмарклета
([http://ru.wikipedia.org/wiki/Букмарклет что такое букмарклет?]).
Чтобы установить такой букмарклет себе в браузер, введите название страницы,
на которой Вы хотели бы поддерживать список своих закладок, нажмите кнопку
\'\'Вывести ссылку\'\' и перетащите появившуюся ссылку себе на панель закладок
или добавьте в закладки.

<html><input type="text" id="wb_page" size="20" value="$1/Закладки" />&nbsp;<input type="button" value="Вывести ссылку" onclick="wb_make_link()" />
<div id="wb_link_div" style="font-weight: bold"></div>
<script language="JavaScript">
function wb_make_link()
{
 pg = document.getElementById("wb_page").value;
 if (pg == "")
 {
  alert("Имя страницы не должно быть пустым!");
  return;
 }
 document.getElementById("wb_link_div").innerHTML =
  "Перетащите следующую ссылку на панель своего браузера:<br />[[ <a href=\"javascript:void("+
  "window.open(\'http://</html>{{SERVERNAME}}{{SCRIPTPATH}}<html>/index.php/Special:Bookmarks?page="+encodeURI(encodeURI(pg))+
  "&url=\'+encodeURI(location)+\'&urltitle=\'+encodeURI(document.title),\'WikiBookmarks\',\'width=400,height=150,menubar=no,location=no,resizable=yes,scrollbars=no\')"+
  ")\">В ВикиЗакладки</a> ]]";
}
</script>
</html>',

    /* Закрыть окно */
    'bookmarks-close-window' => 'Закрыть окно',

    /* Нет прав на редактирование */
    'bookmarks-edit-access-denied' => 'Вам запрещено редактировать страницу [[$1]]. Возможно, следует [[Special:UserLogin|войти]]?',

    /* Закладка добавлена */
    'bookmarks-bookmark-added' => 'Закладка $1 добавлена на страницу [[$2]].',

    /* Закладка уже присутствует */
    'bookmarks-bookmark-already-present' => 'Закладка $1 уже присутствует на странице [[$2]].',
);
