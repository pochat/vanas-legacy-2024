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

/*** joomla.css ***/

/**
 * @package   Momentum Template - RocketTheme
 * @version   1.5 December 12, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.rockettheme.com/legal/license.php RocketTheme Proprietary Use License
 */

/* Article */
.component-content .rt-article {margin-bottom: 15px;}
.component-content .rt-article-cat {margin-top: 10px;margin-bottom: 15px;}
.component-content .rt-article-bg {border-bottom: none;margin: 0;padding: 0;}
.component-content .rt-pagetitle {margin-top: 0;margin-bottom: 30px;font-weight: normal;letter-spacing: normal;font-size: 160%;}
.component-content .rt-blog .rt-description {border-bottom: none;}
.component-content  ol {margin-left: 5px;}
.component-content .rt-teaser-articles {display: inline-block;margin-top: 20px;}
.component-content .rt-more-articles {padding-top: 15px;}
.component-content .multicolumns .rt-article {clear: both;margin-bottom: 25px;}
.component-content .categories-list .article-count dt, .component-content .categories-list .article-count dd {display: inline;}

/* Article Details */
.component-content .rt-article-icons {white-space: nowrap;float: right;margin-top: 2px;margin-left: 5px;width: 90px;}
.component-content .rt-article-icons ul li a {display: block;width: 18px;height: 18px;float: right;transition: background-color 0.3s ease-out;}
.component-content .print-icon a {background-position: 0 0;background-repeat: no-repeat;}
.component-content .email-icon a {background-position: -21px 0;background-repeat: no-repeat;}
.component-content .edit-icon a {background-position: -62px 0;background-repeat: no-repeat;}
.component-content .rt-article-icons .edit a {background-position: -63px 0;}
.component-content .rt-article-icons .edit a {margin: 0;}
.component-content .rt-article-icons .edit a img {opacity: 0 !important;}
.component-content .rt-author, .component-content .rt-date-posted, .component-content .rt-date-modified {display: inline-block;line-height: 100%;font-size: 1em;margin-right: 15px;font-weight: normal;padding: 8px;}
.component-content .rt-articleinfo-text {float: left;position: relative;}
.component-content .rt-articleinfo {border-bottom: 1px dashed rgba(0, 0, 0, 0.3);}

/* More Articles Feature */
.component-content #more-articles {display: block;margin: 0;padding: 0;position: relative;height: 24px;}
.component-content #more-articles span {display: inline-block;margin: 0;padding: 0 20px 0 0;line-height: 24px;font-size: 15px;float: right;}
.component-content #more-articles.spinner {background-image:url(../images/spinner.gif);background-position: 0 50%;background-repeat: no-repeat;}
.component-content #more-articles.disabled {cursor: default;}

/* Category - Subcategories */
.cat-children {clear: both; padding-top: 20px;}
.main-overlay-light .component-content .cat-children ul li span > a, .main-overlay-dark .component-content .cat-children ul li span > a, .main-overlay-light .component-content .category-list .cat-children p a, .main-overlay-dark .component-content .category-list .cat-children p a, .main-overlay-light .component-content .category-list .cat-children .category-desc ul li a, .main-overlay-dark .component-content .category-list .cat-children .category-desc ul li a {background-image: none;}

/* Column Layout */
.component-content .float-left {float: left;}
.component-content .float-right {float: right;}
.component-content .multicolumns {width: 100%;}
.component-content .width25 {width: 25%;}
.component-content .width33 {width: 33.33%;}
.component-content .width50 {width: 50%;}
.component-content .width100 {width: 100%;}
.component-content .multicolumns {width: 100%;margin: 0;}
.component-content .rt-teaser-articles {width: 100%;}

/* Typography */
.component-content .results ol.list {margin-top: 0;}
.component-content .results ol.list h4 {border: 0;margin-top: 0;margin-bottom: 0;display: inline;}
.component-content .results ol.list li {padding: 10px 5px;margin-bottom: 5px;}
.component-content .results ol.list li p {margin-top: 0;font-size: 90%;}
.component-content .results ol.list .description {margin-bottom: 15px;}
.component-content .results ol.list li span.small {font-size: 90%;}
.component-content .rt-article-links h3, .component-content .user legend, .component-content .contact legend {font-weight: normal;font-size: 170%;line-height: 120%;letter-spacing: normal;}
.component-content .user {margin-top: 15px;}
.component-content .user legend {margin-top: 15px;}
.component-content .user .label-left {padding: 4px 0;}
.component-content .user fieldset div {overflow: visible;clear: both;margin-bottom: 8px;}
.component-content .user td.paramlist_key {width: 180px;}
.component-content .search_result select#ordering.inputbox {margin-right: 10px;}
.component-content .search-results .word label, .component-content .search-results .word input {float: left;}
.component-content .search_result .readon {clear: both; margin-left: 15px;}
.component-content .inputbox.invalid {color: #c00;}
.component-content .rt-joomla-table {padding-bottom: 20px;}
.component-content .rt-joomla-table td {border: 0;padding: 8px;}
.component-content .rt-joomla-table th {border: 0;}
.component-content tr.odd td, .component-content tr.even td {border: 0;}
.contact .rt-joomla-table, .contact .rt-joomla-table th, .contact .rt-joomla-table tbody, .contact .rt-joomla-table td, .rt-joomla-table .odd, .rt-joomla-table .even {background: none;border: 0;}
.component-content .rt-description img {margin-right: 15px;}
.component-content .my-pagenav {float: left;margin-left: 10px;}
.component-content .tab {display: inline-block;margin-right: 10px;text-align: center;}
.component-content .page-block {display: inline-block;margin-right: 4px;font-weight: bold;text-align: center;}
.component-content .page-active, .component-content .page-inactive {padding: 0 4px;}
.component-content .page-inactive:hover {cursor: pointer;}
.component-content .search_result label {margin-right: 10px;}
.component-content .search_result legend {font-size: 14px;padding-bottom: 5px;}
.tool-tip {z-index: 1000 !important;}
#form-login ul, #com-form-login ul, ul.rt-more-articles, .rt-section-list ul, ul.mostread, ul.latestnews, .weblinks ul, #rt-popuplogin ul, ul.newsfeed {padding: 0;}
#rt-popuplogin ul {margin-top: 10px;}
#form-login ul li, #com-form-login ul li, ul.rt-more-articles li, .rt-section-list ul li, ul.mostread li, ul.latestnews li, .weblinks ul li, #rt-popuplogin ul li,ul.newsfeed li {list-style: none;}
ul.newsfeed li {padding-top: 10px;}
#form-login ul li a, #com-form-login ul li a, ul.rt-more-articles li a, .rt-section-list ul li a, ul.mostread li a, ul.latestnews li a, .weblinks ul li a, #rt-popuplogin ul li a,ul.newsfeed li a {padding: 4px 0 4px 20px;background-position: 0 4px;background-repeat: no-repeat;display: inline-block;line-height: 100%;font-size: 100%;font-weight: normal;transition: background-position-x 0.2s linear;}
#form-login ul li a:hover, #com-form-login ul li a:hover, ul.rt-more-articles li a:hover, .rt-section-list ul li a:hover, ul.mostread li a:hover, ul.latestnews li a:hover, .weblinks ul li a:hover, #rt-popuplogin ul li a:hover,ul.newsfeed li a:hover {background-position: 5px -311px;}
.component-content .user input#email, .component-content .user input#name, .component-content .user input#username, .component-content .user input#password, .component-content .user input#passwd, .component-content .user input#password2 {width: 160px !important;padding: 5px 4px;outline: none;}
#rt-popuplogin .inputbox {width: 164px;padding: 6px 4px;outline: none;font-size: 14px;}
#com-form-login .inputbox, .component-content .user input#email, .component-content .user input#name, .component-content .user input#username, .component-content .user input#password, .component-content .user input#password2 {width: 260px;}
#com-form-login #remember.inputbox {width: auto;}
#com-form-login .inputbox {padding: 5px 10px;outline: none;}
#com-form-login ul {margin-top: 15px;}
#form-login ul {padding-left: 0;}
#com-form-login fieldset div.readon, #josForm fieldset div.readon {margin: 0;}
#com-form-login fieldset div.readon:active, #josForm fieldset div.readon:active {margin-top: 1px;margin-bottom: -1px;}
#form-login-remember {margin: 15px 0;}

/* Pagination */
.rt-pagination, .pagination {margin-top: 15px;}
.component-content .rt-pagination li {border: none;}
.pagination .counter {margin-bottom: 5px;}
.component-content .pagination ul {list-style: none;padding-left: 0;}
.component-content .pagination li {padding: 0 4px;float: left;}
.main-overlay-light .component-content .pagination ul > li > a.pagenav, .main-overlay-dark .component-content .pagination ul > li > a.pagenav {background-image: none;font-size: 12px;}
ul.pagination {list-style-type: none;margin: 0;padding: 0;text-align: left;}
ul.pagination li {display: inline;padding: 2px 5px 0;text-align: left;}
ul.pagination li a {padding: 2px;}
.component-content .pagenav_prev, .component-content .pagenav_next {border-radius: 5px;padding: 0;border: none;}
.component-content .pagenav_prev a, .component-content .pagenav_next a {display: inline-block;padding: 5px 10px;}

/* Polls */
.fusion-submenu-wrapper .rt-pollrow {font-size: 100%;}
.rt-poll .readon {float: none;margin-right: 5px;}
.rt-poll .rt-pollbuttons {clear: both;padding-bottom: 5px;}
.rt-polltitle {margin: 0 0 15px 0;padding: 0;font-size: 100%;letter-spacing: normal;}
.readonstyle-link .rt-poll .readon {float: left;margin-right: 20px;}
.pollstableborder td div {border: 0;height: 10px !important;min-width: 2px;}
.rt-poll .pollstableborder th, .rt-poll .pollstableborder td, .rt-poll .poll dd {border: 0;}
.rt-pollbuttons .readon:active {margin-top: 0;margin-bottom: 0;}

/* SmartLoad */
img.spinner {background: url(../images/spinner.gif) center center no-repeat;}
.weblinks td img {width: 16px;height: 16px;}

/* Editing */
.component-content .edit-article fieldset legend {font-size: 230%;}
.component-content .edit-article fieldset div {overflow: visible;margin-bottom: 8px;}
.component-content .edit-article fieldset #editor-xtd-buttons div {clear: none;}
#system-message dd.notice {border: 0;background: none;}
.component-content .save .readon {margin-left: 10px;}
div.calendar {z-index: 9999;}
.button2-left {background: url(../images/system/j_button2_left.png) no-repeat !important;color: #666;}
.button2-left .image {background: url(../images/system/j_button2_image.png) 100% 0 no-repeat !important;}
.button2-left .readmore {background: url(../images/system/j_button2_readmore.png) 100% 0 no-repeat !important;}
.button2-left .pagebreak {background: url(../images/system/j_button2_pagebreak.png) 100% 0 no-repeat !important;}
.button2-left .blank {background: url(../images/system/j_button2_blank.png) 100% 0 no-repeat !important;}
.button2-left .linkmacro {background: url(../images/system/j_button2_rokcandy.png) 100% 0 no-repeat !important;}
.tip-wrap {z-index: 100; background: #ccc; padding: 5px; }
.edit .formelm {margin-bottom: 10px;}
.edit .calendar {margin-left: 5px;}
.profile-edit .readon {vertical-align: middle;}

/* Editor */
#editor-xtd-buttons {padding: 5px 0;}
.button2-left {margin-top: 5px;margin-right: 5px;}
.button2-left, .button2-right, .button2-left div, .button2-right div {float: left;}
.component-content  .button2-left .linkmacro, .component-content  .button2-left .image {background: none;}
.toggle-editor .button2-left {margin-top: 5px;}
.component-content .button2-left a {line-height: 2.0em;}
.button2-left a, .button2-right a, .button2-left span, .button2-right span {display: block;height: 22px;float: left;line-height: 22px;font-size: 11px;cursor: pointer;margin-bottom: 5px;}
.button2-left span, .button2-right span {cursor: default;}
.button2-left .page a, .button2-right .page a, .button2-left .page span, .button2-right .page span {padding: 0 6px;}
.button2-left a:hover, .button2-right a:hover {text-decoration: none;}
.button2-left a, .button2-left span {padding: 0 24px 0 6px;}
.button2-right a, .button2-right span {padding: 0 6px 0 24px;}
.button2-left .blank a {padding-right: 6px;}
.img_caption.left {float: left;margin-right: 1em;}
.img_caption.right {float: right;margin-left: 1em;}
.img_caption.left p {clear: left;text-align: center;}
.img_caption.right p {clear: right;text-align: center;}
.img_caption {text-align: center !important;}
.edit-article fieldset div input, .edit-article fieldset textarea {padding: 4px;}
.edit-article fieldset div .label-left {padding: 0;}
.edit-article fieldset div img.calendar {vertical-align: middle;margin-left: 5px;}

/* System Messages */
#system-message dd ul, #system-message dd.error ul, #system-message dd.notice ul {border: 0;font-size: 1.2em;text-align: center;}

/* Compatibility */
#recaptcha_widget_div {position: absolute;top: -140px;}
#emailForm fieldset div.readon {position: relative;margin-top: 140px;}
#emailForm fieldset div.readon br {display: none;}

/* Features */
#rt-accessibility .small .button {background-position: 2px -29px;background-repeat: no-repeat;border-radius: 2px;width: 15px; height: 15px;}
#rt-accessibility .large .button {background-position: 2px 2px;background-repeat: no-repeat;border-radius: 2px;width: 15px; height: 15px;}
#rt-accessibility .small .button:hover {background-position: 2px -39px;}
#rt-accessibility .large .button:hover {background-position: 2px -15px;}