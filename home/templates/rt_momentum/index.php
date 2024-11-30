<?php
/**
 * @package Gantry Template Framework - RocketTheme
 * @version 1.5 December 12, 2011
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted index access' );

// load and inititialize gantry class
require_once('lib/gantry/gantry.php');
$gantry->init();

function isBrowserCapable(){
  global $gantry;
  
  $browser = $gantry->browser;
  
  // ie.
  if ($browser->name == 'ie' && $browser->version < 8) return false;
  
  return true;
}
// get the current preset
$gpreset = str_replace(' ','',strtolower($gantry->get('name')));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $gantry->language; ?>" lang="<?php echo $gantry->language;?>" >
<head>
  <?php 
    $gantry->displayHead();
    $gantry->addStyles(array('template.css','joomla.css'));
    
    if ($gantry->browser->platform != 'iphone')
      $gantry->addInlineScript('window.addEvent("domready", function(){ new SmoothScroll(); });');
      
    if ($gantry->get('loadtransition') && isBrowserCapable()){
      $gantry->addScript('load-transition.js');
      $hidden = ' class="rt-hidden"';
    } else {
      $hidden = '';
    }
    
  ?>
</head>
  <body <?php echo $gantry->displayBodyTag(array()); ?>>
    <div id="rt-bg-surround">
      <?php /** Begin Pattern **/ if ($gantry->get('main-pattern')!='none'): ?>
      <div id="rt-bg-pattern" <?php echo $gantry->displayClassesByTag('rt-bg-pattern'); ?>>
        <div class="pattern-gradient"></div>
      </div>
      <?php endif; ?>
      <div class="rt-container">
        <?php /** Begin Drawer **/ if ($gantry->countModules('drawer')) : ?>
        <div id="rt-drawer">
          <?php echo $gantry->displayModules('drawer','standard','standard'); ?>
          <div class="clear"></div>
        </div>
        <?php /** End Drawer **/ endif; ?>
        <?php /** Begin Top **/ if ($gantry->countModules('top')) : ?>
        <div id="rt-top" class="main-bg">
          <?php echo $gantry->displayModules('top','standard','standard'); ?>
          <div class="clear"></div>
        </div>
        <?php /** End Top **/ endif; ?>
        <div id="rt-page-surround" <?php echo $gantry->displayClassesByTag('rt-page-surround'); ?>>
          <?php /** Begin Background **/ if ($gantry->get('background-enabled')): ?>
          <div id="rt-bg-image">
            <div class="grad-bottom"></div>
          </div>
          <?php /** End Background **/ endif; ?>
          <?php /** Begin Slideshow **/ if ($gantry->countModules('slideshow') || $gantry->countModules('ss-'.$gpreset)) : ?>
          <div id="rt-slideshow" <?php echo $gantry->displayClassesByTag('rt-showcase'); ?>>
            <?php echo $gantry->displayModules('ss-'.$gpreset,'basic','basic'); ?>
            <?php echo $gantry->displayModules('slideshow','basic','basic'); ?>
            <div class="grad-bottom"></div>
            <div class="clear"></div>
          </div>
          <?php /** End Slideshow **/ endif; ?>
          <div id="rt-topbar">
            <?php /** Begin Logo **/ if ($gantry->countModules('logo')) : ?>
            <div id="rt-logo-surround">
              <?php echo $gantry->displayModules('logo','basic','standard'); ?>
            </div>
            <?php /** End Logo **/ endif; ?>
            <?php /** Begin Navigation **/ if ($gantry->countModules('navigation')) : ?>
            <div id="rt-navigation" class="<?php if ($gantry->get('menu-centering')) : ?>centered<?php endif; ?>">
              <?php echo $gantry->displayModules('navigation','basic','menu'); ?>
            </div>
            <?php /** End Navigation **/ endif; ?>
          </div>
          <?php if ($gantry->countModules('slideshow') || $gantry->countModules('ss-'.$gpreset)) : ?>
          <div id="slideshow-spacer"></div>
  <?php endif;?>
          <div id="rt-transition"<?php echo $hidden; ?>>
            <?php /** Begin Header **/ if ($gantry->countModules('header')) : ?>
            <div id="rt-header" <?php echo $gantry->displayClassesByTag('rt-showcase'); ?>>
              <?php echo $gantry->displayModules('header','standard','standard'); ?>
              <div class="clear"></div>
            </div>
            <?php /** End Header **/ endif; ?>
            <?php /** Begin Showcase **/ if ($gantry->countModules('showcase')) : ?>
            <div id="rt-showcase" <?php echo $gantry->displayClassesByTag('rt-showcase'); ?>>
              <?php echo $gantry->displayModules('showcase','standard','standard'); ?>
              <div class="clear"></div>
            </div>
            <?php /** End Showcase **/ endif; ?>
            <?php /** Begin Feature **/ if ($gantry->countModules('feature')) : ?>
            <div id="rt-feature" <?php echo $gantry->displayClassesByTag('rt-showcase'); ?>>
              <?php echo $gantry->displayModules('feature','standard','standard'); ?>
              <div class="clear"></div>
            </div>
            <?php endif; ?>
            <?php /** Begin Utility **/ if ($gantry->countModules('utility')) : ?>
            <div id="rt-utility">
              <?php echo $gantry->displayModules('utility','standard','standard'); ?>
              <div class="clear"></div>
            </div>
            <?php /** End Utility **/ endif; ?>
            <div id="rt-container-bg"<?php echo $hidden; ?>>
              <div id="rt-body-surround">
                <?php /** Begin Main Top **/ if ($gantry->countModules('maintop')) : ?>
                <div id="rt-maintop" class="page-block">
                  <?php echo $gantry->displayModules('maintop','standard','shadow'); ?>
                  <div class="clear"></div>
                </div>
                <?php /** End Main Top **/ endif; ?>
                <?php /** Begin Breadcrumbs **/ if ($gantry->countModules('breadcrumb')) : ?>
                <div id="rt-breadcrumbs" class="page-block">
                  <?php echo $gantry->displayModules('breadcrumb','basic','breadcrumbs'); ?>
                  <div class="clear"></div>
                </div>
                <?php /** End Breadcrumbs **/ endif; ?>
                <?php /** Begin Main Body **/ ?>
                  <?php echo $gantry->displayMainbody('mainbody','sidebar','shadow','standard','content','standard','content'); ?>
                <?php /** End Main Body **/ ?>
                <?php /** Begin Main Bottom **/ if ($gantry->countModules('mainbottom')) : ?>
                <div id="rt-mainbottom" class="page-block">
                  <?php echo $gantry->displayModules('mainbottom','standard','shadow'); ?>
                  <div class="clear"></div>
                </div>
                <?php /** End Main Bottom **/ endif; ?>
              </div>
            </div>
          </div>
        </div>
        <?php /** Begin Footer Section **/ if ($gantry->countModules('bottom') or $gantry->countModules('footer')) : ?>
        <div id="rt-footer-surround">
          <?php /** Begin Bottom **/ if ($gantry->countModules('bottom')) : ?>
          <div id="rt-bottom" class="main-bg">
            <?php echo $gantry->displayModules('bottom','standard','standard'); ?>
            <div class="clear"></div>
          </div>
          <?php /** End Bottom **/ endif; ?>
          <?php /** Begin Footer **/ if ($gantry->countModules('footer')) : ?>
          <div id="rt-footer" class="main-bg">
            <?php echo $gantry->displayModules('footer','standard','standard'); ?>
            <div class="clear"></div>
          </div>
          <?php /** End Footer **/ endif; ?>
        </div>
        <?php /** End Footer Section **/ endif; ?>
      </div>
    </div>
    <?php /** Begin Copyright **/ if ($gantry->countModules('copyright')) : ?>
    <div id="rt-copyright" <?php echo $gantry->displayClassesByTag('rt-copyright'); ?>>
      <div class="rt-container">
        <?php echo $gantry->displayModules('copyright','standard','limited'); ?>
        <div class="clear"></div>
      </div>
    </div>
    <?php /** End Copyright **/ endif; ?>
    <?php /** Begin Debug **/ if ($gantry->countModules('debug')) : ?>
    <div id="rt-debug">
      <div class="rt-container">
        <?php echo $gantry->displayModules('debug','standard','standard'); ?>
        <div class="clear"></div>
      </div>
    </div>
    <?php /** End Debug **/ endif; ?>
    <?php /** Begin Popups **/ 
    echo $gantry->displayModules('popup','popup','popup');
    echo $gantry->displayModules('login','login','popup'); 
    /** End Popup s**/ ?>
    <?php /** Begin Analytics **/ if ($gantry->countModules('analytics')) : ?>
    <?php echo $gantry->displayModules('analytics','basic','basic'); ?>
    <?php /** End Analytics **/ endif; ?>

<!-- Start of StatCounter Code for Joomla -->
<script type="text/javascript">
var sc_project=6551997; 
var sc_invisible=1; 
var sc_security="1194400a"; 
</script>
<script type="text/javascript"
src="http://www.statcounter.com/counter/counter.js"></script>
<noscript><div class="statcounter"><a title="joomla
counter" href="http://statcounter.com/joomla/"
target="_blank"><img class="statcounter"
src="http://c.statcounter.com/6551997/0/1194400a/1/"
alt="joomla counter"></a></div></noscript>
<!-- End of StatCounter Code for Joomla -->

<!--Start of Zopim Live Chat Script
<script type="text/javascript">
window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=
d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
_.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute('charset','utf-8');
$.src='//cdn.zopim.com/?Gn0e9yJDNMqgfJ1Hp5Cy2vr1ziEBV0Bn';z.t=+new Date;$.
type='text/javascript';e.parentNode.insertBefore($,e)})(document,'script');
</script>
End of Zopim Live Chat Script-->
    
    
<!--Start of Zopim Live Chat Script-->
<script type="text/javascript">
window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=
d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
_.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute('charset','utf-8');
$.src='//v2.zopim.com/?Gn0e9yJDNMqgfJ1Hp5Cy2vr1ziEBV0Bn';z.t=+new Date;$.
type='text/javascript';e.parentNode.insertBefore($,e)})(document,'script');
</script>
<!--End of Zopim Live Chat Script-->
    

<!-- Quantcast Tag -->
<script type="text/javascript">
var _qevents = _qevents || [];

(function() {
var elem = document.createElement('script');
elem.src = (document.location.protocol == "https:" ? "https://secure" : "http://edge") + ".quantserve.com/quant.js";
elem.async = true;
elem.type = "text/javascript";
var scpt = document.getElementsByTagName('script')[0];
scpt.parentNode.insertBefore(elem, scpt);
})();

_qevents.push({
qacct:"p-a6Zy2bp5gmpGg"
});
</script>

<noscript>
<div style="display:none;">
<img src="//pixel.quantserve.com/pixel/p-a6Zy2bp5gmpGg.gif" border="0" height="1" width="1" alt="Quantcast"/>
</div>
</noscript>
<!-- End Quantcast tag -->

<!-- **********************Start Perfect Pixel tag -->
<script type="text/javascript" src="//pixels.perfectaudience.com/serve/4fdb6c0bc274780001000001.js" async="true"></script>
<!-- **********************End Perfect Pixel tag -->
  </body>
</html>
<?php
$gantry->finalize();

?>