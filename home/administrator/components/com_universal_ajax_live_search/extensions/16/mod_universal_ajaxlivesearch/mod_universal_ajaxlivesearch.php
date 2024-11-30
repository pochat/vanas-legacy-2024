<?php 
/*------------------------------------------------------------------------
# mod_universal_ajaxlivesearch - Universal AJAX Live Search 
# ------------------------------------------------------------------------
# author    Janos Biro 
# copyright Copyright (C) 2011 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
?>
<?php
  if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

  if (!extension_loaded('gd') || !function_exists('gd_info')) {
      echo "Universal AJAX Live Search needs the <a href='http://php.net/manual/en/book.image.php'>GD module</a> enabled in your PHP runtime 
      environment. Please consult with your System Administrator and he will 
      enable it!";
      return;
  }
  
  /* For parameter editor */
  if(defined('DEMO')){
    if(isset($_SESSION[$_REQUEST['module']."_params"])){
      $params = new JParameter($_SESSION[$_REQUEST['module']."_params"]);
    }
    if($_REQUEST['module'] != $module->module) return;
  }

  $searchresultwidth = $params->get('resultareawidth', 250);
  $productimageheight = $params->get('productimageheight', 40);
  $productsperplugin = $params->get('itemsperplugin', 3);
  $minchars = $params->get('minchars', 2);
  $resultalign = $params->get('resultalign', 0); // 0-left 1-right
  $scrolling = $params->get('scrolling', 1);
  $intro = $params->get('intro', 1);
  $scount = $params->get('scount', 10);
  $stext = $params->get('stext');
  $resultelementheight = 66;

  $searchboxcaption = $params->get('searchbox', 'Search..');
  $noresultstitle = $params->get('noresultstitle', 'Results(0)');
  $noresults = $params->get('noresults', 'No results found for the keyword!');
  $params->def('theme', 'elegant');
  $theme = $params->get('theme', 'elegant');
  if(is_object($theme)){ //For 1.6
    $params->merge(new JRegistry($params->get('theme')));
    $params->set('theme', $theme->theme);
    $theme = $params->get('theme');
  }
  
  
  $keypresswait = $params->get('stimeout', 500);
  $searchformurl = JRoute::_(JURI::root(true).'/'.(version_compare(JVERSION,'1.6.0','>=') ? 'index' : 'index2').".php");
  $document =& JFactory::getDocument();
  require_once(dirname(__FILE__).DS.'themes'.DS.'cache.class.php');
  require_once('helper'.DS.'Helper.class.php');
  $themecache = new OfflajnThemeCache($module, $params, dirname(__FILE__).DS.'themes'.DS);
  if(defined('DEMO')){
    $extra = md5(session_id());
    $themecache->themeCacheDir = JPATH_CACHE.DS.$module->module.'_theme'.DS.$module->id.$extra;
    if(!is_dir($themecache->themeCacheDir)){
      mkdir ($themecache->themeCacheDir , 0777 , true);
    }
    $themecache->themeCacheUrl = JURI::root(true).'/cache/'.$module->module.'_theme/'.$module->id.$extra.'/';
  }
  $context = array();
  $context['url'] = JURI::root(true).'/modules/'.$module->module.'/themes/'.$theme.'/';
  
  $context['helper'] = new OfflajnAJAXSearchHelper($themecache->themeCacheDir, $themecache->themeCacheUrl);
  $context['productsperplugin'] = $productsperplugin;
  $context['searchresultwidth'] = $searchresultwidth;
  $context['resultelementheight'] = $resultelementheight;
    
  $document->addStyleSheet($themecache->generateCss($context).(defined('DEMO') ? '?'.time() : '')); 
  $document->addScript('modules/mod_universal_ajaxlivesearch/engine/dojo.js'); 
  $document->addScript('https://ajax.googleapis.com/ajax/libs/dojo/1.5/dojo/dojo.xd.js');
  $document->addScript('modules/mod_universal_ajaxlivesearch/engine/engine.js'); 
  $document->addScriptDeclaration("
  dojo.addOnLoad(function(){
      var ajaxSearch = new AJAXSearch({
        node : dojo.byId('offlajn-ajax-search'),
        productsPerPlugin : $productsperplugin,
        searchRsWidth : $searchresultwidth,
        resultElementHeight : $resultelementheight,
        minChars : $minchars,
        searchBoxCaption : '$searchboxcaption',
        noResultsTitle : '$noresultstitle',
        noResults : '$noresults',
        searchFormUrl : '$searchformurl',
        enableScroll : '$scrolling',
        showIntroText: '$intro',
        scount: '$scount',
        stext: '$stext',
        moduleId : '$module->id',
        resultAlign : '$resultalign',
        targetsearch: '".$params->get('targetsearch', 0)."',
        linktarget: '".$params->get('linktarget', 0)."',
        keypressWait: '$keypresswait'
      })
    });" 
  );
  
?>
          
<div id="offlajn-ajax-search">
  <div class="offlajn-ajax-search-container">
  <form id="search-form" action="<?php echo JRoute::_('index.php?option=com_search'); ?>" method="get" onSubmit="return false;">
    <div class="offlajn-ajax-search-inner">
    <?php 
      switch($params->get('targetsearch', 0)){
        case 0:
        case 3:
        ?>
        <input type="text" name="searchword" id="search-area" value="" autocomplete="off" />
        <input type="hidden" name="option" value="com_search" />
        <?php 
          break;
        case 1:
        ?>
        <input type="text" name="keyword" id="search-area" value="" autocomplete="off" />
        <input type="hidden" name="option" value="com_virtuemart" />
        <input type="hidden" name="page" value="shop.browse" />
        <?php
          break;
        case 1:
        ?>
        <input type="text" name="searchword" id="search-area" value="" autocomplete="off" />
        <input type="hidden" name="option" value="com_redshop" />
        <input type="hidden" name="view" value="search" />
        <?php
          break;
      }
    ?>
      <div id="search-area-close"></div>
      <div id="ajax-search-button"><div class="magnifier"></div></div>
      <div class="ajax-clear"></div>
    </div>
  </form>
  <div class="ajax-clear"></div>
  </div>
</div>
<div class="ajax-clear"></div>
