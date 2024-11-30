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
error_reporting(E_ALL); // debug

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_faqbook')) 
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

$controller	= JController::getInstance('faqbook');

// Perform the Request task
$controller->execute( JRequest::getCmd('task'));
$controller->redirect();

// Add stylesheet
$document = & JFactory::getDocument();
$document->addStyleSheet(JURI::base().'components/com_faqbook/css/faqbook.css');

?>