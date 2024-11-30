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

abstract class FaqBookHelper
{
	public static $extension = 'com_faqbook';
	
	public static function addSubmenu($vName)
	{
		JSubMenuHelper::addEntry(
			JText::_('COM_FAQBOOK_DASHBOARD'),
			'index.php?option=com_faqbook&view=dashboard',
			$vName == 'dashboard'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_FAQBOOK_CATEGORIES'),
			'index.php?option=com_categories&extension=com_faqbook',
			$vName == 'categories'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_FAQBOOK_FAQS'),
			'index.php?option=com_faqbook&view=items',
			$vName == 'items'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_FAQBOOK_ABOUT'),
			'index.php?option=com_faqbook&view=about',
			$vName == 'about'
		);

		if ($vName=='categories') {
			JToolBarHelper::title(
				JText::sprintf('COM_FAQBOOK',JText::_('com_faqbook')),
				'');
		}
		
	}
	
	/**
	 * Gets a list of the actions that can be performed.
	 *
	 */
	public static function getActions($categoryId = 0, $messageId = 0)
	{	
		jimport('joomla.access.access');
		$user	= JFactory::getUser();
		$result	= new JObject;
 
		if (empty($messageId) && empty($categoryId)) {
			$assetName = 'com_faqbook';
		}
		else if (empty($messageId)) {
			$assetName = 'com_faqbook.category.'.(int) $categoryId;
		}
		else {
			$assetName = 'com_faqbook.item.'.(int) $messageId;
		}
 
    $actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);
		//$actions = JAccess::getActions('com_faqbook', 'component');
 
		foreach ($actions as $action) {
			//$result->set($action->name, $user->authorise($action->name, $assetName));
			$result->set($action,	$user->authorise($action, $assetName));
		}
 
		return $result;
	}
	
}
