<?php
/**
* @title   Minitek FAQ Book
* @version   1.5.2
* @copyright   Copyright (C) 2011-2012 Minitek, All rights reserved.
* @license   GNU General Public License version 2 or later.
* @author url   http://www.minitek.gr/
* @author email   info@minitek.gr
* @developer   Ioannis Maragos - minitek.gr
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import joomla controller library
//jimport('joomla.application.component.controller');

// Require the base controller
require_once JPATH_COMPONENT.DS.'controller.php';
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'route.php' );

// Include tables
JTable::addIncludePath(JPATH_COMPONENT.DS.'tables');

JFactory::getApplication()->set('jquery', true);

$controller = JRequest::getWord('view', 'category');
jimport('joomla.filesystem.file');

if (JFile::exists(JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php')) {
	require_once (JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php');
	$classname = 'FaqBookController'.$controller;
	$controller = new $classname();
	$controller->execute(JRequest::getWord('task'));
	$controller->redirect();
}

// Add stylesheet
$document = & JFactory::getDocument();
$document->addStyleSheet(JURI::base().'components/com_faqbook/css/faqbook.css');
jimport( 'joomla.application.component.helper' );
$params  = JComponentHelper::getParams('com_faqbook');