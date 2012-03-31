<?php
/**
 * RexSEO - URLRewriter Addon
 *
 * @link https://github.com/gn2netwerk/rexseo
 *
 * @author dh[at]gn2-netwerk[dot]de Dave Holloway
 * @author code[at]rexdev[dot]de jdlx
 *
 * Based on url_rewrite Addon by
 * @author markus.staab[at]redaxo[dot]de Markus Staab
 *
 * @package redaxo4.3.x
 * @version 1.4.282-dev
 */

// GET PARAMS, IDENTIFIER, ROOT DIR
////////////////////////////////////////////////////////////////////////////////
$myself        = rex_request('page', 'string');
$subpage       = rex_request('subpage', 'string')!='' ? rex_request('subpage', 'string'): 'settings';
$chapter       = rex_request('chapter', 'string');
$func          = rex_request('func', 'string');
$section_id    = rex_request('section_id', 'string');
$section_class = rex_request('section_class', 'string');
$highlight     = rex_request('highlight', 'string');
$myroot        = $REX['INCLUDE_PATH'].'/addons/'.$myself;

// BACKEND CSS
////////////////////////////////////////////////////////////////////////////////
if ($REX['REDAXO'])
{
  rex_register_extension('PAGE_HEADER', 'rexseo_header_add');

  function rexseo_header_add($params)
  {
    $params['subject'] .=
      PHP_EOL.'<!-- REXSEO -->'.
      PHP_EOL.'  <link rel="stylesheet" type="text/css" href="../files/addons/rexseo/backend.css" media="screen, projection, print" />'.
      PHP_EOL.'  <script type="text/javascript" src="../files/addons/rexseo/jquery.highlight-3.yui.js"></script>'.
      PHP_EOL.'  <script type="text/javascript" src="../files/addons/rexseo/jquery.autogrow-textarea.js"></script>'.
      PHP_EOL.'  <script type="text/javascript" src="../files/addons/rexseo/jquery.scrollTo-1.4.2-min.js"></script>'.
      PHP_EOL.'<!-- /REXSEO -->'.PHP_EOL;

    return $params['subject'];
  }
}

// INCLUDES
////////////////////////////////////////////////////////////////////////////////
require_once $myroot.'/classes/class.github_connect.inc.php';
require_once $myroot.'/functions/function.rexseo_helpers.inc.php';
require_once $myroot.'/classes/class.rexseo_select.inc.php';

// REX TOP
////////////////////////////////////////////////////////////////////////////////
require $REX['INCLUDE_PATH'] . '/layout/top.php';
$subpage = $subpage=='' ? 'settings' : $subpage; /* 4.2.1 fix: top.php resets $subpage */

// REX TITLE/NAVI
////////////////////////////////////////////////////////////////////////////////
rex_title('RexSEO <span class="addonversion">'.$REX['ADDON']['version'][$myself].'</span>', $REX['ADDON'][$myself]['SUBPAGES']);

// NOTIFY DOWNLOADABLE UPDATE
$gc = new github_connect('gn2netwerk','rexseo');
$new_version = $gc->getLatestVersion($REX['ADDON']['version'][$myself],'link');
if($new_version!='')
{
  echo rex_info('Eine neue Version ist als Download verf&uuml;gbar: '.$new_version);
}

// INCLUDE SUBPAGE
////////////////////////////////////////////////////////////////////////////////
require $REX['INCLUDE_PATH'] . '/addons/'.$myself.'/pages/'.$subpage.'.inc.php';

// JS SCRIPT FÜR LINKS IN NEUEN FENSTERN (per <a class="blank">)
////////////////////////////////////////////////////////////////////////////////
echo '
<script type="text/javascript">
// onload
window.onload = externalLinks;

// http://www.sitepoint.com/article/standards-compliant-world
function externalLinks()
{
 if (!document.getElementsByTagName) return;
 var anchors = document.getElementsByTagName("a");
 for (var i=0; i<anchors.length; i++)
 {
   var anchor = anchors[i];
   if (anchor.getAttribute("href"))
   {
     if (anchor.getAttribute("class") == "blank")
     {
     anchor.target = "_blank";
     }
   }
 }
}
</script>
';

// JQUERY HIGHLIGHT SCRIPT
////////////////////////////////////////////////////////////////////////////////
if($highlight)
{
  if($section_id)
  {
    $section = '#'.$section_id;
  }
  elseif($section_class)
  {
    $section = '.'.$section_class;
  }
  else
  {
    $section = '.rexseo';
  }

  echo '
  <script type="text/javascript">
  <!--
  (function($) {

  // http://johannburkard.de/blog/programming/javascript/highlight-javascript-text-higlighting-jquery-plugin.html
  $(document).ready(function() {
      $("'.$section.'").highlight("'.$highlight.'");
      $.scrollTo("span.highlight", 1000, {offset:-50});
  });

  })(jQuery);
  //-->
  </script>
  ';
}

// REX BOTTOM
////////////////////////////////////////////////////////////////////////////////
require $REX['INCLUDE_PATH'] . '/layout/bottom.php';
?>