<?php
ob_start ("ob_gzhandler");
header("Content-type: text/css; charset: UTF-8");
header("Cache-Control: must-revalidate");
$expires_time = 1440;
$offset = 60 * $expires_time ;
$ExpStr = "Expires: " .
gmdate("D, d M Y H:i:s",
time() + $offset) . " GMT";
header($ExpStr);
                ?>

/*** overlays.css ***/

/**
 * @package   Momentum Template - RocketTheme
 * @version   1.5 December 12, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.rockettheme.com/legal/license.php RocketTheme Proprietary Use License
 */

/* Main Patterns */
.main-pattern-carbon {background: url(../images/overlays/patterns/carbon.png) 50% 0;}
.main-pattern-carbonfiber {background: url(../images/overlays/patterns/carbonfiber.png) 50% 0;}
.main-pattern-cardboard {background: url(../images/overlays/patterns/cardboard.png) 50% 0;}
.main-pattern-circles {background: url(../images/overlays/patterns/circles.png) 50% 0;}
.main-pattern-dustnscratches {background: url(../images/overlays/patterns/dustnscratches.png) 50% 0;}
.main-pattern-grunge {background: url(../images/overlays/patterns/grunge.png) 50% 0;}
.main-pattern-leather {background: url(../images/overlays/patterns/leather.png) 50% 0;}
.main-pattern-lines {background: url(../images/overlays/patterns/lines.png) 50% 0;}
.main-pattern-paper {background: url(../images/overlays/patterns/paper.png) 50% 0;}
.main-pattern-projectpaper {background: url(../images/overlays/patterns/projectpaper.png) 50% 0;}
.main-pattern-rubber {background: url(../images/overlays/patterns/rubber.png) 50% 0;}
.main-pattern-textile {background: url(../images/overlays/patterns/textile.png) 50% 0;}
.main-pattern-waves {background: url(../images/overlays/patterns/waves.png) 50% 0;}
.main-pattern-wood {background: url(../images/overlays/patterns/wood.png) 50% 0;}
.main-pattern-woven {background: url(../images/overlays/patterns/woven.png) 50% 0;}

/* Logo - DARK */
.primary-overlay-dark #rt-logo {background: url(../images/logo/dark/logo.png) 0 0 no-repeat;}
.primary-overlay-dark #rt-logo-surround {background-image: url(../images/overlays/dark/diag-lines2.png);}

/* Logo - LIGHT */
.primary-overlay-light #rt-logo {background: url(../images/logo/light/logo.png) 0 0 no-repeat;}
.primary-overlay-light #rt-logo-surround {background-image: url(../images/overlays/light/diag-lines2.png);}

/* Navigation - DARK */
.page-overlay-dark #rt-navigation .rt-fusionmenu, .page-overlay-dark #rt-navigation .rt-splitmenu {background: url(../images/overlays/global/light-15.png);}
.primary-overlay-dark .menutop ul li {background-image: url(../images/overlays/dark/menu-arrows.png);}
.pmimary-overlay-dark .menutop ul li:hover > .item {background: url(../images/overlays/global/light-20.png);}
.primary-overlay-dark .menutop li.parent.root .item span .daddyicon, .primary-overlay-dark .menutop li.root .daddy.item .daddyicon, .menutop .primary-overlay-dark ul li > .daddy span {background-image: url(../images/overlays/dark/menu-arrows.png);}
.fusion-submenu-wrapper.primary-overlay-dark ul li.active > .item, .fusion-submenu-wrapper.primary-overlay-dark ul li > .item:hover, .fusion-submenu-wrapper.primary-overlay-dark li.f-menuparent-itemfocus > .item {background-image: url(../images/overlays/dark/diag-lines2.png);}
.primary-overlay-dark .menutop .fusion-submenu-wrapper, .primary-overlay-dark .menutop li.active.root, .primary-overlay-dark .menutop li.root:hover, .primary-overlay-dark .menutop li.f-mainparent-itemfocus, .primary-overlay-dark .rt-splitmenu .menutop li.active, .primary-overlay-dark .rt-splitmenu .menutop li:hover {background-image: url(../images/overlays/dark/diag-lines2.png);}

/* Navigation - LIGHT */
.page-overlay-light #rt-navigation .rt-fusionmenu, .page-overlay-light #rt-navigation .rt-splitmenu {background: url(../images/overlays/global/dark-70.png);}
.primary-overlay-light .menutop ul li {background-image: url(../images/overlays/light/menu-arrows.png);}
.pmimary-overlay-light .menutop ul li:hover > .item {background: url(../images/overlays/global/light-20.png);}
.primary-overlay-light .menutop li.parent.root .item span .daddyicon, .primary-overlay-light .menutop li.root .daddy.item .daddyicon, .menutop .primary-overlay-light ul li > .daddy span {background-image: url(../images/overlays/light/menu-arrows.png);}
.fusion-submenu-wrapper.primary-overlay-light ul li.active > .item, .fusion-submenu-wrapper.primary-overlay-light ul li > .item:hover, .fusion-submenu-wrapper.primary-overlay-light li.f-menuparent-itemfocus > .item {background-image: url(../images/overlays/light/diag-lines2.png);background-color: rgba(0,0,0,0.1);}
.primary-overlay-light .menutop .fusion-submenu-wrapper, .primary-overlay-light .menutop li.active.root, .primary-overlay-light .menutop li.root:hover, .primary-overlay-light .menutop li.f-mainparent-itemfocus, .primary-overlay-light .rt-splitmenu .menutop li.active, .primary-overlay-light .rt-splitmenu .menutop li:hover {background-image: url(../images/overlays/light/diag-lines2.png);}

/* Side Menus - DARK */
.primary-overlay-dark .module-content ul.menu {background-image: url(../images/overlays/dark/diag-lines2.png);}
.primary-overlay-dark .module-content ul.menu li {background-image: url(../images/overlays/dark/horz-line.png);}
.primary-overlay-dark .module-content ul.menu li a span, .primary-overlay-dark .module-content ul.menu li .item span, .primary-overlay-dark .module-content ul.menu li .separator span {background-image: url(../images/overlays/dark/menu-arrows.png);}
.primary-overlay-dark .module-content ul.menu li.parent li a span, .primary-overlay-dark .module-content ul.menu li.parent li .item span, .primary-overlay-dark .module-content ul.menu li.parent li .separator span {background-image: url(../images/overlays/dark/arrow-accent.png);}
.primary-overlay-dark .module-content ul.menu a:hover, .primary-overlay-dark .module-content ul.menu li.active a, .primary-overlay-dark .module-content ul.menu li li.active a, .primary-overlay-dark .module-content ul.menu .item:hover, .primary-overlay-dark .module-content ul.menu li.active .item, .primary-overlay-dark .module-content ul.menu li li.active .item, .primary-overlay-dark .module-content ul.menu .separator:hover, .primary-overlay-dark .module-content ul.menu li.active .separator, .primary-overlay-dark .module-content ul.menu li li.active .separator {background-image: url(../images/overlays/dark/menu-bg.png);}

/* Side Menus - LIGHT */
.primary-overlay-light .module-content ul.menu {background-image: url(../images/overlays/light/diag-lines2.png);}
.primary-overlay-light .module-content ul.menu li {background-image: url(../images/overlays/light/horz-line.png);}
.primary-overlay-light .module-content ul.menu li a span, .primary-overlay-light .module-content ul.menu li .item span, .primary-overlay-light .module-content ul.menu li .separator span {background-image: url(../images/overlays/light/menu-arrows.png);}
.primary-overlay-light .module-content ul.menu li.parent li a span, .primary-overlay-light .module-content ul.menu li.parent li .item span, .primary-overlay-light .module-content ul.menu li.parent li .separator span {background-image: url(../images/overlays/light/arrow-accent.png);}
.primary-overlay-light .module-content ul.menu a:hover, .primary-overlay-light .module-content ul.menu li.active a, .primary-overlay-light .module-content ul.menu li li.active a, .primary-overlay-light .module-content ul.menu .item:hover, .primary-overlay-light .module-content ul.menu li.active .item, .primary-overlay-light .module-content ul.menu li li.active .item, .primary-overlay-light .module-content ul.menu .separator:hover, .primary-overlay-light .module-content ul.menu li.active .separator, .primary-overlay-light .module-content ul.menu li li.active .separator {background-image: url(../images/overlays/light/menu-bg.png);}

/* Page Container - DARK */
.page-content-dark .component-block, .page-content-dark #rt-content-top, .page-content-dark #rt-content-bottom, .page-content-dark #rt-content-top .rt-block, .page-content-dark #rt-content-bottom .rt-block {background-color: #303030;}
.page-content-dark .component-content .rt-article-icons .icon, .page-content-dark .component-content .rt-article-icons ul li a {background-image: url(../images/overlays/dark/article-icons.png);}
.readonstyle-button .page-content-dark .component-content .readon span, .readonstyle-button .page-content-dark .component-content .readon .button {color: #303030;}

/* Page Container - LIGHT */
.page-content-light .component-block, .page-content-light #rt-content-top, .page-content-light #rt-content-bottom {background-color: #fff;}
.page-content-light .component-content .rt-article-icons .icon, .page-content-light .component-content .rt-article-icons ul li a {background-image: url(../images/overlays/light/article-icons.png);}
.readonstyle-button .page-content-light .component-content .readon span, .readonstyle-button .page-content-light .component-content .readon .button {color: #fff;}

/* Content Top - Content Bottom - DARK */
.primary-overlay-dark .ribbon .module-surround {background-image: url(../images/overlays/dark/menu-bg.png);}
.primary-overlay-dark #rt-content-top .ribbon .module-title, .primary-overlay-dark #rt-content-bottom .ribbon .module-title {background-image: url(../images/overlays/dark/twitter-icon.png);}
.page-content-dark .ribbon-l {background-image: url(../images/overlays/dark/ribbon-l.png);}
.page-content-dark .ribbon-r {background-image: url(../images/overlays/dark/ribbon-r.png);}

/* Content Top - Content Bottom - LIGHT */
.primary-overlay-light .ribbon .module-surround {background-image: url(../images/overlays/light/menu-bg.png);}
.primary-overlay-light #rt-content-top .ribbon .module-title, .primary-overlay-light #rt-content-bottom .ribbon .module-title {background-image: url(../images/overlays/light/twitter-icon.png);}
.page-content-light .ribbon-l {background-image: url(../images/overlays/light/ribbon-l.png);}
.page-content-light .ribbon-r {background-image: url(../images/overlays/light/ribbon-r.png);}

/* Modules - DARK */
.page-overlay-dark .block-shadow, .page-overlay-dark .component-block {box-shadow: 4px 4px 0 rgba(0,0,0,0.8);}

/* Modules - LIGHT */
.page-overlay-light .block-shadow, .page-overlay-light .component-block {box-shadow: 4px 4px 0 rgba(0,0,0,0.1);}

/* Module Variations - Box */
.box3 .rt-block {background-image: url(../images/overlays/dark/menu-bg.png);}
.title1 .module-title, .main-overlay-light .main-bg .title .module-title {background-image: url(../images/overlays/light/menu-arrows.png);}
.main-overlay-dark .main-bg .title1 .module-title {background-image: url(../images/overlays/dark/menu-arrows.png);}
.title3 .title {background: url(../images/overlays/global/dark-30.png);}

/* Module Variations - Global */
.dark10 .rt-block {background: url(../images/overlays/global/dark-10.png);}
.dark15 .rt-block {background: url(../images/overlays/global/dark-15.png);}
.dark20 .rt-block {background: url(../images/overlays/global/dark-20.png);}
.dark25 .rt-block {background: url(../images/overlays/global/dark-25.png);}
.dark30 .rt-block {background: url(../images/overlays/global/dark-30.png);}
.dark35 .rt-block {background: url(../images/overlays/global/dark-35.png);}
.dark40 .rt-block {background: url(../images/overlays/global/dark-40.png);}
.dark45 .rt-block {background: url(../images/overlays/global/dark-45.png);}
.dark50 .rt-block {background: url(../images/overlays/global/dark-50.png);}
.dark55 .rt-block {background: url(../images/overlays/global/dark-55.png);}
.dark60 .rt-block {background: url(../images/overlays/global/dark-60.png);color: #fff;}
.dark65 .rt-block {background: url(../images/overlays/global/dark-65.png);color: #fff;}
.dark70 .rt-block {background: url(../images/overlays/global/dark-70.png);color: #fff;}
.dark75 .rt-block {background: url(../images/overlays/global/dark-75.png);color: #fff;}
.dark80 .rt-block {background: url(../images/overlays/global/dark-80.png);color: #fff;}
.dark85 .rt-block {background: url(../images/overlays/global/dark-85.png);color: #fff;}
.dark90 .rt-block {background: url(../images/overlays/global/dark-90.png);color: #fff;}
.dark95 .rt-block {background: url(../images/overlays/global/dark-95.png);color: #fff;}
.light10 .rt-block {background: url(../images/overlays/global/light-10.png);}
.light15 .rt-block {background: url(../images/overlays/global/light-15.png);}
.light20 .rt-block {background: url(../images/overlays/global/light-20.png);}
.light25 .rt-block {background: url(../images/overlays/global/light-25.png);}
.light30 .rt-block {background: url(../images/overlays/global/light-30.png);}
.light35 .rt-block {background: url(../images/overlays/global/light-35.png);}
.light40 .rt-block {background: url(../images/overlays/global/light-40.png);}
.light45 .rt-block {background: url(../images/overlays/global/light-45.png);}
.light50 .rt-block {background: url(../images/overlays/global/light-50.png);}
.light55 .rt-block {background: url(../images/overlays/global/light-55.png);}
.light60 .rt-block {background: url(../images/overlays/global/light-60.png);color: #000;}
.light65 .rt-block {background: url(../images/overlays/global/light-65.png);color: #000;}
.light70 .rt-block {background: url(../images/overlays/global/light-70.png);color: #000;}
.light75 .rt-block {background: url(../images/overlays/global/light-75.png);color: #000;}
.light80 .rt-block {background: url(../images/overlays/global/light-80.png);color: #000;}
.light85 .rt-block {background: url(../images/overlays/global/light-85.png);color: #000;}
.light90 .rt-block {background: url(../images/overlays/global/light-90.png);color: #000;}
.light95 .rt-block {background: url(../images/overlays/global/light-95.png);color: #000;}

/* Buttons and Inputs - DARK */
.main-overlay-dark .main-bg .rokradios, .main-overlay-dark .main-bg .rokchecks, .main-overlay-dark .main-bg .rokradios-active, .main-overlay-dark .main-bg .rokchecks-active, .page-overlay-dark .rokradios, .page-overlay-dark .rokchecks, .page-overlay-dark .rokradios-active, .page-overlay-dark .rokchecks-active, .bottomblock-overlay-dark .rokradios, .bottomblock-overlay-dark .rokchecks, .bottomblock-overlay-dark .rokradios-active, .bottomblock-overlay-dark .rokchecks-active, .primary-overlay-dark .box2 .rokradios, .primary-overlay-dark .box2 .rokchecks, .primary-overlay-dark .box2 .rokradios-active, .primary-overlay-dark .box2 .rokchecks-active, .primary-overlay-dark .box3 .rokradios, .primary-overlay-dark .box3 .rokchecks, .primary-overlay-dark .box3 .rokradios-active, .primary-overlay-dark .box3 .rokchecks-active {background-image: url(../images/overlays/dark/rokinputs.png);}
.inputbox, #form-login .inputbox, #com-form-login .inputbox, #josForm .inputbox, .component-content .search_result .inputbox, .component-content .contact .inputbox, .component-content .user input#email, .component-content .user input#name, .component-content .user input#username, .component-content .user input#password, .component-content .user input#passwd, .component-content .user input#password2, #rokajaxsearch .inputbox {background: url(../images/overlays/global/dark-10.png);box-shadow: 4px 4px 0 rgba(0,0,0,0.2);}
.basic .inputbox, .basic #form-login .inputbox, .basic #rokajaxsearch .inputbox {background: url(../images/overlays/global/light-90.png);box-shadow: 4px 4px 0 rgba(0,0,0,0.1);}
.main-overlay-dark.readonstyle-button .main-bg a.readon:hover, .readonstyle-button .page-overlay-dark .readon:hover, .main-overlay-dark.readonstyle-button #rt-page-surround .box1 .readon:hover, .readonstyle-button .bottomblock-overlay-dark .readon:hover, .primary-overlay-dark.readonstyle-button .box2 .readon, .primary-overlay-dark.readonstyle-button .box3 .readon {background-color: #fff;}
.primary-overlay-dark.readonstyle-button .main-bg .box2 .readon:hover, .primary-overlay-dark.readonstyle-button .main-bg .box3 .readon:hover {background-color: #000;}
.main-overlay-dark.readonstyle-button .main-bg .readon:hover span, .main-overlay-dark.readonstyle-button .main-bg .readon:hover .button, .readonstyle-button .page-overlay-dark .readon:hover span, .readonstyle-button .page-overlay-dark .readon:hover .button, .main-overlay-dark.readonstyle-button #rt-page-surround .box1 .readon:hover span, .main-overlay-dark.readonstyle-button #rt-page-surround .box1 .readon:hover .button, .bottomblock-overlay-dark .readon:hover span, .bottomblock-overlay-dark .readon:hover .button, .primary-overlay-dark.readonstyle-button .box2 .readon span, .primary-overlay-dark.readonstyle-button .box2 .readon .button, .primary-overlay-dark.readonstyle-button .box3 .readon span, .primary-overlay-dark.readonstyle-button .box3 .readon .button {color: #000;}
.primary-overlay-dark.readonstyle-button .box2 .readon:hover span, .primary-overlay-dark.readonstyle-button .box2 .readon:hover .button, .primary-overlay-dark.readonstyle-button .box3 .readon:hover span, .primary-overlay-dark.readonstyle-button .box3 .readon:hover .button {color: #fff;}

/* Buttons and Inputs - LIGHT */
.main-overlay-light .main-bg .rokradios, .main-overlay-light .main-bg .rokchecks, .main-overlay-light .main-bg .rokradios-active, .main-overlay-light .main-bg .rokchecks-active, .page-overlay-light .rokradios, .page-overlay-light .rokchecks, .page-overlay-light .rokradios-active, .page-overlay-light .rokchecks-active, .bottomblock-overlay-light .rokradios, .bottomblock-overlay-light .rokchecks, .bottomblock-overlay-light .rokradios-active, .bottomblock-overlay-light .rokchecks-active, .primary-overlay-light .box2 .rokradios, .primary-overlay-light .box2 .rokchecks, .primary-overlay-light .box2 .rokradios-active, .primary-overlay-light .box2 .rokchecks-active, .primary-overlay-light .box3 .rokradios, .primary-overlay-light .box3 .rokchecks, .primary-overlay-light .box3 .rokradios-active, .primary-overlay-light .box3 .rokchecks-active {background-image: url(../images/overlays/light/rokinputs.png);}
.inputbox, #form-login .inputbox, #com-form-login .inputbox, #josForm .inputbox, .component-content .search_result .inputbox, .component-content .contact .inputbox, .component-content .user input#email, .component-content .user input#name, .component-content .user input#username, .component-content .user input#password, .component-content .user input#passwd, .component-content .user input#password2, #rokajaxsearch .inputbox {background: url(../images/overlays/global/dark-10.png);box-shadow: 4px 4px 0 rgba(0,0,0,0.2);}
.basic .inputbox, .basic #form-login .inputbox, .basic #rokajaxsearch .inputbox {background: url(../images/overlays/global/light-90.png);box-shadow: 4px 4px 0 rgba(0,0,0,0.1);}
.main-overlay-light.readonstyle-button .main-bg a.readon:hover, .readonstyle-button .page-overlay-light .readon:hover, .main-overlay-light.readonstyle-button #rt-page-surround .box1 .readon:hover, .readonstyle-button .bottomblock-overlay-light .readon:hover, .primary-overlay-light.readonstyle-button .box2 .readon, .primary-overlay-light.readonstyle-button .box3 .readon {background-color: #000;}
.primary-overlay-light.readonstyle-button .main-bg .box2 .readon:hover, .primary-overlay-light.readonstyle-button .main-bg .box3 .readon:hover {background-color: #fff;}
.main-overlay-light.readonstyle-button .main-bg .readon:hover span, .main-overlay-light.readonstyle-button .main-bg .readon:hover .button, .readonstyle-button .page-overlay-light .readon:hover span, .readonstyle-button .page-overlay-light .readon:hover .button, .main-overlay-light.readonstyle-button #rt-page-surround .box1 .readon:hover span, .main-overlay-light.readonstyle-button #rt-page-surround .box1 .readon:hover .button, .bottomblock-overlay-light .readon:hover span, .bottomblock-overlay-light .readon:hover .button, .primary-overlay-light.readonstyle-button .box2 .readon span, .primary-overlay-light.readonstyle-button .box2 .readon .button, .primary-overlay-light.readonstyle-button .box3 .readon span, .primary-overlay-light.readonstyle-button .box3 .readon .button {color: #fff;}
.primary-overlay-light.readonstyle-button .box2 .readon:hover span, .primary-overlay-light.readonstyle-button .box2 .readon:hover .button, .primary-overlay-light.readonstyle-button .box3 .readon:hover span, .primary-overlay-light.readonstyle-button .box3 .readon:hover .button {color: #000;}

/* Typography - DARK */
.main-overlay-dark .main-bg #form-login ul li a, .main-overlay-dark .main-bg .rt-section-list ul li a, .main-overlay-dark .main-bg ul.mostread li a, .main-overlay-dark .main-bg ul.latestnews li a, .main-overlay-dark .main-bg .weblinks ul li a, .main-overlay-dark .main-bg ul.newsfeed li a, .showcaseblock-overlay-dark #rt-feature #form-login ul li a, .showcaseblock-overlay-dark #rt-feature .rt-section-list ul li a, .showcaseblock-overlay-dark #rt-feature ul.mostread li a, .showcaseblock-overlay-dark #rt-feature ul.latestnews li a, .showcaseblock-overlay-dark #rt-feature .weblinks ul li a, .showcaseblock-overlay-dark #rt-feature ul.newsfeed li a, .page-overlay-dark #form-login ul li a, .page-overlay-dark #com-form-login ul li a, .page-overlay-dark ul.rt-more-articles li a, .page-overlay-dark .rt-section-list ul li a, .page-overlay-dark ul.mostread li a, .page-overlay-dark ul.latestnews li a, .page-overlay-dark .weblinks ul li a, .page-overlay-dark ul.newsfeed li a, .page-overlay-dark .component-content .category-list ul li a, .primary-overlay-dark #form-login ul li a, .primary-overlay-dark .rt-section-list ul li a, .primary-overlay-dark ul.mostread li a, .primary-overlay-dark ul.latestnews li a, .primary-overlay-dark .weblinks ul li a, .primary-overlay-dark ul.newsfeed li a, .bottomblock-overlay-dark #form-login ul li a, .bottomblock-overlay-dark .rt-section-list ul li a, .bottomblock-overlay-dark ul.mostread li a, .bottomblock-overlay-dark ul.latestnews li a, .bottomblock-overlay-dark .weblinks ul li a, .bottomblock-overlay-dark ul.newsfeed li a {background-image: url(../images/overlays/dark/arrow-accent.png);}
.bottomblock-overlay-dark #rocket {background: url(../images/overlays/dark/rocket.png) 0 0 no-repeat;}
.bottomblock-overlay-dark #gantry-totop {background-image: url(../images/overlays/dark/to-top.png);}
.main-overlay-dark #rt-accessibility a.small .button, .main-overlay-dark #rt-accessibility a.large .button {background-image: url(../images/overlays/dark/text-buttons.png);}
.showcaseblock-overlay-dark ul.bullet li, .main-overlay-dark ul.bullet li, .page-content-dark ul.bullet li, .bottomblock-overlay-dark ul.bullet li {background-image: url(../images/overlays/light/round-bullet.png);}
.page-content-dark .contentbox1 {background: url(../images/overlays/global/light-10.png);}
.page-content-dark .contentbox2 {background: url(../images/overlays/global/light-15.png);}
.page-content-dark .contentbox3 {background: url(../images/overlays/global/light-20.png);}
.page-content-dark .contentbox4 {background: url(../images/overlays/global/light-25.png);}
.page-content-dark .component-content .rt-articleinfo {border-bottom: 1px dashed #666;}

/* Typography - LIGHT */
.main-overlay-light .main-bg #form-login ul li a, .main-overlay-light .main-bg .rt-section-list ul li a, .main-overlay-light .main-bg ul.mostread li a, .main-overlay-light .main-bg ul.latestnews li a, .main-overlay-light .main-bg .weblinks ul li a, .main-overlay-light .main-bg ul.newsfeed li a, .showcaseblock-overlay-light #rt-feature #form-login ul li a, .showcaseblock-overlay-light #rt-feature .rt-section-list ul li a, .showcaseblock-overlay-light #rt-feature ul.mostread li a, .showcaseblock-overlay-light #rt-feature ul.latestnews li a, .showcaseblock-overlay-light #rt-feature .weblinks ul li a, .showcaseblock-overlay-light #rt-feature ul.newsfeed li a, .page-overlay-light #form-login ul li a, .page-overlay-light #com-form-login ul li a, .page-overlay-light ul.rt-more-articles li a, .page-overlay-light .rt-section-list ul li a, .page-overlay-light ul.mostread li a, .page-overlay-light ul.latestnews li a, .page-overlay-light .weblinks ul li a, .page-overlay-light ul.newsfeed li a, .page-overlay-light .component-content .category-list ul li a, .primary-overlay-light #form-login ul li a, .primary-overlay-light .rt-section-list ul li a, .primary-overlay-light ul.mostread li a, .primary-overlay-light ul.latestnews li a, .primary-overlay-light .weblinks ul li a, .primary-overlay-light ul.newsfeed li a, .bottomblock-overlay-light #form-login ul li a, .bottomblock-overlay-light .rt-section-list ul li a, .bottomblock-overlay-light ul.mostread li a, .bottomblock-overlay-light ul.latestnews li a, .bottomblock-overlay-light .weblinks ul li a, .bottomblock-overlay-light ul.newsfeed li a {background-image: url(../images/overlays/light/arrow-accent.png);}
.bottomblock-overlay-light #rocket {background: url(../images/overlays/light/rocket.png) 0 0 no-repeat;}
.bottomblock-overlay-light #gantry-totop {background-image: url(../images/overlays/light/to-top.png);}
.main-overlay-light #rt-accessibility a.small .button, .main-overlay-light #rt-accessibility a.large .button {background-image: url(../images/overlays/dark/text-buttons.png);}
.page-content-light .contentbox1 {background: url(../images/overlays/global/dark-10.png);}
.page-content-light .contentbox2 {background: url(../images/overlays/global/dark-15.png);}
.page-content-light .contentbox3 {background: url(../images/overlays/global/dark-20.png);}
.page-content-light .contentbox4 {background: url(../images/overlays/global/dark-25.png);}
.page-content-light .component-content .rt-articleinfo {border-bottom: 1px dashed #ccc;}

/* RTL */
.primary-overlay-dark.rtl #rt-logo {background: url(../images/logo/dark/rtl/logo.png) 0 0 no-repeat;}
.primary-overlay-light.rtl #rt-logo {background: url(../images/logo/light/rtl/logo.png) 0 0 no-repeat;}

/*** typography.css ***/

/**
 * @package   Momentum Template - RocketTheme
 * @version   1.5 December 12, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.rockettheme.com/legal/license.php RocketTheme Proprietary Use License
 */
 
/* Content Boxes */
.contentbox1, .contentbox2, .contentbox3, .contentbox4 {padding: 15px;margin-bottom: 15px;border-radius: 3px;}

/* Quote Styles */
blockquote {padding: 5px 0 0 35px;}
blockquote p {padding: 0 35px 5px 0;font-size: 15px;}
.blockquote, .typography-style-dark blockquote {background: url(../images/typography/dark/quote-l.png) 0 0 no-repeat;}
.typography-style-light blockquote {background: url(../images/typography/light/quote-l.png) 0 0 no-repeat;}
.blockquote p, .typography-style-dark blockquote p {background: url(../images/typography/dark/quote-r.png) 100% 100% no-repeat;}
.typography-style-light blockquote p {background: url(../images/typography/light/quote-r.png) 100% 100% no-repeat;}
blockquote.alignleft {width: 30%;float: left;margin: 25px 25px 25px 0;}
blockquote.alignright {width: 30%;float: right;margin: 25px 0 25px 25px;}
blockquote.alignleft p, blockquote.alignright p {background: none;padding: 0;}

/* Pre/Code Styles */
pre {overflow: auto;padding: 17px 15px;margin-bottom: 15px;font-size: 11px;line-height: 17px;cursor: text;}
pre, .typography-style-dark pre {background: #222;border: 1px solid #333;}
.typography-style-light pre {background: #fafafa;border: 1px solid #eaeaea;color: #555;}
pre.lines, .typography-style-dark pre.lines {background: #151515 url(../images/typography/dark/pre-lines.png) 50% 0;border: none;transition: background-color 0.3s linear;}
pre.lines:hover, .typography-style-dark pre.lines:hover {background-color: #202020;}
.typography-style-light pre.lines {background: #eaeaea url(../images/typography/light/pre-lines.png) 50% 0;border: none;color: #333;}

/* Emphasis Styles*/
em.italic {font-style: italic;font-weight: bold;}
em.bold {font-size: 120%;font-weight: bold;line-height: 145%;font-style: normal;}
em.bold2 {font-size: 135%;font-weight: bold;line-height: 120%;font-style: normal;}
em.normal {font-style: normal;}
em.nobold {font-weight: normal;}
em.highlight {font-style: normal;padding: 2px 5px;border-radius: 4px;text-shadow: none;}
em.highlight.white {background: #fff;color: #000;}
.typography-style-light em.highlight.white {background: #ddd;color: #000;}
em.highlight.black {background: #000;color: #fff;}
em.highlight.green {background: #74924A;color: #fff;}
em.highlight.yellow {background: #D69839;color: #fff;}
em.highlight.blue {background: #488AAD;color: #fff;}
em.highlight.red {background: #BD5151;color: #fff;}
.rt-upper {text-transform: uppercase;}
.rt-center {text-align: center;}
.rt-justify {text-align: justify;}

/* Drop Caps */
p.dropcap {overflow: hidden;}
p.dropcap:first-letter {font-size: 300%;margin: 0 5px 0 0;line-height: 110%;float: left;display: inline-block;}
p.dropcap2:first-letter, p.dropcap4:first-letter, p.dropcap6:first-letter {font-size: 30px;margin: 0 10px 10px 0;line-height: 30px;float: left;display: inline-block;border-radius: 30px;padding: 5px 10px;}
p.dropcap3:first-letter, p.dropcap5:first-letter, p.dropcap7:first-letter  {font-size: 30px;margin: 0 10px 10px 0;line-height: 30px;float: left;display: inline-block;padding: 4px 8px;}
p.dropcap2:first-letter {background: #333;}
.typography-style-light p.dropcap2:first-letter {background: #555;color: #fff;}
p.dropcap3:first-letter {background: #333;border: 1px solid rgba(255,255,255,0.1);}
.typography-style-light p.dropcap3:first-letter {background: #555;color: #fff;border: 1px solid #fff;}
p.dropcap4:first-letter {background: #ddd;color: #555;}
p.dropcap5:first-letter {background: #ddd;color: #555;border: 1px solid rgba(255,255,255,0.3);}

/* Inset Styles */
span.inset-left {float: left;margin-right: 20px;}
span.inset-right {float: right;margin-left: 20px;}
span.inset-right-title, span.inset-left-title {display: block;font-size: 100%;font-weight: bold;}
span.inset-left, span.inset-right {display: block;padding: 10px;width: 20%;font-size: 100%;font-style: italic;margin-top: 15px;position: relative;text-align: justify; margin-bottom: 15px;}

/* List and Text Styles - Icons */
ul.list-icon {padding: 0;margin: 0 0 15px 0;background: none;}
ul.list-icon li {list-style: none;padding: 0 0 0 24px;margin: 0 0 5px 0;background-repeat: no-repeat;}
ul.list-icon li a {font-size: 100%;line-height: 1.7em;}
.text-icon {display: inline-block;line-height: 20px;font-size: 110%;background-repeat: no-repeat;padding-left: 24px;}
span.email, span.phone, span.quote, span.cart, span.rss, span.tags, span.write, span.info, span.sound, .email li, .phone li, .quote li, .cart li, .rss li, .tags li, .write li, .info li, .sound li {background-image: url(../images/typography/dark/icon-set1.png);}
.typography-style-light span.email, .typography-style-light span.phone, .typography-style-light span.quote, .typography-style-light span.cart, .typography-style-light span.rss, .typography-style-light span.tags, .typography-style-light span.write, .typography-style-light span.info, .typography-style-light span.sound, .typography-style-light .email li, .typography-style-light .phone li, .typography-style-light .quote li, .typography-style-light .cart li, .typography-style-light .rss li, .typography-style-light .tags li, .typography-style-light .write li, .typography-style-light .info li, .typography-style-light .sound li {background-image: url(../images/typography/light/icon-set1.png);}
span.rightarrow, span.leftarrow, span.circlearrow, span.downarrow, span.uparrow, span.person, span.calendar, span.doc, span.bulb, .rightarrow li, .leftarrow li, .circlearrow li, .downarrow li, .uparrow li, .person li, .calendar li, .doc li, .bulb li {background-image: url(../images/typography/dark/icon-set2.png);}
.typography-style-light span.rightarrow, .typography-style-light span.leftarrow, .typography-style-light span.circlearrow, .typography-style-light span.downarrow, .typography-style-light span.uparrow, .typography-style-light span.person, .typography-style-light span.calendar, .typography-style-light span.doc, .typography-style-light span.bulb, .typography-style-light .rightarrow li, .typography-style-light .leftarrow li, .typography-style-light .circlearrow li, .typography-style-light .downarrow li, .typography-style-light .uparrow li, .typography-style-light .person li, .typography-style-light .calendar li, .typography-style-light .doc li, .typography-style-light .bulb li {background-image: url(../images/typography/light/icon-set2.png);}
span.twitter, span.youtube, span.skype, span.facebook, span.like, span.video, span.dropbox, span.vimeo, span.paypal, .twitter li, .youtube li, .skype li, .facebook li, .like li, .video li, .dropbox li, .vimeo li, .paypal li {background-image: url(../images/typography/dark/icon-set3.png);}
.typography-style-light span.twitter, .typography-style-light span.youtube, .typography-style-light span.skype, .typography-style-light span.facebook, .typography-style-light span.like, .typography-style-light span.video, .typography-style-light span.dropbox, .typography-style-light span.vimeo, .typography-style-light span.paypal, .typography-style-light .twitter li, .typography-style-light .youtube li, .typography-style-light .skype li, .typography-style-light .facebook li, .typography-style-light .like li, .typography-style-light .video li, .typography-style-light .dropbox li, .typography-style-light .vimeo li, .typography-style-light .paypal li {background-image: url(../images/typography/light/icon-set3.png);}
.text-icon.email, .text-icon.rightarrow, .text-icon.twitter, .email li, .rightarrow li, .twitter li {background-position: 0 4px;}
.text-icon.phone, .text-icon.leftarrow, .text-icon.youtube, .phone li, .leftarrow li, .youtube li {background-position: 0 -296px;}
.text-icon.quote, .text-icon.circlearrow, .text-icon.skype, .quote li, .circlearrow li, .skype li {background-position: 0 -596px;}
.text-icon.cart, .text-icon.downarrow, .text-icon.facebook, .cart li, .downarrow li, .facebook li {background-position: 0 -896px;}
.text-icon.rss, .text-icon.uparrow, .text-icon.like, .rss li, .uparrow li, .like li {background-position: 0 -1196px;}
.text-icon.tags, .text-icon.person, .text-icon.video, .tags li, .person li, .video li {background-position: 0 -1496px;}
.text-icon.write, .text-icon.calendar, .text-icon.dropbox, .write li, .calendar li, .dropbox li {background-position: 0 -1796px;}
.text-icon.info, .text-icon.doc, .text-icon.vimeo, .info li, .doc li, .vimeo li {background-position: 0 -2096px;}
.text-icon.sound, .text-icon.bulb, .text-icon.paypal, .sound li, .bulb li, .paypal li {background-position: 0 -2396px;}

/* Standard List Styles */
ul.checkmark, ul.circle-checkmark, ul.square-checkmark, ul.circle-small, ul.circle, ul.circle-large, ul.triangle-small, ul.triangle, ul.triangle-large, ul.bullet {margin: 0 0 25px 0;padding: 0;}
ul.checkmark li, ul.circle-checkmark li, ul.square-checkmark li, ul.circle-small li, ul.circle li, ul.circle-large li, ul.triangle-small li, ul.triangle li, ul.triangle-large li, ul.bullet li {list-style-type: none;padding: 0 0 0 30px;position: relative;font-size: 120%;margin-bottom: 8px;}
ul.checkmark li a, ul.circle-checkmark li a, ul.square-checkmark li a, ul.circle-small li a, ul.circle li a, ul.circle-large li a, ul.triangle-small li a, ul.triangle li a, ul.triangle-large li {font-size: 100%;line-height: 1.7em;}
ul.checkmark li:after {content:"";display:block;width: 4px;height: 8px;border-color: #fff;border-style: solid;border-width: 0 3px 3px 0;position:absolute;left: 14px;top: 3px;-webkit-transform: rotate(45deg);-moz-transform: rotate(45deg);-o-transform: rotate(45deg);}
ul.circle-checkmark li:before {content:"";display: block;border-color: #fff;border-style: solid;border-width: 9px;border-radius: 9px;height: 0;width: 0;position: absolute;left: 7px;top: 1px;}
ul.circle-checkmark li:after {content:"";display:block;width: 3px;height: 6px;border-color: #000;border-width: 0 2px 2px 0;border-style: solid;position:absolute;left: 14px;top: 5px;-webkit-transform: rotate(45deg);-moz-transform: rotate(45deg);-o-transform: rotate(45deg);}
.typography-style-light ul.circle-checkmark li:after {border-color: #fff;}
ul.square-checkmark li:before {content:"";display: block;border-color: #fff;border-width: 7px;border-style: solid;height: 0;width: 0;position: absolute;left: 9px;top: 3px;}
ul.square-checkmark li:after {content:"";display:block;width: 3px;height: 6px;border-color: #000;border-width: 0 2px 2px 0;border-style: solid;position:absolute;left: 14px;top: 9px;margin-top: -4px;-webkit-transform: rotate(45deg);-moz-transform: rotate(45deg);-o-transform: rotate(45deg);}
.typography-style-light ul.square-checkmark li:after {border-color: #fff;}
ul.triangle-small li:after {content:"";display:block;width: 0;height: 0;border-color: transparent transparent transparent #fff;border-style: solid;border-width: 3px;position:absolute;left: 18px;top: 7px;}
ul.triangle li:after {content:"";display:block;width: 0;height: 0;border-color: transparent transparent transparent #fff;border-style: solid;border-width: 4px;position:absolute;left: 17px;top: 6px;}
ul.triangle.green li:after {border-color: transparent transparent transparent #74924A;}
ul.triangle.yellow li:after {border-color: transparent transparent transparent #D69839;}
ul.triangle.red li:after {border-color: transparent transparent transparent #BD5151;}
ul.triangle.blue li:after {border-color: transparent transparent transparent #488AAD;}
ul.triangle.white li:after {border-color: transparent transparent transparent #fff;}
.typography-style-light ul.triangle.white li:after {border-color: transparent transparent transparent #ddd;}
ul.triangle.black li:after {border-color: transparent transparent transparent #000;}
ul.triangle-large li:after {content:"";display:block;width: 0;height: 0;border-color: transparent transparent transparent #fff;border-style: solid;border-width: 6px;position:absolute;left: 15px;top: 4px;}
ul.circle-small li:after {content:"";display:block;width: 0;height: 0;border-color: #fff;border-style: solid;border-width: 2px;border-radius: 2px;position:absolute;left: 16px;top: 8px;}
ul.circle li:after {content:"";display:block;width: 0;height: 0;border-color: #fff;border-style: solid;border-width: 3px;border-radius: 3px;position:absolute;left: 15px;top: 7px;}
ul.circle-large li:after {content:"";display:block;width: 0;height: 0;border-color: #fff;border-style: solid;border-width: 5px;border-radius: 5px;position:absolute;left: 12px;top: 5px;}
ul.circle.green li:after, ul.checkmark.green li:after {border-color: #74924A;}
ul.circle.yellow li:after, ul.checkmark.yellow li:after {border-color: #D69839;}
ul.circle.red li:after, ul.checkmark.red li:after {border-color: #BD5151;}
ul.circle.blue li:after, ul.checkmark.blue li:after {border-color: #488AAD;}
ul.circle.white li:after, ul.checkmark.white li:after {border-color: #fff;}
.typography-style-light ul.circle.white li:after, .typography-style-light ul.checkmark.white li:after {border-color: #ddd;}
ul.circle.black li:after, ul.checkmark.black li:after {border-color: #000;}
ul.bullet li {background-position: 15px 7px;background-repeat: no-repeat;}

/* Notice/System Styles */
.approved, .attention, .alert, .notice {padding: 10px 10px 10px 45px;border-radius: 6px;font-weight: bold;background-position: 10px 50%;background-repeat: no-repeat;}
#system-message dd ul {border-radius: 6px;background-repeat: no-repeat;background-position: 10px 50%;}
.approved a, .attention a, .alert a, .notice a {color: #000 !important;text-decoration: underline;}
.approved a:hover, .attention a:hover, .alert a:hover, .notice a:hover {color: #fff !important;text-shadow: none;}
.approved {background-color: #74924A;background-image: url(../images/typography/dark/approved.png);color: #232F12;text-shadow: 1px 1px 1px rgba(255,255,255,0.3);border-top: 1px solid rgba(255,255,255,0.3);}
.attention, #system-message dd.notice ul {background-color: #D69839; background-image: url(../images/typography/dark/attention.png);color: #5A3800;text-shadow: 1px 1px 1px rgba(255,255,255,0.3);border-top: 1px solid rgba(255,255,255,0.3);}
.alert, #system-message dd.error ul {background-color: #BD5151; background-image: url(../images/typography/dark/alert.png);color: #4C1818;text-shadow: 1px 1px 1px rgba(255,255,255,0.3);border-top: 1px solid rgba(255,255,255,0.3);}
.notice, #system-message dd ul {background-color: #488AAD; background-image: url(../images/typography/dark/notice.png);color: #133548;text-shadow: 1px 1px 1px rgba(255,255,255,0.3);border-top: 1px solid rgba(255,255,255,0.3);}

/*** extensions.css ***/

/**
 * @package   Momentum Template - RocketTheme
 * @version   1.5 December 12, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.rockettheme.com/legal/license.php RocketTheme Proprietary Use License
 */
 
/* Fonts */
.font-family-momentum #rt-slideshow .rg-ss-title .title-1 {font-family: OstrichSansBold,Helvetica,Arial,sans-serif;}
.font-family-momentum #rt-slideshow .rg-ss-title .title-2 {font-family: OstrichSansBlack,Helvetica,Arial,sans-serif;}
.font-family-momentum .rg-detail-item-title {font-family: YanoneKaffeesatzLight,Helvetica,Arial,sans-serif;font-weight: normal;text-transform: uppercase;}

/* RokGallery - Slideshow */
#rt-slideshow {position: absolute;top: 0;left: 0;}
#rt-slideshow .rg-ss-controls .prev, #rt-slideshow .rg-ss-controls .next {position: absolute;width: 34px;height: 34px;top: 30%;margin-top: -17px;background-repeat: no-repeat;cursor: pointer;border-radius: 0;background-color: transparent;}
#rt-slideshow .rg-ss-controls .prev {left: 60px;background-position: 0 0;}
#rt-slideshow .rg-ss-controls .next{right: 60px;background-position: 0 -40px;}
#rt-slideshow .rg-ss-controls .prev:hover {background-position: 0 -80px;}
#rt-slideshow .rg-ss-controls .next:hover {background-position: 0 -120px;}
#rt-slideshow .rg-ss-controls .prev:active, #rt-slideshow .rg-ss-controls .next:active {margin-top: -16px;}
#rt-slideshow .rg-ss-info {width: 30%;left: 110px;text-shadow: none;}
#rt-slideshow .rg-ss-title {font-size: 70px;line-height: 100%;}
#rt-slideshow .rg-ss-caption {text-indent: 0;font-size: 14px;line-height: 130%;margin-top: 5px;}

/* RokStories */
#rt-showcase .feature-block {margin: -5px -15px;}
#rt-showcase .feature-arrow-l, #rt-showcase .feature-arrow-r {position: absolute;width: 34px;height: 34px;top: 50%;margin-top: -17px;background-repeat: no-repeat;cursor: pointer;}
#rt-showcase .feature-arrow-l {left: 0;background-position: 0 0;}
#rt-showcase .feature-arrow-r {right: 0;background-position: 0 -40px;}
#rt-showcase .feature-arrow-l:hover {background-position: 0 -80px;}
#rt-showcase .feature-arrow-r:hover {background-position: 0 -120px;}
#rt-showcase .feature-arrow-l:active, #rt-showcase .feature-arrow-r:active {margin-top: -16px;}
#rt-showcase .feature-desc .desc {width: 40%;font-size: 14px;margin-top: 5px;display: block;}
#rt-showcase .feature-block .desc-container {margin: 15px 55px;}

/* RokAjaxSearch */
#rokajaxsearch .inputbox {height: 18px;line-height: 18px;padding: 4px 5px;font-size: 12px;width: 200px;transition: background-color 0.2s linear;border: none;}
#rokajaxsearch .roksearch-wrapper {background-repeat: no-repeat; background-position: 186px 6px;width: 205px;}
body #roksearch_results span.small {display: inline;position: relative;text-align: inherit;bottom: 0;right: 0;font-size: 11px;font-style: italic;}
body #roksearch_results a.clr {font-size: 100%;}
body .search_options {float: left;margin: 5px 10px 0;}
body .search_options label {margin-right: 15px !important;padding-bottom: 2px;text-transform: lowercase;}
body #roksearch_results {z-index: 10000;position: absolute;width: 325px;float: right;text-align: left;visibility: hidden;border: none;margin: 10px 0 0;}
body .roksearch_wrapper1 {border-radius: 5px;}
body #roksearch_results h3 {margin-top: 0;margin-bottom: 2px;line-height: 110%;font-size: 120%;padding-bottom: 0;}
body #roksearch_results .container-wrapper {overflow: hidden;}
body #roksearch_results .page {float: left;}
body #roksearch_results .roksearch_header, body #roksearch_results .roksearch_row_btm {padding: 5px 10px;line-height: 130%;text-transform: none;font-weight: normal;font-size: 15px;border: 0;}
body #roksearch_results .roksearch_row_btm {border-bottom: none;}
body #roksearch_results .roksearch_even, body #roksearch_results .roksearch_odd {padding: 10px;border-bottom: none;}
body #roksearch_results .roksearch_even span, body #roksearch_results .roksearch_odd span {margin-top: 15px;line-height: 140%;}
body #roksearch_results .roksearch_even span.highlight, body #roksearch_results .roksearch_odd span.highlight {margin-top: 0;}
body #roksearch_results .roksearch_row_btm {overflow: hidden;}
body #roksearch_results .roksearch_row_btm span {line-height: 140%;}
body #roksearch_results .viewall span {line-height: 130%;text-transform: none;}
body #roksearch_results .viewall {float: left;margin: 0 5px;}
body #roksearch_results .estimated_res, body #roksearch_results .pagination_res {font-size: 9px;font-style: italic;text-transform: none;float:left;}
body #roksearch_results .pagination_res {font-style: normal;float: right;font-size: 11px;margin-top: -1px;}
body #roksearch_results .powered-by-google {text-transform: lowercase;font-style: italic;font-size: 9px;}
body #roksearch_results .google-search a#roksearch_link {margin-top: 5px;}
body #roksearch_results .readon {margin-top: 15px;}
body #roksearch_results .readon span, body #roksearch_results .readon .button {font-size: 13px;line-height: 22px;}
body #roksearch_results .google-thumb-image {margin: 0 auto;}
body .searchintro {overflow: hidden;}
body #roksearch_results h3 {padding: 0 0 0 15px;}
#rokajaxsearch #roksearch_search_str.loading {background-image: url(../images/spinner.gif);background-position: 98% 50%;background-repeat: no-repeat;}
body #roksearch_results .roksearch_even, body #roksearch_results .roksearch_row_btm {background: none;border: none;}
body #roksearch_results .arrow-left, body #roksearch_results .arrow-right, body #roksearch_results .arrow-left-disabled, body #roksearch_results .arrow-right-disabled {width: 13px;height: 14px;background-repeat: no-repeat;border: none;}
body #roksearch_results .arrow-left, body #roksearch_results .arrow-left-disabled {background-position: -1px -347px;}
body #roksearch_results .arrow-right, body #roksearch_results .arrow-right-disabled {background-position: -1px -162px;}
body #roksearch_results .arrow-left-disabled, body #roksearch_results .arrow-right-disabled {opacity: 0.2;}
body #roksearch_results a#roksearch_link {display: block;float: right;height: 16px;width: 16px; margin: 8px 8px 0 0;background-repeat: no-repeat;}

/* RokTabs - Main */
.roktabs-wrapper {margin: -15px;padding: 0;border-radius: 4px;position: relative;}
.roktabs {position: relative;}
.roktabs-wrapper p {clear: both;}
.roktabs-wrapper .roktabs-container-inner {position: relative;overflow: hidden;}
.roktabs-wrapper .rt-module-inner {position: relative;}
.roktabs-wrapper .roktabs-container-wrapper {clear: both;position: relative;overflow: hidden;width: 50000px;}
.roktabs-tab1, .roktabs-tab2, .roktabs-tab3, .roktabs-tab4, .roktabs-tab5, .roktabs-tab6, .roktabs-tab7, .roktabs-tab8, .roktabs-tab9, .roktabs-tab10, .roktabs-tab11, .roktabs-tab12, .roktabs-tab13, .roktabs-tab14, .roktabs-tab15, .roktabs-tab16, .roktabs-tab17, .roktabs-tab18, .roktabs-tab19, .roktabs-tab20 {float: left;}
.roktabs-container-wrapper .wrapper {padding: 15px;width: auto;float: none;}
.roktabs-wrapper .roktabs-links {margin: 0;padding: 0 8px;overflow: hidden;position: relative;}
.roktabs-wrapper .active-arrows .roktabs-links {margin: 0 28px;padding: 0;}
.roktabs-wrapper .roktabs-links ul {text-align: left;list-style-type: none;overflow: hidden;margin: 0;padding: 0;}
.roktabs-wrapper .roktabs-links ul li {float: left;margin: 0;text-decoration: none;position: relative;}
.roktabs-wrapper .roktabs-links ul li span {display: block;padding: 10px 12px;font-size: 14px;line-height: 28px;font-weight: normal;transition: color 0.2s ease-out;}
.roktabs-wrapper .roktabs-links ul.roktabs-top li.active span {background-position: 50% -767px;background-repeat: no-repeat;}
.roktabs-wrapper .roktabs-links ul.roktabs-bottom li.active span {background-position: 50% -1304px;background-repeat: no-repeat;}
.roktabs-wrapper .roktabs-scroller {position: absolute;bottom: 0;right: 0;}
.roktabs-wrapper .arrow-next, .roktabs-wrapper .arrow-prev {text-align: center;cursor: pointer;position: absolute;top: 0;height: 30px;width: 28px;margin: 0;background-repeat: no-repeat;}
.roktabs-wrapper .arrow-prev span, .roktabs-wrapper .arrow-next span {display: none;}
.roktabs-wrapper .arrow-next {background-position: 5px 16px;right: 0;}
.roktabs-wrapper .arrow-next:hover {opacity: 0.4;}
.roktabs-wrapper .arrow-prev {background-position: 8px -383px;left: 0;}
.roktabs-wrapper .arrow-prev:hover {opacity: 0.4;}
.roktabs-wrapper .tab-icon {margin-right: 8px;vertical-align: middle;}
.tablocation-top .roktabs-wrapper .active-arrows {background-position: 50% 100%;}
.tablocation-bottom .roktabs-wrapper .active-arrows {background-position: 50% 0;}
.active-arrows .arrow-prev, .active-arrows .arrow-next {background-color: transparent;}

/* RokTwittie */
body #roktwittie .clr {clear: both;}
body #roktwittie .info {margin-top: 5px;margin-left: 58px;}
body #roktwittie .name {margin: 0 0 4px;padding: 0;display: block;padding-top: 3px;}
body #roktwittie .nick {display: inline;margin: 3px 0 0;padding: 2px 0;}
body #roktwittie ul {padding: 0;list-style: none;}
body #roktwittie a {font-weight: normal;font-size: 100%;line-height: 100%;}
body #roktwittie .title {margin-bottom: 0;}
body #roktwittie .loading {text-align: center;height: 16px;}
body #roktwittie .loading span {display: none;}
body #roktwittie .status {margin-bottom: 20px;}
body #roktwittie .header {padding: 4px 8px;overflow: hidden;background: none;}
body #roktwittie .header-wrapper {margin-bottom: 15px;padding: 6px;}
body #roktwittie .status .header .avatar {float: left;}
body #roktwittie .status .header .showavatar {margin-bottom: 5px;background: none;}
body #roktwittie .status .header .name {font-size: 20px;font-weight: normal;line-height: 100%;}
body #roktwittie .status .header .nick {font-size: 13px;}
body #roktwittie .status ul li {clear: both;padding: 0;list-style: none;overflow: hidden;line-height: 150%;}
body #roktwittie .status ul li a {line-height: 150%;}
body #roktwittie .status ul li .title {float: left;width: 55px;text-align: right;}
body #roktwittie .status ul li .content {margin-left: 66px;font-weight: normal;margin-bottom: 0;}
body #roktwittie .user .stats {margin-bottom: 10px;}
body #roktwittie .user .feed a {display: block;float: left;height: 16px;padding-right: 20px;}
body #roktwittie .user .subscribe {margin-top: 5px;}
body #roktwittie .tweets-wrapper {margin-bottom: 20px;}
body #roktwittie .tweets ul, body #roktwittie .query ul {list-style-type: none;margin: 0;padding: 0;}
body #roktwittie .roktwittie-avatar {margin: 5px;margin-left: 0;float: left;padding-left: 0px;padding-top: 12px;}
body #roktwittie a.roktwittie-avatar img {vertical-align: middle;}
body #roktwittie div.roktwittie-infos {font-size: 90%;margin: 0.3em 0;}
body #roktwittie div.roktwittie-infos .roktwittie-date {float: right;font-size: 80%;padding-top: 5px;}
body #roktwittie div.roktwittie-infos span .roktwittie-source {font-style: italic;}
body #roktwittie .roktwittie-surround {margin: 0;padding: 0;}
body #roktwittie.showavatar .roktwittie-surround {margin-left: 61px !important;}
body #roktwittie .roktwittie-m1 {background: none;}
body #roktwittie .roktwittie-m1:hover {opacity: 1.0;}
body #roktwittie .roktwittie-m3, body #roktwittie .roktwittie-b3 {margin: 0;}
body #roktwittie .roktwittie-b3 {height: 0;margin: 0;}
body #roktwittie div.roktwittie-text {padding: 0;overflow: hidden;font-size: 13px;}
body #roktwittie div.roktwittie-text > span {line-height:120%;}
body #roktwittie li {overflow: auto;padding: 0 0 0;margin: 0;}
body #roktwittie .content ul, body #roktwittie .friends_list {margin: 0 0 10px 0;padding: 0;}
body #roktwittie ul li {list-style: none !important;padding: 0px;}
body #roktwittie .status ul li .content.feed {min-height: 20px;}
body #roktwittie .roktwittie-t1, body #roktwittie .roktwittie-t2, body #roktwittie .roktwittie-t3, body #roktwittie .roktwittie-m2, body #roktwittie .roktwittie-m3, body #roktwittie .roktwittie-b1, body #roktwittie .roktwittie-b2, body #roktwittie .roktwittie-b3 {background:none;}
body #roktwittie .roktwittie-t1 {position: relative; width: 11px; height: 12px;top:19px;left:-16px;}
body #roktwittie .roktwittie-avatar img {width: 35px; height: 35px;padding: 8px;}
body #roktwittie .roktwittie-m1 {padding: 0 0 15px 0;}
body #roktwittie .content {padding: 5px;padding-left: 10px;}
body #roktwittie ul li .content {background: none;padding: 0px;}

/* RokNewsPager */
body .roknewspager {margin: 0;position: relative;}
body .roknewspager-li {list-style: none;overflow: auto;}
body .roknewspager-li2 {padding-bottom: 2px;}
body .roknewspager-h3 {background: none;padding: 5px;padding-right: 20px;border: none !important;font-size: 14px;font-weight: bold;margin: 0;position: relative;line-height: 120%;}
body .roknewspager .roknewspager-title {font-weight: normal;}
body .roknewspager-li .roknewspager-content {background-position: 25px 0;background-repeat: no-repeat;}
body .roknewspager-li.active .roknewspager-content {opacity: 1.0;}
body .roknewspager .roknewspager-title {display: inline-block;font-size: 20px;line-height: 24px;font-weight: normal;cursor: pointer;}
body .roknewspager-wrapper {position:relative;overflow:hidden;margin-bottom: 8px;margin: -15px;}
body .roknewspager-content {padding: 15px;}
body .roknewspager-content .readon {margin-top: 10px;}
body .roknewspager-toggle, body .roknewspager-toggle-active {width: 12px;height: 13px;right: 10px;top: 13px;position: absolute;cursor: pointer;background-repeat: no-repeat;z-index: 2;}
body .roknewspager .roknewspager-overlay {width: 11px;height: 11px;right: 10px;top: 9px;position: absolute;cursor: pointer;background-repeat: no-repeat;}
body .roknewspager-toggle:hover {background-position: 0 0;}
body .roknewspager-toggle-active {opacity: 0.3;}
body .roknewspager-toggle-active, body .roknewspager-toggle {transition: opacity 0.2s linear;}
body .roknewspager-pages {margin: 15px -15px -15px -15px;text-align: center;padding: 5px;}
body .roknewspager-pages2 {float: none;}
body .roknewspager-prev, body .roknewspager-prev-disabled, body .roknewspager-next, body .roknewspager-next-disabled {width: 12px;height: 13px;top: 2px;position: absolute;cursor: pointer;background-repeat: no-repeat;}
body .roknewspager-next, body .roknewspager-next-disabled {right: 5px;background-position: 0 0;}
body .roknewspager-prev, body .roknewspager-prev-disabled {left: 5px;background-position: 0 -399px;}
body .roknewspager-next:hover, body .roknewspager-prev:hover {opacity: 1;}
body .roknewspager-next-disabled, body .roknewspager-prev-disabled {opacity: 0.2;cursor: default;}
body .roknewspager-wrapper ul.roknewspager {padding: 0;}
body .roknewspager-wrapper ul li {padding: 15px;margin: 0;list-style: none;}
body .roknewspager-wrapper ul li .roknewspager-div {padding: 0;}
body .roknewspager-spinner {width: 16px;height: 16px;background: transparent url(../images/spinner.gif) no-repeat scroll 0 50%;display: none;position: absolute;bottom: 6px;left: 25px;}
body ul.roknewspager-numbers {margin: 0;padding: 0 24px;overflow: hidden;height: 18px;text-align: center;background: none;}
body ul.roknewspager-numbers li {list-style: none;display: inline-block;cursor: pointer;width: auto;height: auto;padding: 0 2px;opacity: 0.7;line-height: 20px;transition: opacity 0.2s linear;}
body .roknewspager-numbers li.active {cursor: default;background-position: 0 -136px;opacity: 1;}
body .roknewspager-li .roknewspager-h3 {padding: 8px 30px 8px 48px;background-repeat: no-repeat;background-position: 0 50%;}
body .roknewspager-overlay {z-index: 2;}
body .article-rating {float: left;margin: 10px 0;white-space:nowrap;}
body .article-rating .rating-bar {float:left;height:14px;width:104px;background:url(../images/ratingbar.png);text-align:left;}
body .article-rating .rating-bar div {height:14px;background:url(../images/ratingbar.png) 0 -15px;overflow:hidden;}
body .roknewspager-toggle, body .roknewspager-toggle:hover {background-position: 0 0;background-color: transparent !important;}
body .roknewspager-toggle-active, body .roknewspager-toggle-active:hover {background-position: 0 -800px;background-color: transparent !important;}

/* RokNewsFlash */
.roknewsflash li a {font-size: 100%;line-height: 100%;font-weight: normal}

/* RokDownloads */
#rokdownloads .rd_pagination .tab, #rokdownloads .rd_pagination .page-block {display: inline-block;margin: 0 10px;}

/* RokGallery */
#rt-showcase .rokgallery-wrapper .rg-ss-container, .slideshow .rt-block .rokgallery-wrapper .rg-ss-container {margin: -30px -25px;}
.rg-view-header {margin: 0 -5px 15px;padding: 0 15px;}
.rg-view-pagination .prev, .rg-view-pagination .next {font-style: italic;background-repeat: no-repeat;}
.rg-view-pagination .prev {padding-left: 20px;background-position: 0 -399px;}
.rg-view-pagination .prev:hover {background-position: 0 -599px;}
.rg-view-pagination .next {padding-right: 20px;background-position: 100% 1px;}
.rg-view-pagination .next:hover {background-position: 100% -199px;}
.rg-view-pagination-list li a {display: block;width: 9px;height: 9px;background-position: 0 0;background-repeat: no-repeat;}
.rg-view-pagination-list li.active a {background-position: 0 -200px;}
.rg-view-pagination-list li a span {display: none;}
.rg-detail-file-main, .rg-detail-file-info, .rg-detail-slice-info {background-position: 93% 100% !important;}
.rg-detail-info-table-container {margin: 0px -25px 0 -25px;padding: 25px 25px 0 25px;}
.rg-detail-img-bg {padding: 5px 5px 12px !important;border-radius: 0 !important;}
.rg-detail-img, .rg-grid-view .rg-grid-thumb, .rg-list-view .rg-list-thumb {border-radius: 0;}
.rg-detail-item-title {text-shadow: none !important;font-size: 29px;}
.rg-grid-view .tag, .rg-list-view .tag, .rg-detail-slicetag .tag, .rg-detail-filetag .tag {border: 0;border-radius: 0;text-shadow: none;}
.rg-grid-view .rg-grid-thumb, .rg-list-view .rg-list-thumb {transition: box-shadow 0.2s linear;}
ul.rg-view-selector-list li {padding: 6px 6px 7px;}
ul.rg-view-selector-list {margin: -3px 0; padding: 0;}
ul.rg-sort-list li {margin: 0 2px; padding: 0 5px;}
ul.rg-sort-list, #main ul.rg-sort-list {margin: 0;padding: 0;}
.component-content .rg-gm-slice-list {margin: 0;padding: 0;list-style: none;}

/* RokGallery - Grid */
.rokgallery-wrapper .rg-gm-slice-item {border-radius: 0;}
.rokgallery-wrapper .rg-gm-slice {border-radius: 0;}
.rokgallery-wrapper .rg-gm-slice-list {list-style: none;}

/* RokGallery - Slideshow */
.rg-ss-navigation-container {border-radius: 6px;margin-top: 10px;}
.rg-ss-arrow-left {border-radius: 6px 0 0 6px;}
.rg-ss-arrow-right {border-radius: 0 6px 6px 0;}
.rg-ss-arrow-left span, .rg-ss-arrow-right span {width: 10px !important;height: 34px !important;margin-top: -22px !important;}
.rg-ss-thumb, .rg-ss-thumb.active {border: 0 none !important;}
.rg-ss-thumb,.rg-ss-thumb.active {padding: 3px !important;}
.rg-ss-thumb {margin: 0 10px !important;}
.rg-ss-arrow-left span {background-position: -24px 0 !important;}
.rg-ss-arrow-left span:hover {background-position: -24px -36px !important;}
.rg-ss-arrow-right span:hover {background-position: 0 -36px !important;}
#rt-bg-surround .rg-ss-loader {top: 0;opacity: 0.7;border: 0;height: 2px;padding: 4px 0;}
#rt-bg-surround .rg-ss-progress {height: 4px;opacity: 0.9;}

/*** extensions-overlays.css ***/

/**
 * @package   Momentum Template - RocketTheme
 * @version   1.5 December 12, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.rockettheme.com/legal/license.php RocketTheme Proprietary Use License
 */

/* RokGallery - Slideshow - DARK */
#rt-slideshow.showcaseblock-overlay-dark .rg-ss-controls .prev, #rt-slideshow.showcaseblock-overlay-dark .rg-ss-controls .next {background-image: url(../images/overlays/dark/rotator-arrows.png);}

/* RokGallery - Slideshow - LIGHT */
#rt-slideshow.showcaseblock-overlay-light .rg-ss-controls .prev, #rt-slideshow.showcaseblock-overlay-light .rg-ss-controls .next {background-image: url(../images/overlays/light/rotator-arrows.png);}

/* RokGallery - Component - DARK */
.page-content-dark .rg-detail-img-bg, .page-content-dark .rg-grid-view .rg-grid-thumb, .page-content-dark .rg-list-view .rg-list-thumb {background: #444;box-shadow: 3px 3px 0 #222;border: 1px solid #444;}
.page-content-dark .rg-grid-view .rg-grid-thumb:hover, .page-content-dark .rg-list-view .rg-list-thumb:hover {box-shadow: 3px 3px 0 #222;}
.page-content-dark .rg-grid-view .rg-grid:hover {background: #282828;box-shadow: 4px 4px 0 #222;}
.page-content-dark .rg-grid-view .tag, .page-content-dark .rg-grid-view .tag, .page-content-dark .rg-detail-slicetag .tag, .page-content-dark .rg-detail-filetag .tag {box-shadow: 2px 2px 0 #222;}
.page-content-dark .rg-view-header {border-bottom: 1px solid #555;}
.page-content-dark .rg-view-selector-list li.active {background: #333;border-top: 1px solid #555;border-left: 1px solid #555;border-right: 1px solid #555;}

/* RokGallery - Component - LIGHT */
.page-content-light .rg-detail-img-bg, .page-content-light .rg-grid-view .rg-grid-thumb, .page-content-light .rg-list-view .rg-list-thumb {box-shadow: 3px 3px 0 #ddd;border: 1px solid #ddd;}
.page-content-light .rg-grid-view .rg-grid-thumb:hover, .page-content-light .rg-list-view .rg-list-thumb:hover {box-shadow: 3px 3px 0 #bbb;}
.page-content-light .rg-grid-view .rg-grid:hover {box-shadow: 4px 4px 0 #ddd;}
.page-content-light .rg-grid-view .tag, .page-content-light .rg-list-view .tag, .page-content-light .rg-detail-slicetag .tag, .page-content-light .rg-detail-filetag .tag {box-shadow: 2px 2px 0 #ddd;}

/* RokGallery - Grid - DARK */
.page-content-dark .rokgallery-wrapper .rg-gm-slice {background: #444;box-shadow: 3px 3px 0 #222;border: 1px solid #222;}

/* RokGallery - Grid - LIGHT */
.page-content-light .rokgallery-wrapper .rg-gm-slice {box-shadow: 3px 3px 0 #ddd;border: 1px solid #ddd;}

/* RokStories - DARK */
#rt-showcase.showcaseblock-overlay-dark .feature-arrow-l, #rt-showcase.showcaseblock-overlay-dark .feature-arrow-r {background-image: url(../images/overlays/dark/rotator-arrows.png);}

/* RokStories - LIGHT */
#rt-showcase.showcaseblock-overlay-light .feature-arrow-l, #rt-showcase.showcaseblock-overlay-light .feature-arrow-r {background-image: url(../images/overlays/light/rotator-arrows.png);}

/* RokNewsPager - DARK */
.primary-overlay-dark .roknewspager .active .roknewspager-div {background-image: url(../images/overlays/dark/diag-lines.png);}
.primary-overlay-dark .roknewspager-li .roknewspager-h3 {background-image: url(../images/overlays/dark/newspager-title-bar.png);box-shadow: 0 2px 5px rgba(0,0,0,0.2);}
.primary-overlay-dark .roknewspager .roknewspager-toggle {background-image: url(../images/overlays/dark/menu-arrows.png);}
.main-overlay-dark .roknewspager-prev, .main-overlay-dark .roknewspager-prev-disabled, .main-overlay-dark .roknewspager-next, .main-overlay-dark .roknewspager-next-disabled {background-image: url(../images/overlays/dark/menu-arrows.png);}
.main-overlay-dark .roknewspager-li, .main-overlay-dark .roknewspager-pages {background-image: url(../images/overlays/global/light-25.png);}
.main-overlay-dark .roknewspager-li2 {background: url(../images/overlays/dark/horz-line.png) 50% 100% repeat-x;}

/* RokNewsPager - LIGHT */
.primary-overlay-light .roknewspager .active .roknewspager-div {background-image: url(../images/overlays/light/diag-lines.png);}
.primary-overlay-light .roknewspager-li .roknewspager-h3 {background-image: url(../images/overlays/light/newspager-title-bar.png);box-shadow: 0 2px 5px rgba(0,0,0,0.2);}
.primary-overlay-light .roknewspager .roknewspager-toggle {background-image: url(../images/overlays/light/menu-arrows.png);}
.main-overlay-light .roknewspager-prev, .main-overlay-light .roknewspager-prev-disabled, .main-overlay-light .roknewspager-next, .main-overlay-light .roknewspager-next-disabled {background-image: url(../images/overlays/light/menu-arrows.png);}
.main-overlay-light .roknewspager-li, .main-overlay-light .roknewspager-pages {background-image: url(../images/overlays/global/light-10.png);}
.main-overlay-light .roknewspager-li2 {background: url(../images/overlays/light/horz-line.png) 50% 100% repeat-x;}

/* RokTwittie - DARK */
.primary-overlay-dark .ribbon .roktwittie-surround {background: url(../images/overlays/dark/horz-line-dotted.png) 50% 100% repeat-x;}

/* RokTwittie - LIGHT */
.primary-overlay-light .ribbon .roktwittie-surround {background: url(../images/overlays/light/horz-line-dotted.png) 50% 100% repeat-x;}

/* RokTabs - DARK */
.main-overlay-dark .roktabs-wrapper .roktabs-links ul li.active span, .main-overlay-dark .roktabs-wrapper .active-arrows .arrow-prev, .main-overlay-dark .roktabs-wrapper .active-arrows .arrow-next {background-image: url(../images/overlays/dark/menu-arrows.png);}
.main-overlay-dark .roktabs-wrapper .roktabs-links ul li.active {background-image: url(../images/overlays/global/dark-30.png);}
.main-overlay-dark .roktabs-wrapper ul, .main-overlay-dark .roktabs-wrapper .active-arrows {background-image: url(../images/overlays/dark/diag-lines2.png);}

/* RokTabs - LIGHT */
.main-overlay-light .roktabs-wrapper .roktabs-links ul li.active span, .main-overlay-light .roktabs-wrapper .active-arrows .arrow-prev, .main-overlay-light .roktabs-wrapper .active-arrows .arrow-next {background-image: url(../images/overlays/light/menu-arrows.png);}
.main-overlay-light .roktabs-wrapper .roktabs-links ul li.active {background-image: url(../images/overlays/global/dark-30.png);}
.main-overlay-light .roktabs-wrapper ul, .main-overlay-light .roktabs-wrapper .active-arrows {background-image: url(../images/overlays/light/diag-lines2.png);}

/* RokAjaxSearch - DARK */
body.main-overlay-dark #roksearch_results {background-image: url(../images/overlays/global/dark-20.png); background-color: #fff;box-shadow: 4px 4px 0 rgba(0, 0, 0, 0.2);}
body.main-overlay-dark #roksearch_results .roksearch_odd {background-image: url(../images/overlays/global/dark-10.png);}
body.main-overlay-dark #roksearch_results h3 {background: url(../images/overlays/dark/menu-arrows.png) no-repeat;}
body.main-overlay-dark #roksearch_results .roksearch_even, body #roksearch_results .roksearch_odd {border-top: 1px solid rgba(0,0,0,0.1)}
body.main-overlay-dark #rokajaxsearch .roksearch-wrapper {background-image: url(../images/overlays/dark/utility-icons.png);}
body.main-overlay-dark #rokajaxsearch #roksearch_search_str {background-image: url(../images/overlays/global/light-20.png);}
body.main-overlay-dark #roksearch_results a#roksearch_link {background-image: url(../images/overlays/dark/search-close.png);}

/* RokAjaxSearch - LIGHT */
body.main-overlay-light #roksearch_results {background-image: url(../images/overlays/global/dark-10.png); background-color: #fff;box-shadow: 4px 4px 0 rgba(0, 0, 0, 0.2);}
body.main-overlay-light #roksearch_results .roksearch_odd {background-image: url(../images/overlays/global/dark-10.png);}
body.main-overlay-light #roksearch_results h3 {background: url(../images/overlays/dark/menu-arrows.png) no-repeat;}
body.main-overlay-light #roksearch_results .roksearch_even, body #roksearch_results .roksearch_odd {border-top: 1px solid rgba(0,0,0,0.1)}
body.main-overlay-light #rokajaxsearch .roksearch-wrapper {background-image: url(../images/overlays/dark/utility-icons.png);}
body.main-overlay-light #roksearch_results a#roksearch_link {background-image: url(../images/overlays/light/search-close.png);}

/*** demo-styles.css ***/

/**
 * @package   Momentum Template - RocketTheme
 * @version   1.5 December 12, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.rockettheme.com/legal/license.php RocketTheme Proprietary Use License
 */
 
/* Frontpage */
body .rt-fimg1 {margin-left: -23px !important;}
.rotator-text {margin-left: 25%;width: 60%;color: #fff;}
.rotator-line1 {display: block;font-size: 140%;margin-bottom: 5px;padding-left: 5px;text-shadow: 2px 2px 0 rgba(0, 0, 0, 0.5);}
.rotator-line2 {display: block;font-size: 92px;line-height: 90px;letter-spacing: -12px;font-weight: bold;text-shadow: 4px 2px 5px rgba(0, 0, 0, 0.6);margin-left: 5%;}
.rotator-line3 {display: block;text-align: right;font-size: 140%;padding-right: 5px;text-shadow: 2px 2px 0 rgba(0, 0, 0, 0.5);}
#rt-slideshow .title-highlight .rg-ss-title .title-1 {background: url(../images/overlays/global/dark-60.png);display: inline-block;line-height: 80%;margin: -10px -10px 2px -10px;padding: 15px 10px 0 10px;}
#rt-slideshow .title-highlight .rg-ss-title .title-2 {background: url(../images/overlays/global/dark-40.png);display: inline-block;line-height: 80%;margin: 0 -10px 2px -10px;padding: 15px 10px 0 10px;}
#rt-slideshow .title-highlight .rg-ss-caption {background: url(../images/overlays/global/dark-60.png);display: inline-block;margin: 0 -10px -10px -10px;padding: 5px 10px;}
.oct11-home #rt-main-column .block-shadow {margin-bottom: 15px;}
.oct11-home .component-block .rokgallery-wrapper {margin-bottom: -20px !important;}

/* Menu Styles */
.menu-preset {width: 128px;float: left;margin: 0 12px 12px;text-align: center;line-height: 20px;font-family: Helvetica,Arial,sans-serif;}
.menu-preset span {box-shadow: 3px 3px 3px rgba(0,0,0,0.2);}
.menutop li .menu-preset > a > span {display:block;width:128px;height:100px;background:url(../../../images/stories/demo/menu/menu-presets.png);padding:0;margin:0;cursor:pointer;}
.preset2 > a > span {background-position: -138px 0 !important;}
.preset3 > a > span {background-position: -276px 0 !important;}
.preset4 > a > span {background-position: -0 -100px !important;}
.preset5 > a > span {background-position: -138px -100px !important;}
.preset6 > a > span {background-position: -276px -100px !important;}

/* Custom Grid */
.rt-demo-grid-2, .rt-demo-grid-3, .rt-demo-grid-4, .rt-demo-grid-5, .rt-demo-grid-6, .rt-demo-grid-7 {float: left;margin-left: 10px;margin-right: 10px;}
.rt-grid-4 .rt-block .rt-demo-grid-2 {width: 90px;}
.rt-grid-8 .rt-demo-grid-2 {width: 90px;}
.rt-grid-9 .rt-demo-grid-2 {width: 120px;}
.rt-grid-6 .rt-block .rt-demo-grid-3 {width: 190px;}
.rt-grid-8 .rt-demo-grid-3 {width: 175px;}
.rt-grid-9 .rt-demo-grid-3 {width: 200px;}
.rt-grid-8 .rt-demo-grid-4 {width: 270px;}
.rt-grid-9 .rt-demo-grid-4 {width: 313px;}
.rt-grid-8 .rt-demo-grid-6 {width: 440px;}
.rt-demo-grid-alpha {margin-left: 0;}
.rt-demo-grid-omega {margin-right: 0;margin-left: 0;}

/* Tutorials */
.ext-list {line-height: 30px;margin-bottom: 5px;}
.rt-install1-j15, .rt-install1-j17, .rt-install2-j15, .rt-install2-j17 {position: absolute;}
.rt-install1-j15 {bottom: 2px;left: 130px;font-size: 50%;}
.rt-install1-j17 {bottom: 2px;left: 425px;font-size: 50%;}
.rt-install2-j15 {top: 222px;left: 42px;font-size: 70%;color: #0B55C4;}
.rt-install2-j17 {bottom: 3px;left: 345px;font-size: 70%;color: #025A8D;}
.rt-install2-j17a {background: #c00;color: #fff;padding: 2px 4px;}

/* Standard Inline Structural Adjustments */
body .floatleft {float: left;margin-right: 15px;margin-left: 0;}
body .floatright {float: right;margin-left: 15px;margin-right: 0;}
body .normalfont, body .normalfont li {font-size: 100% !important;}
body .nobold, body .nobold li {font-weight: normal !important;}
body .nomargintop, body .nomargintop .rt-block, body ul.nomargintop {margin-top: 0 !important;}
body .nomarginbottom, body .nomarginbottom .rt-block, body ul.nomarginbottom {margin-bottom: 0 !important;}
body .nomarginleft, body .nomarginleft .rt-block, body ul.nomarginleft {margin-left: 0;}
body .nomarginright, body .nomarginright .rt-block, body ul.nomarginright {margin-right: 0;}
body .nopaddingtop, body .nopaddingtop .rt-block, body ul.nopaddingtop {padding-top: 0 !important;}
body .nopaddingbottom, body .nopaddingbottom .rt-block, body ul.nopaddingbottom {padding-bottom: 0 !important;}
body .nopaddingleft, body .nopaddingleft .rt-block, body ul.nopaddingleft {padding-left: 0;}
body .nopaddingright, body .nopaddingright .rt-block, body ul.nopaddingright {padding-right: 0;}
.floatnone {float: none !important;}
.clearnone {clear: none !important;}
.floatleftlast {float: left;margin-right: 0;}
.floatrightlast {float: right;margin-left: 0;}
.noimage {display: none;visibility: hidden;}
body .smallmarginbottom {margin-bottom: 5px !important;}
body .smallmargintop {margin-top: 5px !important;}
body .medmarginbottom {margin-bottom: 10px !important;}
body .medmargintop {margin-top: 10px !important;}
body .largemarginbottom {margin-bottom: 15px !important;}
body .largemargintop {margin-top: 15px !important;}
body .smallmarginleft {margin-left: 5px !important;}
body .smallmarginright {margin-right: 5px !important;}
body .medmarginleft {margin-left: 10px !important;}
body .medmarginright {margin-right: 10px !important;}
body .largemarginleft {margin-left: 15px !important;}
body .largemarginright {margin-right: 15px !important;}
body .smallpaddingbottom {padding-bottom: 5px !important;}
body .smallpaddingtop {padding-top: 5px !important;}
body .medpaddingbottom {padding-bottom: 10px !important;}
body .medpaddingtop {padding-top: 10px !important;}
body .largepaddingbottom {padding-bottom: 15px !important;}
body .largepaddingtop {padding-top: 15px !important;}
body .smallpaddingleft {padding-left: 5px !important;}
body .smallpaddingright {padding-right: 5px !important;}
body .medpaddingleft {padding-left: 10px !important;}
body .medpaddingright {padding-right: 10px !important;}
body .largepaddingleft {padding-left: 15px !important;}
body .largepaddingright {padding-right: 15px !important;}
body .rt-relative {position: relative;}

/* RTL Stuff */
body.rtl .floatleft {float: right;margin-right: 0;margin-left: 15px;}
body.rtl .floatright {float: left;margin-left: 0;margin-right: 15px;}
body.rtl .nomarginleft, body.rtl .nomarginleft .rt-block, body.rtl ul.nomarginleft {margin-right: 0;}
body.rtl .nomarginright, body.rtl .nomarginright .rt-block, body.rtl ul.nomarginright {margin-left: 0;}
body.rtl .nopaddingleft, body.rtl .nopaddingleft .rt-block, body.rtl ul.nopaddingleft {padding-right: 0;}
body.rtl .nopaddingright, body.rtl .nopaddingright .rt-block, body.rtl ul.nopaddingright {padding-left: 0;}
body.rtl .floatleftlast {float: right;margin-left: 0;}
body.rtl .floatrightlast {float: left;margin-right: 0;}
body.rtl .smallmarginleft {margin-right: 5px;}
body.rtl .smallmarginright {margin-left: 5px;}
body.rtl .medmarginleft {margin-right: 10px;}
body.rtl .medmarginright {margin-left: 10px;}
body.rtl .largemarginleft {margin-right: 15px;}
body.rtl .largemarginright {margin-left: 15px;}
body.rtl .smallpaddingleft {padding-right: 5px;}
body.rtl .smallpaddingright {padding-left: 5px;}
body.rtl .medpaddingleft {padding-right: 10px;}
body.rtl .medpaddingright {padding-left: 10px;}
body.rtl .largepaddingleft {padding-right: 15px;}
body.rtl .largepaddingright {padding-left: 15px;}
body.rtl .rt-demo-grid-2, body.rtl .rt-demo-grid-3, body.rtl .rt-demo-grid-4, body.rtl .rt-demo-grid-5, body.rtl .rt-demo-grid-6, body.rtl .rt-demo-grid-7 {float: right;}

/*** template.css ***/

/**
 * @package   Momentum Template - RocketTheme
 * @version   1.5 December 12, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.rockettheme.com/legal/license.php RocketTheme Proprietary Use License
*/

div.rg-ss-info{ color: #ffffff;}
div#rt-body-surround {margin-top:280px}
ul.menutop li.root > .item {font-size: 12px;}

/* Core */
html {margin-bottom: 1px;height: 100%;}
body {height: 100%;margin-bottom: 1px;font-family: Helvetica,Arial,sans-serif;}
body.component-body {min-width: 320px;}
a {font-weight: bold;transition: color 0.2s ease-out;}
h1.title {margin-top: 0;}
h1, h2, .module-title h2, h3, h4, h5 {line-height: 1.1em;letter-spacing: inherit;}
h5 {text-transform: uppercase;font-size: 100%;}
.rt-container, #rt-sidebar-a, #rt-sidebar-b, #rt-sidebar-c {background: transparent;}
.rt-container {position: relative;}
.rt-block {margin: -5px 0;}
ul {list-style-image: none;}
ul.menu {padding-left: 0;}
#rt-content-top, #rt-content-bottom {clear: both;overflow: visible;}
#rt-debug {padding-top: 20px;position: relative;}
#rt-bg-surround {overflow: hidden;position: relative;}
.module-title .title a {font-weight: normal;}
#rt-header, #rt-showcase, #rt-feature, #rt-utility, #rt-maintop, #rt-mainbottom, #rt-content-top, #rt-content-bottom, #rt-bottom, #rt-footer, .rt-block {position: relative;}

/* Fonts */
@font-face {font-family: 'OstrichSansBlack';src: url('../fonts/ostrich-black-webfont.eot');src: url('../fonts/ostrich-black-webfont.eot?#iefix') format('embedded-opentype'),url('../fonts/ostrich-black-webfont.woff') format('woff'),url('../fonts/ostrich-black-webfont.ttf') format('truetype'),url('../fonts/ostrich-black-webfont.svg#OstrichSansBlack') format('svg');font-weight: normal;font-style: normal;}
@font-face {font-family: 'OstrichSansBold';src: url('../fonts/ostrich-bold-webfont.eot');src: url('../fonts/ostrich-bold-webfont.eot?#iefix') format('embedded-opentype'),url('../fonts/ostrich-bold-webfont.woff') format('woff'),url('../fonts/ostrich-bold-webfont.ttf') format('truetype'),url('../fonts/ostrich-bold-webfont.svg#OstrichSansBold') format('svg');font-weight: normal;font-style: normal;}
@font-face {font-family: 'YanoneKaffeesatzLight';src: url('../fonts/YanoneKaffeesatz-Light-webfont.eot');src: url('../fonts/YanoneKaffeesatz-Light-webfont.eot?#iefix') format('embedded-opentype'),url('../fonts/YanoneKaffeesatz-Light-webfont.woff') format('woff'),url('../fonts/YanoneKaffeesatz-Light-webfont.ttf') format('truetype'),url('../fonts/YanoneKaffeesatz-Light-webfont.svg#YanoneKaffeesatzLight') format('svg');font-weight: normal;font-style: normal;}
.font-family-momentum h3, .font-family-momentum .menutop li > .item, .font-family-momentum .module-title .title, .font-family-momentum .rt-date-feature, .font-family-momentum #rt-accessibility .rt-desc, .font-family-momentum .rt-date-feature span, .font-family-momentum #rt-popup-button .buttontext, .font-family-momentum .module-content ul.menu li a, .font-family-momentum .module-content ul.menu li .separator, .font-family-momentum .module-content ul.menu li .item, .font-family-momentum .roknewspager .roknewspager-title, .readonstyle-button.font-family-momentum .readon span, .readonstyle-button.font-family-momentum .readon .button {font-family: YanoneKaffeesatzLight,Helvetica,Arial,sans-serif;font-weight: normal;text-transform: uppercase;}
.font-family-momentum .feature-block .feature-title, .font-family-momentum #rt-showcase .promo-title span {font-family: OstrichSansBlack,Helvetica,Arial,sans-serif;}
.font-family-momentum #rt-showcase .promo-title {font-family: OstrichSansBold,Helvetica,Arial,sans-serif;}
.readonstyle-link.font-family-momentum .readon {font-family: inherit;}
.font-family-momentum .menutop li .item em {font-family: Helvetica,Arial,sans-serif;}
.font-family-momentum h3 {font-size: 190%;}


/* Backgrounds */
.grad-bottom, .pattern-gradient {position: absolute;right: 0;left: 0;bottom: 0;height: 250px;}
#rt-bg-pattern {position: absolute;width: 100%;height: 500px;top: 0;left: 0;}

/* Page Surround */
#rt-page-surround {margin: 0 -50px;padding: 0 50px 25px 50px;position: relative;overflow: hidden;}

/* Topbar */
#rt-topbar {margin: 30px 0;}

/* Logo */
#rt-logo-surround {float: left;position: relative;z-index: 1000;width: 180px;height: 45px;}
#rt-logo {display: block;width: 200px;height: 60px;margin: -8px 0 0 -20px;position: relative;z-index: 1000;}
.centered #rt-logo {margin: 0 auto;}
#rt-logo-surround .logo-block {margin: 0;padding: 0;}

/* Navigation */
.logo-enabled-1 #rt-navigation {margin-left: 180px;}
.rt-menubar {display: inline-block;position: relative;margin: 0;}
#rt-navigation .rt-fusionmenu, #rt-navigation .rt-splitmenu {height: 45px;display: block;}
.menu-block {margin: 0;padding: 0;}

/* Showcase */
#rt-showcase {position: relative;}
#rt-showcase .promo .rt-block {padding: 15px 60px;}
#rt-showcase .promo-title {font-size: 70px;line-height: 100%;}
#rt-showcase .promo-title span {display: block;line-height: 80%;}
#rt-showcase .promo .desc {width: 40%;font-size: 14px;margin-top: 5px;display: block;}
#rt-showcase .promo .arrow-left, #rt-showcase .promo .arrow-right {position: absolute;width: 34px;height: 34px;top: 50%;margin-top: -17px;background-repeat: no-repeat;cursor: pointer;}
#rt-showcase .promo .arrow-left {left: 0;background-position: 0 0;}
#rt-showcase .promo .arrow-right {right: 0;background-position: 0 -40px;}
#rt-showcase .promo .arrow-left:hover {background-position: 0 -80px;}
#rt-showcase .promo .arrow-right:hover {background-position: 0 -120px;}
#rt-showcase .promo .arrow-left:active, #rt-showcase .promo .arrow-right:active {margin-top: -16px;}

/* Content Top - Content Bottom */
#rt-content-top {margin-top: 15px;}
#rt-content-bottom {margin-bottom: 15px;}
#rt-content-top .rt-block, #rt-content-bottom .rt-block {margin: 0;}
#rt-content-top .ribbon .module-surround {width: 192px;}
#rt-content-top .rt-alpha .ribbon, #rt-content-bottom .rt-alpha .ribbon {margin: 0 20px 0 -30px;}
#rt-content-top .rt-omega .ribbon, #rt-content-bottom .rt-omega .ribbon {margin: 0 -30px 0 20px;}
#rt-content-top .ribbon .rt-block, #rt-content-bottom .ribbon .rt-block {margin-top: -30px;margin-bottom: 0;padding: 15px 45px;background: none !important;}
#rt-content-top .ribbon .module-surround, #rt-content-bottom .ribbon .module-surround {padding: 15px 15px 50px 15px;position: relative;}
.ribbon-l, .ribbon-r {width: 111px;height: 26px;position: absolute;bottom: 0;background-repeat: no-repeat;}
.ribbon-l {left: 0;background-position: 100% 0;}
.ribbon-r {right: 0;background-position: 0 0;}
#rt-content-top .wide, #rt-content-bottom .wide {width: 370px;}
#rt-content-top .ribbon .module-title, #rt-content-bottom .ribbon .module-title {padding-left: 35px;background-position: 0 2px;background-repeat: no-repeat;}

/* Modules */
.module-title {margin-bottom: 20px;position: relative;}
.module-title .title {margin: 0;padding: 0;font-size: 190%;}
.module-title .title span {opacity: 0.8;}

/* Module Variations - Title */
.component-content .title {padding: 10px 0;font-size: 260%;line-height: 110%;letter-spacing: normal;}
.component-content h1.title, .component-content h2.title {font-size: 260%;line-height: 110%;letter-spacing: normal;}
.component-content .title span, .rt-article-links h3.title span {opacity: 0.7;}
.rt-leading-articles .module-title .pointer {content: '';position: absolute;top: 15px;width: 0;height: 0;border-top: 12px solid transparent;border-bottom: 12px solid transparent;}
.mb7-sa5 .module-title .pointer, .mb8-sa4 .module-title .pointer, .mb9-sa3 .module-title .pointer, .mb6-sa6 .module-title .pointer, .mb4-sa4-sb4 .module-title .pointer, .mb6-sa3-sb3 .module-title .pointer, .mb8-sa2-sb2 .module-title .pointer, .mb3-sa3-sb3-sc3 .module-title .pointer, .mb4-sa2-sb3-sc3 .module-title .pointer, .mb4-sa3-sb2-sc3 .module-title .pointer, .mb4-sa3-sb3-sc2 .module-title .pointer, .mb6-sa2-sb2-sc2 .module-title .pointer {left: -75px;border-width: 12px;border-left-style: solid;}
.sa5-mb7 .module-title .pointer, .sa4-mb8 .module-title .pointer, .sa3-mb9 .module-title .pointer, .sa6-mb6 .module-title .pointer, .sa4-sb4-mb4 .module-title .pointer, .sa3-sb3-mb6 .module-title .pointer, .sa2-sb2-mb8 .module-title .pointer, .sa3-sb3-sc3-mb3 .module-title .pointer, .sa3-sb3-sc2-mb4 .module-title .pointer, .sa3-sb2-sc3-mb4 .module-title .pointer, .sa2-sb3-sc3-mb4 .module-title .pointer, .sa2-sb2-sc2-mb6 .module-title .pointer {right: -75px;border-width: 12px;border-right-style: solid;}
.title1 h2.title {padding: 0 0 0 18px;}
.title1 .module-title {background-repeat: no-repeat;background-position: 0 6px;}
.title2 h2.title {padding: 10px 15px;}
.title2 .module-title {margin: -15px -15px 15px -15px;}
.title2 .accent {border-top: 12px solid;border-left: 12px solid transparent !important; border-right: 12px solid transparent !important;content: "";height: 0; width: 0;position: absolute;left: 13px;}
.title3 .module-title {margin: -10px -5px 20px -5px;}
.title3 .title {margin: -5px;padding: 5px;}

/* Module Standard Variations */
.basic .rt-block {background: none !important;}
.basic .block-shadow {box-shadow: none !important;}
.standardcase .module-title .title {text-transform: inherit;}
.lowercase .module-title .title {text-transform: lowercase;}
.uppercase .module-title .title {text-transform: uppercase;}
.flush .rt-block {padding: 0;}
.flushtop .rt-block {padding-top: 0;}
.flushbottom .rt-block {padding-bottom: 0;}
#rt-bg-surround .rounded .rt-block {border-radius: 6px;}
#rt-bg-surround .square .rt-block {border-radius: 0;}
#rt-popup, #rt-popuplogin {display: none;}

/* Side Menus */
.module-content .rt-menubar {display: block;margin: 0;float: none;position: relative;height: auto;background: none;border: 0;}
.module-content ul.menu {margin: -15px;}
.module-content ul.menu li {list-style: none;padding: 0;margin: 0;background-position: 0 0;background-repeat: repeat-x;}
.rt-block .module-content ul.menu li li, #rt-body-surround .module-content ul.menu li li {background: none;margin: 0;padding: 0;border: none;}
.module-content ul.menu li > a, .module-content ul.menu li > .separator, .module-content ul.menu li > .item {padding: 0;display: block;}
.module-content ul.menu li a span, .module-content ul.menu li .separator, .module-content ul.menu li .item span {padding: 10px 25px;font-size: 17px;line-height: 17px;display: block;font-weight: normal;background-position: 6px 12px;background-repeat: no-repeat;}
.module-content ul.menu li li a, .module-content ul.menu li li .separator, .module-content ul.menu li li .item, #rt-body-surround .module-content ul.menu li li a, #rt-body-surround .module-content ul.menu li li .separator, #rt-body-surround .module-content ul.menu li li .item {background: none;}
.module-content ul.menu li li a span, .module-content ul.menu li li .separator, .module-content ul.menu li li .item span {font-size: 13px;padding: 4px 10px 4px 25px;}
.module-content ul.menu li > a {padding: 4px 0 0 0;}
.module-content ul.menu li .separator {display: block;}
.module-content ul.menu ul {padding: 10px 0 15px 15px;margin: 0;}
.module-content ul.menu ul ul {background: none;}
.module-content ul.menu .subtext em {line-height: 12px;padding-left: 0;}
.module-content ul.menu em {display: block;font-size: 80%;font-style: normal;font-weight: normal;}
.module-content ul.menu .image img {float: left;}
.module-content ul.menu .item.image {background: none;padding-left: 8px;}
.module-content ul.menu .daddyicon, .module-content ul.menu .arrow-indicator {display: none !important;}
.module-content ul.menu li.parent li a span, .module-content ul.menu li.parent li .item span, .module-content ul.menu li.parent li .separator span {padding-bottom: 1px;background-position: 5px 5px;background-repeat: no-repeat;}
.module-content ul.menu li.parent li.active > a > span, .module-content ul.menu li.parent li.active > .item > span, .module-content ul.menu li.parent li.active > .separator > span {background-position: 5px -310px;background-repeat: no-repeat;}

/* Buttons and Inputs */
.readon {display: inline-block;margin: 0;overflow: visible;border-radius: 5px;transition: background-color 0.2s linear;border: none;}
.readon span, .readon .button {display: block;margin: 0;padding: 7px 10px;cursor: pointer;line-height: 16px;font-size: 16px;overflow: visible;font-weight: normal;position: relative;white-space: normal;background-color: transparent;border: none;letter-spacing: normal;transition: color 0.2s linear;}
.readon span span {display: inline;background: none;margin-left: 0;padding: 0;float: none;}
.readon:active {margin-top: 1px;margin-bottom: -1px;}
.readonstyle-link .readon {background: none !important;border: none !important;padding: 0 !important;margin: 0 !important;height: auto !important;box-shadow: none !important;}
.readonstyle-link .readon .button, .readonstyle-link .readon span {background: none !important;border: none !important;font-size: 13px !important;outline: none !important;padding: 0 !important;margin: 0 !important;line-height: 14px !important;font-weight: normal !important;text-transform: none;height: auto !important;text-shadow: none !important;}
.rokchecks, .rokradios {padding: 3px 0 3px 18px;line-height: 130%;cursor: pointer;}
.rokradios {background-position: 0 4px;background-repeat: no-repeat;}
.rokradios-active {background-position: 0 -146px;background-repeat: no-repeat;}
.rokchecks {background-position: 0 -295px;background-repeat: no-repeat;}
.rokchecks-active {background-position: 0 -446px;background-repeat: no-repeat;}
/*.register-buttons .readon {display: block;float: left;}
.register-buttons a.readon {margin-left: 4px;}*/
.register-buttons .readon .button, .register-buttons .readon span {display: inline-block !important;}

/* Typography */
.inputbox, #form-login .inputbox, #com-form-login .inputbox, #josForm .inputbox, .component-content .search_result .inputbox, .component-content .contact .inputbox, .component-content .user input#email, .component-content .user input#name, .component-content .user input#username, .component-content .user input#password, .component-content .user input#passwd, .component-content .user input#password2, #rokajaxsearch .inputbox {position: relative;z-index: 100;padding: 5px 4px;border: none;}
#rt-page-surround .rt-sidebar-surround .inputbox {border: none;}
.component-content .rt-article-icons a {display: block;text-align: center;}

/* Branding */
#developed-by {overflow: hidden;}
#rocket {display: block;width: 120px;height: 18px;vertical-align: middle;margin: 0;float: right;}

/* Copyright */
#rt-copyright {margin-top: 10px;text-align: inherit;}
#rt-copyright .rt-block {margin: 0;padding: 10px 15px;}
.copytext {padding: 0;margin: 0;font-size: 11px;line-height: 15px;display: block;text-align: left;}

/* Breadcrumbs */
#rt-breadcrumbs .rt-block {margin: 0;padding: 10px 20px;}
#breadcrumbs h3, .leading_separator {display: none;}
.rt-breadcrumb-surround {position: relative;overflow: hidden;padding: 0;height: auto;}
span.breadcrumbs, #rt-breadcrumbs .breadcrumbs {display: inline-block;font-size: 120%;overflow: hidden;}
span.breadcrumbs img {float:left;margin-top: 6px;}
#rt-breadcrumbs img[src $="arrow.png"] {float:left;height: 0;width: 6px;margin-top: 6px;padding-top: 12px;background: url(../images/arrow.png);}
span.breadcrumbs a, span.no-link, span.showHere, .pathway {padding: 5px;margin: 0;float: left;display: block;line-height: 100%;}

/* Date Feature */
.rt-date-feature span {font-size: 120%;line-height: 130%;padding: 2px 0;}

/* Fontsizer Feature */
#rt-accessibility .rt-desc {font-size: 120%;}

/* Popup/Login Module Feature */
.loginbutton-block, .popupbutton-block {margin: 0;}
#rt-login-button .buttontext, #rt-popup-button .buttontext {font-size: 130%;line-height: 130%;font-weight: normal;}
#rt-popup .module-content, #rt-popuplogin .module-content {margin: 0;}
#rt-popup .rt-block, #rt-popuplogin .rt-block {margin: 0;padding: 0;}
#rt-popup .title, #rt-popuplogin .title {padding: 0;margin-bottom: 15px;margin-top: 0;}
#rt-popuplogin p {float: left;margin-bottom: 10px;}
#rt-popuplogin p:first-child {margin-right: 10px;}
#rt-popuplogin .readon {float: right;}
#rt-popuplogin ul li {float: none;display: inline-block;margin-right: 10px;}
#rt-popuplogin .inputbox {border: none;}

/* ToTop Feature */
.totop-block {text-align: center;}
#gantry-totop {font-size: 110%;line-height: 110%;font-weight: normal;padding: 4px 0 4px 30px;background-position: 0 1px;background-repeat: no-repeat;}
#gantry-totop:hover {background-position: 0 -26px;}

/* Custom Content */
img.feature-img {width: 212px;height: 150px;padding: 5px;}
.feature-img-surround {width: 222px;height: 178px;}
.image-block-surround {width: 160px;height: 150px;float: left;margin-right: 15px;}
img.image-block {width: 150px;height: 122px;padding: 5px;position: relative;}
.content-block {overflow: hidden;}
.number-image {display: inline-block;float: left;margin-right: 15px;margin-bottom: 15px;padding: 5px;position: relative;}
.number-image-text {display: block;text-transform: uppercase;font-weight: bold;font-size: 20px;padding-top: 10px;margin: 0 0 -8px -5px;}
.heading1 {font-size: 160%;display: block;margin-bottom: 15px;}

/* iPhone */
body #gantry-viewswitcher {z-index: 100;right:50%;position:relative;top:inherit;right:inherit;margin: 1em auto;}


div#slideshow-spacer {min-height: 150px; max-height: 150px;}

/*** template-gecko.css ***/

/**
 * @package   Momentum Template - RocketTheme
 * @version   1.5 December 12, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.rockettheme.com/legal/license.php RocketTheme Proprietary Use License
 */
 
/* Structure */
body {text-rendering: optimizeSpeed;}
.rt-section-surround {float: left;}


/* Typography */
.rokradios, .rokchecks {line-height: 140%;}
#more-articles span {line-height: 24px;}
.inputstyling-enabled-1 .rt-pollrow input {left: 0 !important;visibility: hidden;}
p.dropcap2:first-letter, p.dropcap4:first-letter, p.dropcap6:first-letter {padding: 10px 10px;}
#search_searchword {width: 144px;}
.readon input.button {margin: -3px;}
.readon button.button {margin: -1px;}

/* Shadows */
.page-overlay-dark .block-shadow, .page-overlay-dark .component-block {-moz-box-shadow: 4px 4px 0 rgba(0,0,0,0.8);}
.page-overlay-light .block-shadow, .page-overlay-light .component-block, .basic .inputbox, .basic #form-login .inputbox, .basic #rokajaxsearch .inputbox {-moz-box-shadow: 4px 4px 0 rgba(0,0,0,0.1);}
.inputbox, #form-login .inputbox, #com-form-login .inputbox, #josForm .inputbox, .component-content .search_result .inputbox, .component-content .contact .inputbox, .component-content .user input#email, .component-content .user input#name, .component-content .user input#username, .component-content .user input#password, .component-content .user input#passwd, .component-content .user input#password2, #rokajaxsearch .inputbox {-moz-box-shadow: 4px 4px 0 rgba(0,0,0,0.2);} 
.page-content-dark .rg-detail-img-bg, .page-content-dark .rg-grid-view .rg-grid-thumb, .page-content-dark .rg-list-view .rg-list-thumb {-moz-box-shadow: 3px 3px 0 #222;}
.page-content-dark .rg-grid-view .rg-grid-thumb:hover, .page-content-dark .rg-list-view .rg-list-thumb:hover {-moz-box-shadow: 3px 3px 0 #222;}
.page-content-dark .rg-grid-view .rg-grid:hover {-moz-box-shadow: 4px 4px 0 #222;}
.page-content-dark .rg-grid-view .tag, .page-content-dark .rg-grid-view .tag, .page-content-dark .rg-detail-slicetag .tag, .page-content-dark .rg-detail-filetag .tag {-moz-box-shadow: 2px 2px 0 #222;}
.page-content-light .rg-detail-img-bg, .page-content-light .rg-grid-view .rg-grid-thumb, .page-content-light .rg-list-view .rg-list-thumb {-moz-box-shadow: 3px 3px 0 #ddd;}
.page-content-light .rg-grid-view .rg-grid-thumb:hover, .page-content-light .rg-list-view .rg-list-thumb:hover {-moz-box-shadow: 3px 3px 0 #bbb;}
.page-content-light .rg-grid-view .rg-grid:hover {-moz-box-shadow: 4px 4px 0 #ddd;}
.page-content-light .rg-grid-view .tag, .page-content-light .rg-list-view .tag, .page-content-light .rg-detail-slicetag .tag, .page-content-light .rg-detail-filetag .tag {-moz-box-shadow: 2px 2px 0 #ddd;}
.page-content-dark .module-content .rg-gm-slice {-moz-box-shadow: 3px 3px 0 #222;}
.page-content-light .module-content .rg-gm-slice {-moz-box-shadow: 3px 3px 0 #ddd;}
.primary-overlay-dark .roknewspager-li .roknewspager-h3, .primary-overlay-light .roknewspager-li .roknewspager-h3 {-moz-box-shadow: 0 2px 5px rgba(0,0,0,0.2);}
body #roksearch_results, .menutop .fusion-submenu-wrapper {-moz-box-shadow: 4px 4px 0 rgba(0, 0, 0, 0.2);}

/* Transitions */
a, .readon span, .readon .button {-moz-transition: color 0.2s linear;}
.menutop li.root, .rt-splitmenu .menutop li, .roknewspager .roknewspager-h3, .roknewspager .roknewspager-div, .readon, #rokajaxsearch .inputbox, .readon {-moz-transition: background-color 0.2s linear;}
body .roknewspager-next, body .roknewspager-prev {-moz-transition: opacity 0.2s linear;}
body .roknewspager-toggle-active, body .roknewspager-toggle, body ul.roknewspager-numbers li {-moz-transition: opacity 0.2s linear;}
.roktabs-wrapper .roktabs-links ul li span {-moz-transition: color 0.2s ease-out;}
.rg-grid-view .rg-grid .rg-grid-thumb-img {-moz-transition: opacity 0.2s linear;}
.rg-grid-view .rg-grid-thumb, .rg-list-view .rg-list-thumb {-moz-transition: box-shadow 0.2s linear;}
.menutop li.root, pre.lines, .typography-style-dark pre.lines {-moz-transition: background-color 0.3s linear;}
.component-content .rt-article-icons .icon {-moz-transition: background-color 0.3s ease-out;}
#form-login ul li a, #com-form-login ul li a, ul.rt-more-articles li a, .rt-section-list ul li a, ul.mostread li a, ul.latestnews li a, .weblinks ul li a, #rt-popuplogin ul li a,ul.newsfeed li a {-moz-transition: background-position-x 0.2s linear;}

/* load transition */
#rt-transition.rt-hidden {opacity: 0;}
#rt-transition.rt-visible {opacity: 1;-moz-transition: opacity 0.4s ease-in;transition: opacity 0.4s ease-in;}

/* Demo Presets */
.menu-preset span {-moz-box-shadow: 3px 3px 3px rgba(0,0,0,0.2);}
body .menutop .type-module ol li .fusion-module {overflow: visible;}
.menu-preset a span {-moz-transition: all 0.3s ease-in-out;-moz-animation-iteration-count: 1;position:relative;z-index:3000;-moz-transform: scale(1); cursor:pointer;}
.menu-preset a:hover span {-moz-transition: all 0.3s ease-in-out;-moz-animation-iteration-count: 1;position:relative;z-index:3000;-moz-transform: scale(1.1); cursor:pointer;}

/* Additional Fixes */
.roktabs-wrapper .roktabs-links ul li span {-moz-user-select: none;}
.component-content .contact #emailForm button {padding: 1px 0;}


/*** fusionmenu.css ***/

/**
 * @package   Momentum Template - RocketTheme
 * @version   1.5 December 12, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.rockettheme.com/legal/license.php RocketTheme Proprietary Use License
 */
/* Global */
.menutop {z-index:1;}
.menutop li {height: auto;margin: 0;padding: 0;position: relative;list-style: none;}
.menutop em {font-size: 80%;font-weight: normal;display: block;font-style: normal;}
.menutop li .item, .menutop li.active .item {display: block;margin: 0;text-decoration: none;float: none;width: auto;}
.menutop li .fusion-submenu-wrapper {float: none;left: -999em;position: absolute;z-index: 500;}
.menutop li:hover li ul, .menutop li.sfHover li ul {top: -999em;}
.menutop li:hover ul, .menutop li.sfHover ul {top: 0;}

/* Root Items */
.menutop {list-style: none;margin: 0;padding: 0;position: relative;display: inline-block;}
.menutop li.root {float: left;margin: 0 0 0 1px;padding: 0;transition: background-color 0.3s linear;}
.menutop li.root > .item {white-space: nowrap;display: block;font-size: 1.5em;z-index: 100;cursor: pointer;position: relative;padding: 0;margin: 0;outline: none;font-weight: normal;letter-spacing: 1px;}
.menutop li.root > span.item {cursor: default;}
.menutop li.root .subtext {line-height: 24px;}
.menutop li.root > .item span {display: block;outline: none;padding: 0 10px;height: 45px;line-height: 45px;width: auto;}
.menutop li.parent.root .item span .daddyicon, .menutop li.root .daddy.item .daddyicon {width: 12px;height: 9px;position: absolute;right: -7px;margin-left: -3px;bottom: 18px;padding: 0;background-position: 0 -801px;background-repeat: no-repeat;}
.menutop li.parent.root {padding: 0 15px 0 0;}
.menutop li.root > .subtext span {padding: 0 10px;line-height: 35px;}
.menutop li.root > .item em {font-size: 10px;display: block;font-style: normal;line-height: 100%;text-align: left;text-transform: none;letter-spacing: normal;margin-top: -8px;}
.menutop li.root > .item img {margin: 12px 10px 12px 0px;}
.menutop li.root > .f-submenu-pad {padding: 0;}
.menutop li.active.root > .item span, .menutop li.root:hover > .item span, .menutop li.f-mainparent-itemfocus > .item span {background-position: 50% 100%;background-repeat: no-repeat;}

/* Dropdown Surrounds */
.menutop .f-submenu-pad {padding: 0 6px;}
.menutop ul {padding: 0;margin: 0;float: left;}
.menutop .drop-bot {height: 1px;overflow: hidden;clear: both;}
.menutop .fusion-submenu-wrapper {height: auto !important;padding: 0;box-shadow: 4px 4px 0 rgba(0, 0, 0, 0.2);}

/* Dropdown Items */
.menutop ul li {display: block;background-position: 6px 17px;background-repeat: no-repeat;}
.menutop ul li > .item {padding: 15px 15px 15px 25px;height: auto;display: block;font-size: 16px;line-height: 100%;font-weight: normal;letter-spacing: normal;}
.menutop ul li > .item:hover span, .menutop ul li.active > .item span, .menutop ul li.f-menuparent-itemfocus > .item span {margin: 0;}
.menutop li > .bullet {padding-left: 10px;}
.menutop li .item img {float: left;margin: 0px 6px 0px 0;vertical-align: top;}
.menutop li > .item span {padding: 0;display: block;width: auto;}
.menutop ul li .nolink span {display: block;}
.menutop li a.item {cursor: pointer;}
.menutop li span.item {cursor: default;outline: none;}
.menutop ul li .subtext em {line-height: 14px;}
.menutop ul li > .fusion-modules.item:hover {background: none;}
.menutop ul .readon span {width: auto;}
.menutop ul li > .daddy span {background-position: 100% 2px;background-repeat: no-repeat;}

/* No JS */
.menutop li.root:hover > .fusion-submenu-wrapper {top: 45px;left: 0;}
.menutop ul li:hover > .fusion-submenu-wrapper {left: 180px;top: -5px;}

/* Fusion JS */
.fusion-js-container {display: block;height: 0;left: 0;overflow: visible;position: absolute;top: 0;z-index: 600000!important;background: transparent !important;}
.fusion-js-subs {display: none;margin: 0;overflow: hidden;padding: 0;position: absolute;}

/* Grouped & Modules */
.fusion-grouped {padding-bottom: 10px;padding-left: 15px;}
.fusion-grouped ol {padding: 0;}
.menutop .fusion-grouped ol li .item {padding: 4px 15px;}
.fusion-grouped ol li span {font-size: 11px;padding-left: 10px;}
.menutop .fusion-grouped ol li {margin-left: 0;padding: 0;background-image: none;}
.menutop .type-module ol {padding: 0;background-image: none;}
.menutop .type-module ol li {padding: 0;}
.menutop .type-module ol li .fusion-modules {background: none;}
.menutop .type-module ol li .fusion-module {padding: 0;background: none;overflow: hidden;}
.menutop .fusion-module, .menutop .fusion-modules, .menutop .fusion-grouped {display: block;}
.menutop .fusion-module em {display: inline;font-size: inherit;font-style: italic;}
.menutop .fusion-module a {font-size: inherit;line-height: 130%;}
.menutop .fusion-module p {line-height: 160%;}
.menutop ul li.grouped-parent > .daddy span {background: none;}
.fusion-module.module-padding {padding: 10px;}