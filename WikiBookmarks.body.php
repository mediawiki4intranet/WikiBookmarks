<?php
/**
 * WikiBookmarks.body.php -- Simple bookmarklet support for managing bookmarks using MediaWiki
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

if(!defined('MEDIAWIKI'))
    exit(1);

class SpecialWikiBookmarks extends SpecialPage
{
    public function __construct()
    {
        parent::__construct('Bookmarks');
    }
    public function execute($par)
    {
        global $wgRequest, $wgOut, $wgParser, $wgUser;
        wfLoadExtensionMessages('WikiBookmarks');
        /* если просто запросили справку */
        if (!($page = $wgRequest->getVal('page')) ||
            !($title = Title::newFromText($page)) ||
            !($url = $wgRequest->getVal('url')))
        {
            global $wgLang;
            $article = false;
            /* текст справки загружаем в wiki, чтобы она, например, находилась поиском */
            $vars = array_merge($wgLang->getVariants(), array($wgLang->getFallbackLanguageCode()));
            foreach ($vars as $var)
            {
                $file = dirname(__FILE__).'/WikiBookmarks.'.$var.'.wikitext';
                if (file_exists($file) &&
                    ($title = Title::newFromText(wfMsg('bookmarks-help-page'))) &&
                    ($article = new Article($title)))
                {
                    if (!$article->exists() || filemtime($file) > wfTimestamp(TS_UNIX, $article->getTimestamp()))
                    {
                        $text = file_get_contents($file);
                        if (trim($text) != trim($article->getContent()))
                            $article->doEdit($text, 'WikiBookmarks: load help page', EDIT_FORCE_BOT);
                    }
                    break;
                }
            }
            if ($article)
                $article->doRedirect();
            return;
        }
        /* закладка */
        $urltitle = $wgRequest->getVal('urltitle');
        $selection = $wgRequest->getVal('selection');
        if (!$urltitle)
        {
            $urltitle = urldecode($url);
            /* если есть выделение и оно не безумное - взять его первые <=50 символов на границе слова */
            if ($selection)
            {
                $s = $selection;
                if (substr($s, 0, 6) == '<html>')
                    $s = strip_tags($s);
                if (preg_match('/^.{0,50}\b/is', $s, $m))
                {
                    $urltitle = $m[0];
                    /* если это и было всё выделение - повторять его уже не нужно */
                    if ($selection == $m[0])
                        $selection = '';
                }
            }
        }
        $bookmark = "[$url $urltitle]";
        /* загружаем текст статьи */
        $article = new Article($title);
        if ($article->exists())
            $content = $article->getContent();
        $wgOut->disable();
        if (count($permErrors = $title->getUserPermissionsErrors('edit', $wgUser)) ||
            $wgUser->pingLimiter())
            $msg = wfMsgExt('bookmarks-edit-access-denied', 'parse', $title->getPrefixedText());
        else if (strpos($content, "[$url ") === false)
        {
            $s0 = $article->getSection($content, 0);
            $split = false;
            $datef = false;
            if (preg_match('/<!--\s*bookmarkheadings:?\s*(.+\S)\s*-->/is', $s0, $m))
            {
                $re = "/\"((?:[^\"\\\\]+|\\\\\\\\|\\\\\")+)\"/is";
                preg_match_all($re, $m[1], $split, PREG_PATTERN_ORDER);
                $split = $split[1];
            }
            if (preg_match('/<!--\s*bookmarkdate:?\s*(.+\S)\s*-->/is', $s0, $m))
                $datef = trim($m[1]);
            if (!$split || !count($split))
                $split = array('%Y', '%B %Y');
            if (!$datef)
                $datef = '%d %B, %H:%M:%S:';
            $split = array_map('strftime', $split);
            /* парсим текст статьи */
            $section1 = $article->getSection($content, 1);
            $prefix = '';
            $headlevel = 2;
            for ($i = 0; $i < count($split); $i++)
            {
                if (!preg_match('/^.*?(?:^|\n|\r)(=+)([^\n]*[^=])=+(?:\n|\r|$)/s', $section1, $m))
                    break;
                $headlevel = strlen($m[1]);
                $head = trim($m[2]);
                if ($head == $split[$i])
                {
                    $prefix .= $m[0];
                    $section1 = trim(substr($section1, strlen($m[0])));
                }
                else
                    break;
            }
            if ($i < count($split))
            {
                $prefix = trim($prefix);
                for ($j = $i; $j < count($split); $j++, $headlevel++)
                    $prefix .= "\n\n" . str_repeat('=', $headlevel) . ' ' . $split[$j] . ' ' . str_repeat('=', $headlevel);
                $section1 = "\n" . $section1;
            }
            /* записываем закладку в текст */
            $bm = trim($prefix) . "\n\n* " . strftime($datef) . ' ' . $bookmark . "\n";
            if ($selection)
            {
                if (substr($selection, 0, 6) == '<html>')
                    $nl = '';
                else
                    $nl = '<br/>';
                $selection = str_replace(array("\n", "\r"), array($nl, ''), $selection);
                /* исправляем ссылки в HTML */
                if (!$nl)
                {
                    $fixer = new WikiBookmarksLinkFixer($url);
                    $selection = $fixer->fix($selection);
                }
                $bm .= "*: $selection\n";
            }
            $section1 = $bm . $section1;
            if ($content)
                $content = $wgParser->replaceSection($content, 1, $section1);
            else
                $content = $section1;
            /* сохраняем текст статьи */
            $article->doEdit($content, wfMsgExt('bookmarks-edit-summary', array('parsemag', 'escape'), $url, $urltitle));
            $msg = wfMsgExt('bookmarks-bookmark-added', 'parse', $bookmark, $title->getPrefixedText());
        }
        else
            $msg = wfMsgExt('bookmarks-bookmark-already-present', 'parse', $bookmark, $title->getPrefixedText());
        /* выводим мини-страничку */
        header('Content-Type: text/html; charset=utf-8');
        print
            '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML//EN"><html><head>' .
            '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' .
            '<base target="_blank" /></head><body style="font-family: sans-serif; font-size: 80%; text-align: center">' .
            $msg .
            '<p><a href="#" onclick="window.close()">'.wfMsg('bookmarks-close-window').'</a><script language="JavaScript">setTimeout("window.close()",5000);</script></p>' .
            '</body></html>';
    }
}

class WikiBookmarksLinkFixer
{
    var $base, $dom;
    public function __construct($url)
    {
        $this->base = preg_replace('#[^/]*\?.*$#is', '', $url);
        $this->dom = preg_replace('#^([a-z]+:/*[^/]+).*#', '\1', $this->base);
    }
    function fix_links($m)
    {
        $m[2] = preg_replace('#((?:src|href)\s*=\s*[\'"]?)/([^\'"<>]*)#is', '\1'.$this->dom.'\2', $m[2]);
        $m[2] = preg_replace('#(?>(?:src|href)\s*=\s*[\'"]?)(?![a-z]+:)([^\'"<>]*)#is', '\1'.$this->base.'\2', $m[2]);
        return $m[1].$m[2];
    }
    function fix($s)
    {
        return preg_replace_callback('#(<[a-z0-9_\-:]+)(\s+[^>]*)#is', array($this, 'fix_links'), $s);
    }
}
