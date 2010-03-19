<?php
/**
 * WikiBookmarks.php -- Simple bookmarklet support for managing bookmarks using MediaWiki
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

$dir = dirname(__FILE__) . '/';
$wgAutoloadClasses['SpecialWikiBookmarks'] = $dir . 'WikiBookmarks.body.php';
$wgExtensionMessagesFiles['WikiBookmarks'] = $dir . 'WikiBookmarks.i18n.php';
$wgSpecialPages['Bookmarks'] = 'SpecialWikiBookmarks';

// Extension credits that will show up on Special:Version
$wgExtensionCredits['specialpage'][] = array(
    'path'        => __FILE__,
    'name'        => 'WikiBookmarks',
    'version'     => '0.94:2010-03-19',
    'author'      => 'Vitaliy Filippov',
    'url'         => 'http://www.yourcmc.ru/wiki/index.php/WikiBookmarks_(MediaWiki)',
    'description' => 'Simple bookmarklet support for managing bookmarks using MediaWiki',
);
