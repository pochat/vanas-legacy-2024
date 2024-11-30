<?php
/**
* @version		$Id: mod_fancybookmarks.php 14401 2010-01-26 14:10:00Z louis $
* @package		Fancy Bookmarks with Google +1
* @copyright	Copyright (C) 2011 Qubesys Technologies PVt.Ltd. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/


// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$style = $params->get('style');
$twitter = $params->get('twitter');
$email = $params->get('email');
$facebook = $params->get('facebook');
$share = $params->get('share');
$google = $params->get('google');
$facebook_like = $params->get('facebook_like');

$tumblr = $params->get('tumblr');
$technorati = $params->get('technorati');
$stumble = $params->get('stumble');
$linkedin = $params->get('linkedin');
$delicious = $params->get('delicious');
$reddit = $params->get('reddit');
$pintrest = $params->get('pintrest');
$myspace = $params->get('myspace');
$heading_txt = $params->get('heading_txt');
$publisher_id = $params->get('publisher_id');
$twitter_username = $params->get('twitter_username');

require(JModuleHelper::getLayoutPath('mod_fancybookmarks','default'));

?>