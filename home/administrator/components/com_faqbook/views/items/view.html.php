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

jimport('joomla.application.component.view');

class FaqBookViewItems extends JView
{
	protected $items;
	protected $pagination;
	protected $state;
	
	public function display($tpl = null)
	{
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		 	 
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		$this->addToolbar();		
		parent::display($tpl);
	}
	
	protected function addToolbar()
	{
	  $canDo = FaqBookHelper::getActions();
		$user		= JFactory::getUser();
		JToolBarHelper::title(JText::_('FAQs Manager: FAQ Book'), 'article-add.png');
    if ($canDo->get('core.create') || (count($user->getAuthorisedCategories('com_faqbook', 'core.create'))) > 0 ) {
		  JToolBarHelper::addNew('item.add','JTOOLBAR_NEW');
		}
		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own'))) {
		  JToolBarHelper::editList('item.edit','JTOOLBAR_EDIT');
		}
		if ($canDo->get('core.delete')) {
		  JToolBarHelper::deleteList('', 'items.delete','JTOOLBAR_DELETE');
		}
		if ($canDo->get('core.edit.state')) {
		  JToolBarHelper::divider();
		  JToolBarHelper::custom('items.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
		  JToolBarHelper::custom('items.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
		}
		if ($canDo->get('core.admin')) {
		  JToolBarHelper::divider();
		  // Settings Icon in Top Bar
		  JToolBarHelper::preferences('com_faqbook', '600', '800');		
		}
	}
	
}