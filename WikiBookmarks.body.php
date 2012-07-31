<?php
/**
 * WikiBookmarks.body.php -- Simple bookmarklet support for managing bookmarks using MediaWiki
 * Copyright 2009+ Vitaliy Filippov <vitalif@mail.ru>
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
    const ALREADY_PRESENT = 1;
    const CITE_ADDED = 2;
    const BOOKMARK_ADDED = 3;
    const EDIT_DENIED = 4;

    public function __construct()
    {
        parent::__construct('Bookmarks');
    }

    static function fixSelection($selection, $url)
    {
        if ($selection)
        {
            if (substr($selection, 0, 6) == '<html>')
                $nl = ' ';
            else
                $nl = '<br/>';
            $selection = str_replace(array("\n", "\r"), array($nl, ''), $selection);
            /* исправляем ссылки в HTML */
            if (substr($selection, 0, 6) == '<html>')
            {
                $fixer = new WikiBookmarksLinkFixer($url);
                $selection = $fixer->fix($selection);
            }
            $selection = "*: $selection\n";
        }
        else
            $selection = '';
        return $selection;
    }

    // Add bookmark/citation to text $content, return status
    static function addBookmark(&$content, $url, $urltitle, $selection)
    {
        global $wgParser, $egWikiBookmarksPageTemplate;
        $selection = self::fixSelection($selection, $url);
        $comment = false;
        $bookmark = "[$url $urltitle]";
        if (($p = strpos($content, "[$url ")) !== false)
        {
            // Bookmark is already present
            $p = strpos($content, "\n", $p);
            if ($p === false)
                $p = strlen($content);
            else
                $p++;
            // Extract previous citation from article text
            $cite = '';
            if (preg_match("/(?<=\n)[ \t\r]*([^ \t\r\\*:]|\\*[ \t\r]*[^ \t\r\\*:])/s", $content, $m, PREG_OFFSET_CAPTURE, $p))
                $cite = substr($content, $p, $m[0][1]-$p);
            if (!$selection || strpos(preg_replace('/\s+/s', '', $cite), preg_replace('/\s+/', '', $selection)) !== false)
            {
                // Bookmark and/or citation already present
                return self::ALREADY_PRESENT;
            }
            else
            {
                // Append citation to the existing bookmark
                if (!trim($cite))
                    $selection .= "<!-- NEXT BOOKMARK -->\n";
                $content = substr($content, 0, $p) . $selection . substr($content, $p);
                return self::CITE_ADDED;
            }
        }
        else
        {
            // Bookmark is not yet present in the article
            $s0 = $wgParser->getSection($content, 0);
            $split = false;
            $datef = false;
            // Extract heading template parameter
            if (preg_match('/<!--\s*bookmarkheadings:?\s*(.+\S)\s*-->/is', $s0, $m))
            {
                $re = "/\"((?:[^\"\\\\]+|\\\\\\\\|\\\\\")+)\"/is";
                preg_match_all($re, $m[1], $split, PREG_PATTERN_ORDER);
                $split = $split[1];
            }
            // Extract date template parameter
            if (preg_match('/<!--\s*bookmarkdate:?\s*(.+\S)\s*-->/is', $s0, $m))
                $datef = trim($m[1]);
            if (!$split || !count($split))
                $split = array('%Y', '%Y-%m');
            if (!$datef)
                $datef = '%Y-%m-%d, %H:%M:%S:';
            $split = array_map('strftime', $split);
            // Parse article text
            $section1 = $wgParser->getSection($content, 1);
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
            // Append bookmark
            $bm = trim($prefix) . "\n\n* " . strftime($datef) . ' ' . $bookmark . "\n";
            if ($selection)
                $selection .= "<!-- NEXT BOOKMARK -->\n";
            $section1 = $bm . $selection . $section1;
            if ($content)
                $content = $wgParser->replaceSection($content, 1, $section1);
            else
            {
                $tmpl = $egWikiBookmarksPageTemplate;
                if (!$tmpl)
                    $tmpl = 'WikiBookmarks';
                if (($tmpl = Title::newFromText($tmpl, NS_TEMPLATE)) &&
                    ($tmpl = new Article($tmpl)) &&
                    ($tmpl->getID()))
                {
                    $content = $tmpl->getContent();
                    $content = str_ireplace(
                        array('$title', '$username', '$userrealname'),
                        array($title->getText(), $wgUser->getName(), $wgUser->getRealName()),
                        $content
                    );
                    if (stripos($content, '$content') !== false)
                        $content = str_ireplace('$content', $section1, $content);
                    else
                        $content .= "\n" . $section1;
                }
                else
                    $content = $section1;
            }
            return self::BOOKMARK_ADDED;
        }
    }

    // Preload help text onto help page so it can be searched, and display it
    static function showHelp()
    {
        global $wgLang, $wgEnableParserCache;
        $wgEnableParserCache = false;
        $article = false;
        $vars = array_merge($wgLang->getVariants(), array($wgLang->getFallbackLanguageCode()));
        $htmlfile = dirname(__FILE__).'/WikiBookmarks.html';
        $htmlmtime = filemtime($htmlfile);
        foreach ($vars as $var)
        {
            $file = dirname(__FILE__).'/WikiBookmarks.'.$var.'.wikitext';
            if (file_exists($file) &&
                ($title = Title::newFromText(wfMsg('bookmarks-help-page'))) &&
                ($article = new Article($title)))
            {
                $mtime = filemtime($file);
                if ($htmlmtime > $mtime)
                    $mtime = $htmlmtime;
                if (!$article->exists() || $mtime > wfTimestamp(TS_UNIX, $article->getTimestamp()))
                {
                    $text = file_get_contents($file);
                    $text = str_replace('__BOOKMARKLET_CREATION_CODE__', trim(file_get_contents($htmlfile)), $text);
                    $r = new WikiBookmarksMessageReplacer($var);
                    $text = $r->run($text);
                    if (trim($text) != trim($article->getContent()))
                    {
                        $article->doEdit($text, 'WikiBookmarks: load help page', EDIT_FORCE_BOT);
                        $article = new Article($title);
                    }
                }
                break;
            }
        }
        if ($article)
            $article->view();
    }

    // Get call parameters
    static function getRequestParams()
    {
        global $wgRequest;
        if (!$wgRequest->wasPosted())
            return array();
        $page = $wgRequest->getVal('page');
        $format = $wgRequest->getVal('format', 'html');
        $url = $wgRequest->getVal('url');
        $urltitle = $wgRequest->getVal('urltitle');
        $selection = $wgRequest->getVal('selection');
        if (!$urltitle)
        {
            // If title is empty, guess it from url and/or first 50 chars of selection
            $urltitle = urldecode($url);
            if ($selection)
            {
                $s = $selection;
                if (substr($s, 0, 6) == '<html>')
                    $s = strip_tags($s);
                if (preg_match('/^.{0,50}\b/is', $s, $m))
                {
                    $urltitle = $m[0];
                    // If the selection was equal to this string - do not repeat it
                    if ($selection == $m[0])
                        $selection = '';
                }
            }
        }
        $urltitle = str_replace(array("[", "]"), array("(", ")"), trim($urltitle));
        return array(
            'url' => $url,
            'page' => $page,
            'format' => $format,
            'urltitle' => $urltitle,
            'selection' => $selection,
        );
    }

    // Edit an article (add bookmark/citation) and return edit status
    static function editArticle($title, $url, $urltitle, $selection)
    {
        global $wgUser;
        if (count($permErrors = $title->getUserPermissionsErrors('edit', $wgUser)) ||
            $wgUser->pingLimiter() ||
            !$title->userCan('edit'))
        {
            return self::EDIT_DENIED;
        }
        else
        {
            $article = new Article($title);
            $content = '';
            if ($article->exists())
            {
                // Load article text
                $content = $article->getContent();
            }
            $comment = false;
            $status = self::addBookmark($content, $url, $urltitle, $selection);
            if ($status != self::ALREADY_PRESENT)
            {
                // Record article edit
                if ($status == self::CITE_ADDED)
                    $comment = 'bookmarks-add-cite-summary';
                else
                    $comment = 'bookmarks-edit-summary';
                $comment = wfMsgExt($comment, array('parsemag', 'escape'), $url, $urltitle);
                $article->doEdit($content, $comment);
            }
        }
        return $status;
    }

    // Format status message
    // @param boolean $fancy whether to include HTML into output
    static function getStatusMessage($status, $title, $params, $fancy)
    {
        $bookmark = "[$params[url] $params[urltitle]]";
        if ($status == self::EDIT_DENIED)
        {
            $msg = wfMsgExt('bookmarks-edit-access-denied', 'parse', $title->getPrefixedText());
        }
        elseif ($status == self::ALREADY_PRESENT)
        {
            $msg = wfMsgExt('bookmarks-bookmark-already-present', 'parse', $bookmark, $title->getPrefixedText());
        }
        elseif ($status == self::CITE_ADDED)
        {
            if ($fancy)
            {
                $msg = wfMsgExt(
                    'bookmarks-cite-added', 'parse',
                    $params['url'], $params['urltitle'],
                    $params['selection'], $title->getPrefixedText()
                );
            }
            else
            {
                $msg = wfMsgExt('bookmarks-cite-added-simple', 'parse', $params['url'], $params['urltitle'], $title->getPrefixedText());
            }
        }
        elseif ($params['selection'])
        {
            if ($fancy)
            {
                $msg = wfMsgExt('bookmarks-bookmark-cite-added', 'parse', $bookmark, $title->getPrefixedText(), $params['selection']);
            }
            else
            {
                $msg = wfMsgExt('bookmarks-bookmark-cite-added-simple', 'parse', $bookmark, $title->getPrefixedText());
            }
        }
        else
        {
            $msg = wfMsgExt('bookmarks-bookmark-added', 'parse', $bookmark, $title->getPrefixedText());
        }
        if (!$fancy)
        {
            $msg = strip_tags($msg);
        }
        return $msg;
    }

    // Output status message as JSON object
    static function printJsonStatus($status, $title, $params)
    {
        $msg = self::getStatusMessage($status, $title, $params, false);
        header('Content-Type: application/json; charset=utf-8');
        print json_encode(array(
            'status' => $status,
            'msg' => $msg,
        ));
    }

    // Output simple HTML page with status message
    static function printHtmlStatus($status, $title, $params)
    {
        $msg = self::getStatusMessage($status, $title, $params, true);
        header('Content-Type: text/html; charset=utf-8');
        print
            '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML//EN"><html><head>' .
            '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' .
            '<base target="_blank" /></head><body style="font-family: sans-serif; font-size: 80%; text-align: center">' .
            $msg .
            '<p><a href="#" onclick="window.close()">'.wfMsg('bookmarks-close-window').
            '</a><script language="JavaScript">setTimeout("window.close()",5000);</script></p>' .
            '</body></html>';
    }

    // Entry point
    public function execute($par)
    {
        global $wgOut, $wgParser, $wgUser, $egWikiBookmarksPageTemplate;
        wfLoadExtensionMessages('WikiBookmarks');
        // Get call parameters
        $params = self::getRequestParams();
        if (!$params || !($title = Title::newFromText($params['page'])))
        {
            // This is just a call to Special:WikiBookmarks (shows the help text)
            self::showHelp();
            return;
        }
        // Edit article and print status
        $status = self::editArticle($title, $params['url'], $params['urltitle'], $params['selection']);
        $wgOut->disable();
        if ($params['format'] == 'json')
        {
            self::printJsonStatus($status, $title, $params);
        }
        else
        {
            self::printHtmlStatus($status, $title, $params);
        }
    }
}

class WikiBookmarksMessageReplacer
{
    var $lang;
    function __construct($lang)
    {
        $this->lang = $lang;
    }
    function run($s)
    {
        return preg_replace_callback('#\{\{MediaWiki:([^\}]*)\}\}#', array($this, 'getMessage'), $s);
    }
    function getMessage($m)
    {
        return wfMsgGetKey($m[1], true, $this->lang);
    }
}

class WikiBookmarksLinkFixer
{
    var $base, $dom;
    function __construct($url)
    {
        $this->base = preg_replace('#[^/]*(\?.*)?$#is', '', $url);
        $this->dom = preg_replace('#^([a-z]+:/*[^/]+).*#', '\1', $this->base);
    }
    function fix_links($m)
    {
        $m[2] = preg_replace('#((?:src|href)\s*=\s*[\'"]?)/([^\'"<>]*)#is', '\1'.$this->dom.'/\2', $m[2]);
        $m[2] = preg_replace('#((?>(?:src|href)\s*=\s*[\'"]?))(?![a-z]+:)([^\'"<>]*)#is', '\1'.$this->base.'\2', $m[2]);
        return $m[1].$m[2];
    }
    function fix($s)
    {
        return preg_replace_callback('#(<[a-z0-9_\-:]+)(\s+[^>]*)#is', array($this, 'fix_links'), $s);
    }
}
