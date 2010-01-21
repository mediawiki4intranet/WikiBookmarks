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
    'bookmarks-help-page' => 'WikiBookmarks/Usage',

    /* Close window */
    'bookmarks-close-window' => 'Close window',

    /* Edit access denied */
    'bookmarks-edit-access-denied' => 'You are forbidden to edit page [[$1]]. Maybe you need to [[Special:UserLogin|login]]?',

    /* Bookmark added */
    'bookmarks-bookmark-added' => 'Bookmark $1 added onto page [[$2]].',

    /* It's already there */
    'bookmarks-bookmark-already-present' => 'Bookmark $1 is already on page [[$2]].',

    /* Error message for "Article title empty" */
    'wikibookmarks-page-empty'       => 'Please, enter non-empty bookmarks article title!',

    /* Article editbox title */
    'wikibookmarks-page-editlabel'   => 'Article title:',

    /* Button text editbox title */
    'wikibookmarks-text-editlabel'   => 'Button text:',

    /* Default button text */
    'wikibookmarks-default-text'     => 'WikiBookmark it',

    /* Make bookmarklet button text */
    'wikibookmarks-make-link'        => 'Make bookmarklet for me',

    /* Default bookmarks per-user sub-article name */
    'wikibookmarks-bookmarks-page'   => 'Bookmarks',

    /* Hint for created bookmarklet */
    'wikibookmarks-drag-bookmarklet' => 'Drag-and-drop the following link onto your browser\'s panel:',
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
    'bookmarks-help-page' => 'ВикиЗакладки/Справка',

    /* Закрыть окно */
    'bookmarks-close-window' => 'Закрыть окно',

    /* Нет прав на редактирование */
    'bookmarks-edit-access-denied' => 'Вам запрещено редактировать страницу [[$1]]. Возможно, следует [[Special:UserLogin|войти]]?',

    /* Закладка добавлена */
    'bookmarks-bookmark-added' => 'Закладка $1 добавлена на страницу [[$2]].',

    /* Закладка уже присутствует */
    'bookmarks-bookmark-already-present' => 'Закладка $1 уже присутствует на странице [[$2]].',

    /* Сообщение об ошибке - о пустом введённом названии страницы закладок */
    'wikibookmarks-page-empty'       => 'Введите, всё-таки, непустое имя страницы для закладок!',

    /* Подпись поля ввода названия страницы закладок */
    'wikibookmarks-page-editlabel'   => 'Страница:',

    /* Подпись поля ввода текста букмарклета */
    'wikibookmarks-text-editlabel'   => 'Текст кнопки:',

    /* Текст по умолчанию для букмарклета */
    'wikibookmarks-default-text'     => 'В ВикиЗакладки',

    /* Текст кнопки создания букмарклета */
    'wikibookmarks-make-link'        => 'Вывести ссылку',

    /* Подстраница страницы участника для закладок по умолчанию */
    'wikibookmarks-bookmarks-page'   => 'Закладки',

    /* Подсказка, а что же делать с букмарклетом */
    'wikibookmarks-drag-bookmarklet' => 'Перетащите следующую ссылку на панель своего браузера:',
);
