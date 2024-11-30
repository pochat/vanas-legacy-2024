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

$user= &JFactory::getUser();

// website root directory
$_root = JURI::root();
?>

<table width="100%" border="0">
	<tr>
		<td width="55%" valign="top">

<div id="cpanel" style="float:left;">
  
	
	<div style="float:left;">
    	<div class="icon">
	    	<a href="index.php?option=com_categories&amp;extension=com_faqbook">
		    	<img alt="<?php echo JText::_('COM_FAQBOOK_CATEGORIES'); ?>" src="components/com_faqbook/images/dashboard/icon-48-category.png" />
		    	<span><?php echo JText::_('COM_FAQBOOK_CATEGORIES'); ?></span>
	    	</a>
    	</div>
  	</div>


	<div style="float:left;">
    	<div class="icon">
	    	<a href="index.php?option=com_faqbook&amp;view=items">
		    	<img alt="<?php echo JText::_('COM_FAQBOOK_FAQS'); ?>" src="components/com_faqbook/images/dashboard/icon-48-article-add.png" />
		    	<span><?php echo JText::_('COM_FAQBOOK_FAQS'); ?></span>
	    	</a>
    	</div>
  	</div>

  	<div style="float:left;">
    	<div class="icon">
	    	<a href="index.php?option=com_faqbook&amp;view=about">
		    	<img alt="<?php echo JText::_('COM_FAQBOOK_ABOUT'); ?>" src="components/com_faqbook/images/dashboard/icon-48-info.png" />
		    	<span><?php echo JText::_('COM_FAQBOOK_ABOUT'); ?></span>
	    	</a>
    	</div>
  	</div>

  
	<div class="clr"></div>
  
</div>


</td>
		<td width="45%" valign="top">
			<?php
				echo $this->pane->startPane( 'stat-pane' );
				echo $this->pane->startPanel( JText::_('COM_FAQBOOK_WELCOME') , 'welcome' );
			?>
			<table class="adminlist">
				<tr>
					<td>
						<div style="font-weight:700;font-size:16px;margin-top:10px;">
							<?php echo JText::_('COM_FAQBOOK_BROUGHT_TO_YOU_BY_MINITEK');?>
						</div>
						<br />
						<p style="font-size:14px;">
							If you need support or found any bugs just visit the support forum at 
							<a href="http://www.minitek.gr/support/forum/2-minitek-faq-book.html" target="_blank">
							Minitek Support
							</a>.
							
						</p>
						<br />
						<p style="font-size:14px;">
							Before you start using FAQ Book, read the documentation:
							<a href="http://www.minitek.gr/documentation/joomla-free-extensions/minitek-faq-book" target="_blank">
							FAQ Book Documentation
							</a>.
						</p>
						<br />	
						<p style="font-size:14px;">
						If you use FAQ Book, please <a href="http://extensions.joomla.org/extensions/directory-a-documentation/faq/17056" target="_blank">submit a review at the JED</a>.
						</p>
						<br/>
						<img alt="<?php echo JText::_('FAQ Book'); ?>" src="components/com_faqbook/images/faq_book_small.png" style="margin:10px 20px 0 0;"/>
						<a href="http://www.minitek.gr/" target="_blank">
		    		<img alt="<?php echo JText::_('minitek.gr'); ?>" src="components/com_faqbook/images/dashboard/logo_minitek.png" style="margin:40px 0 0;float:right;"/>
	    			</a>				
						<br />
					</td>
				</tr>
			</table>
			<?php
				echo $this->pane->endPanel();			
				echo $this->pane->endPane();
			?>
		</td>
	</tr>
</table>


		
			

<div class="clr"></div>





