<?php
error_reporting(0);
/**
 # plg_content_fancyBookmarks -
 # @version		1.6.x
 # ------------------------------------------------------------------------
 # author    Qubesys Technologies Pvt.Ltd
 # copyright Copyright (C) 2011-2012 Qubesys Technologies Pvt.Ltd. All Rights Reserved.
 # @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL.
 # Websites: http://www.qubesys.com

-------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * fancyBookmarks Content Plugin
 */


 ?>
<style>
.pinterest-btn {
    height: 30px;
    margin: 0;
    padding: 0;
}

.pin-it-btn {
    position: absolute;
    background: url(http://assets.pinterest.com/images/pinit6.png);
    font: 11px Arial, sans-serif;
    text-indent: -9999em;
    font-size: .01em;
    color: #CD1F1F;
    height: 20px;
    width: 43px;
    background-position: 0 -7px;
}

.pin-it-btn:hover {
    background-position: 0 -28px;
}

.pin-it-btn:active {
    background-position: 0 -49px;
}
</style>


<script type="text/javascript">
(function() {
    window.PinIt = window.PinIt || { loaded:false };
    if (window.PinIt.loaded) return;
    window.PinIt.loaded = true;
    function async_load(){
        var s = document.createElement("script");
        s.type = "text/javascript";
        s.async = true;
        if (window.location.protocol == "https:")
            s.src = "https://assets.pinterest.com/js/pinit.js";
        else
            s.src = "http://assets.pinterest.com/js/pinit.js";
        var x = document.getElementsByTagName("script")[0];
        x.parentNode.insertBefore(s, x);
    }
    if (window.attachEvent)
        window.attachEvent("onload", async_load);
    else
        window.addEventListener("load", async_load, false);
})();
</script>
<script>
function exec_pinmarklet() {
    var e=document.createElement('script');
    e.setAttribute('type','text/javascript');
    e.setAttribute('charset','UTF-8');
    e.setAttribute('src','http://assets.pinterest.com/js/pinmarklet.js?r=' + Math.random()*99999999);
    document.body.appendChild(e);
}
</script>
 
 <?php
class plgContentfancyBookmarks extends JPlugin
{
	/**
	 * fancyBookmarks after display content method
	 *
	 * Method is called by the view and the results are imploded and displayed in a placeholder
	 *
	 * @param	string		The context for the content passed to the plugin.
	 * @param	object		The content object.  Note $article->text is also available
	 * @param	object		The content params
	 * @param	int			The 'page' number
	 * @return	string
	 * @since	1.6
	 */
	public function onContentAfterDisplay($context, &$article, &$params, $limitstart=1)
	{
		$showToolBox = $this->showToolbox($article);
        $showpos     = $this->params->get('position', 0);
        
        if($showToolBox){
           
			
			
                
           
			 if($showpos == 1){
			$script = $this->getToolboxScript($article);
				
				return $script;
			
            }
        }
	}
    /**
	 * fancyBookmarks before display content method
	 *
	 * Method is called by the view and the results are imploded and displayed in a placeholder
	 *
	 * @param	string		The context for the content passed to the plugin.
	 * @param	object		The content object.  Note $article->text is also available
	 * @param	object		The content params
	 * @param	int			The 'page' number
	 * @return	string
	 * @since	1.6
	 */
	public function onContentBeforeDisplay($context, &$article, &$params, $limitstart=1)
	{
		$showToolBox = $this->showToolbox($article);
        $showpos     = $this->params->get('position', 0);
        
		
		
		
		
        if($showToolBox){
            
            
            if($showpos == 0){
			$script = $this->getToolboxScript($article);
				return $script;
				
            }
        }
	}	
	
    /**
     * getToolboxScript
     * 
     * Preparing the toolbox script
     *      
     * @param object $article
     * @return string - Returns the script for rendering the selected services in toolbox
    */
    public function getToolboxScript($article){
if($this->params->get('enable_ssl') == 1 ) {
$socials ='<script type="text/javascript">var switchTo5x=false;</script><script type="text/javascript" src="https://ws.sharethis.com/button/buttons.js"></script><script type="text/javascript">stLight.options({publisher:"'.$this->params->get('publisher_id').'"});</script><script type="text/javascript">
  (function() {
    var po = document.createElement("script"); po.type = "text/javascript"; po.async = true;
    po.src = "https://apis.google.com/js/plusone.js";
    var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(po, s);
  })();
</script>';
} else{
$socials ='<script type="text/javascript">var switchTo5x=false;</script><script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script><script type="text/javascript">stLight.options({publisher:"'.$this->params->get('publisher_id').'"});</script><script type="text/javascript">
  (function() {
    var po = document.createElement("script"); po.type = "text/javascript"; po.async = true;
    po.src = "https://apis.google.com/js/plusone.js";
    var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(po, s);
  })();
</script>';

} 	
if($this->params->get('style') == 'style1' ) {
$socials .= "\n".'<div id="style1" style="margin-top:15px !important;" ><p>'.$this->params->get('heading_txt').'</p>';
if($this->params->get('rss') == 1){
$socials .='<span><a href="'.$this->params->get('rss_url').'" target="_blank"/><img src="'.$base_url.'plugins/content/fancybookmarks/fancybookmarks/images/rss_button.gif"/></a></span>';
}
if($this->params->get('twitter') == 1){
$socials .='<span  class="st_twitter_hcount" displayText="Tweet"  st_via="'.$this->params->get('twitter_username').'"></span>';
}if($this->params->get('facebook') == 1){
$socials .='<span  class="st_facebook_hcount" displayText="Facebook"></span>';
}if($this->params->get('share') == 1){
$socials .='<span  class="st_sharethis_hcount" displayText="ShareThis"></span>';
}if($this->params->get('email') == 1){
$socials .='<span  class="st_email_hcount" displayText="Email"></span>';
}if($this->params->get('yahoo') == 1){
$socials .='<span  class="st_yahoo_hcount" displayText="Yahoo!"></span>';
}if($this->params->get('tumblr') == 1){
$socials .='<span  class="st_tumblr_hcount" displayText="Tumblr"></span>';
}if($this->params->get('technorati') == 1){
$socials .="<span  class='st_technorati_hcount' displayText='Technorati'></span>";
}if($this->params->get('stumble') == 1){
$socials .="<span  class='st_stumbleupon_hcount' displayText='StumbleUpon'></span>";
}if($this->params->get('linkedin') == 1){
$socials .="<span  class='st_linkedin_hcount' displayText='LinkedIn'></span>";
}if($this->params->get('delicious') == 1){
$socials .="<span  class='st_delicious_hcount' displayText='Delicious'></span>";
}if($this->params->get('reddit') == 1){
$socials .="<span  class='st_reddit_hcount' displayText='Reddit'></span>";
}if($this->params->get('google') == 1){

$socials .='<span  class="st_plusone_hcount" ></span>';
}if($this->params->get('facebook_like') == 1){
$socials .='<span style="padding:0 !important;margin:0 !important;" class="st_fblike_hcount"></span>';
}
if($this->params->get('pintrest') == 1){
$socials .='<span class="pinterest-btn">
	<a href="javascript:exec_pinmarklet();" class="pin-it-btn" title="Pin It on Pinterest"></a>
</span>';
}

$socials .='</div>';
}

if($this->params->get('style') == 'style2') {
$socials .= "\n".'<div id="style2" style="margin-top:15px !important;"><p>'.$this->params->get('heading_txt').'</p><table><tr>';
if($this->params->get('rss') == 1){
$socials .='<td><span><a href="'.$this->params->get('rss_url').'" target="_blank"><img src="'.$base_url.'plugins/content/fancybookmarks/fancybookmarks/images/rss_32.png"/></a></span></td>';
}
if($this->params->get('twitter') == 1){
$socials .='<td><span  class="st_twitter_large" st_via="'.$this->params->get('twitter_username').'" ></span></td>';
}if($this->params->get('facebook') == 1){
$socials .='<td><span  class="st_facebook_large" ></span></td>';
}if($this->params->get('email') == 1){
$socials .='<td><span  class="st_email_large" ></span></td>';
}if($this->params->get('yahoo') == 1){
$socials .="<td><span  class='st_yahoo_bmarks_large' ></span></td>";
}if($this->params->get('tumblr') == 1){
$socials .="<td><span  class='st_tumblr_large' ></span></td>";
}if($this->params->get('technorati') == 1){
$socials .="<td><span  class='st_technorati_large' ></span></td>";
}if($this->params->get('stumble') == 1){
$socials .="<td><span  class='st_stumbleupon_large' ></span></td>";
}if($this->params->get('linkedin') == 1){
$socials .="<td><span  class='st_linkedin_large' ></span></td>";
}if($this->params->get('delicious') == 1){
$socials .="<td><span  class='st_delicious_large' ></span></td>";
}if($this->params->get('reddit') == 1){
$socials .="<td><span  class='st_reddit_large' ></span></td>";
}if($this->params->get('share') == 1){
$socials .='<td><span  class="st_sharethis_large" ></span></td>';
}if($this->params->get('google') == 1){
$socials .='<td><span><div class="g-plusone"></div></span></td>';
}

if($this->params->get('pintrest') == 1){
$socials .='<td><span class="pinterest-btn">
	<a href="javascript:exec_pinmarklet();" class="pin-it-btn" title="Pin It on Pinterest"></a>
</span></td>';
}
$socials .='</tr></table>';
$socials .='</div>';
}


if($this->params->get('style') == 'style3') {
$socials .= "\n"."<div id='style3' style='margin-top:15px !important;'><p>".$this->params->get('heading_txt')."</p>";
if($this->params->get('rss') == 1){
$socials .='<span><a href="'.$this->params->get('rss_url').'" target="_blank"/><img src="'.$base_url.'plugins/content/fancybookmarks/fancybookmarks/images/social_rss_box_orange_128.png" ></a></span>';
}
if($this->params->get('twitter') == 1){
$socials .="<span  class='st_twitter_vcount' displayText='Tweet' st_via='".$this->params->get('twitter_username')."'></span>";
}if($this->params->get('email') == 1){
$socials .="<span  class='st_email_vcount' displayText='Email'></span>";
}if($this->params->get('facebook') == 1){
$socials .="<span  class='st_facebook_vcount' displayText='Facebook'></span>";
}if($this->params->get('yahoo') == 1){
$socials .="<span  class='st_yahoo_vcount' displayText='Yahoo!'></span>";
}if($this->params->get('tumblr') == 1){
$socials .="<span  class='st_tumblr_vcount' displayText='Tumblr'></span>";
}if($this->params->get('technorati') == 1){
$socials .="<span  class='st_technorati_vcount' displayText='Technorati'></span>";
}if($this->params->get('stumble') == 1){
$socials .="<span  class='st_stumbleupon_vcount' displayText='StumbleUpon'></span>";
}if($this->params->get('linkedin') == 1){
$socials .="<span  class='st_linkedin_vcount' displayText='LinkedIn'></span>";
}if($this->params->get('delicious') == 1){
$socials .="<span  class='st_delicious_vcount' displayText='Delicious'></span>";
}

if($this->params->get('reddit') == 1){
$socials .="<span  class='st_reddit_vcount' displayText='Reddit'></span>";
}

if($this->params->get('google') == 1){
$socials .='<span><g:plusone size="tall"></g:plusone></span>';
}

if($this->params->get('share') == 1){
$socials .="<span  class='st_sharethis_vcount' displayText='ShareThis'></span>";
}
if($this->params->get('facebook_like') == 1){
$socials .='<span style="margin-bottom:10px;"  class="st_fblike_vcount"></span>';
}

$socials .='</div>';
}

if($this->params->get('style') == 'style4' ) {
$socials .= "\n".'<div id="style1" style="margin-top:15px !important;" ><p>'.$this->params->get('heading_txt').'</p>';
if($this->params->get('rss') == 1){
$socials .='<span><a href="'.$this->params->get('rss_url').'" target="_blank"/><img src="'.$base_url.'plugins/content/fancybookmarks/fancybookmarks/images/rss_button.gif"/></a></span>';
}
if($this->params->get('twitter') == 1){
$socials .="<span class='st_twitter_button' displayText='Tweet' st_via='".$this->params->get('twitter_username')."'></span>";
}if($this->params->get('facebook') == 1){
$socials .="<span class='st_facebook_button' displayText='Facebook'></span>";
}if($this->params->get('share') == 1){
$socials .="<span class='st_sharethis_button' displayText='ShareThis'></span>";
}if($this->params->get('email') == 1){
$socials .="<span class='st_email_button' displayText='Email'></span>";
}if($this->params->get('yahoo') == 1){
$socials .="<span  class='st_yahoo_bmarks_button' displayText='Bookmarks'></span>";
}if($this->params->get('tumblr') == 1){
$socials .="<span class='st_tumblr_button' displayText='Tumblr' ></span>";
}if($this->params->get('technorati') == 1){
$socials .="<span class='st_technorati_button' displayText='Technorati' ></span>";
}if($this->params->get('stumble') == 1){
$socials .="<span class='st_stumbleupon_button' displayText='StumbleUpon' ></span>";
}if($this->params->get('linkedin') == 1){
$socials .="<span class='st_linkedin_button' displayText='LinkedIn'></span>";
}if($this->params->get('delicious') == 1){
$socials .="<span class='st_delicious_button' displayText='Delicious' ></span>";
}if($this->params->get('reddit') == 1){
$socials .="<span class='st_reddit_button' displayText='Reddit' ></span>";
}if($this->params->get('google') == 1){

$socials .='<span class="st_plusone_button" ></span>';
}if($this->params->get('facebook_like') == 1){
$socials .='<span style="padding:0 !important;margin:0 !important;" class="st_fblike_hcount"></span>';
}
if($this->params->get('pintrest') == 1){
$socials .='<span class="pinterest-btn">
	<a href="javascript:exec_pinmarklet();" class="pin-it-btn" title="Pin It on Pinterest"></a>
</span>';
}

$socials .='</div>';
}




       
            return $socials;
}
	

    
   /**
    * getArticleUrl
    *
    * Gets the static url for the article
    * 
    * @param object $article - Joomla article object
    **/
    private function getArticleUrl(&$article)
    {
        if (!is_null($article)) 
        {
            require_once( JPATH_SITE . DS . 'components' . DS . 'com_content' . DS . 'helpers' . DS . 'route.php');
            $url = JURI::root().substr(JRoute::_(ContentHelperRoute::getArticleRoute($article->slug, $article->catid)), 1);
            
            return $url;
        }
    }
    
   /**
    * Show Or Hide The Toolbox
    * 
    * @return bool
    * 
    **/
    private function showToolbox($article){
	    if(JFactory::getApplication()->isAdmin()) return;
        $catid         = $article->catid;       
        $menu          =&JSite::getMenu();             
        $showFrontpage = (bool) $this->params->get('show_frontpage', 1);
		$showArticlepage = (bool) $this->params->get('article', 1);
		
		if($showArticlepage == 1 )
		{
        $showCat       = (array) @explode(',',$this->params->get( 'include_cat'));
		}
        //$hideCat       = (array) @explode(',',$this->params->get( 'ex_categories', '-1'));
		
		
		
		if(!in_array($catid,$showCat) && $showCat != array(0) && $catid !=0){
            return;
        }
        
        if(($menu->getActive() == $menu->getDefault()) && !$showFrontpage ) {
            return;
        } 
		if (JRequest::getVar('layout') == 'blog' )
            {
          return;
		  
              }
				
         
      
        
       
        
		
		
		
		$hideCat       = explode(',',$this->params->get( 'filter_cat'));
	 if (!in_array($article->catid, $hideCat))
	{
        
        return true;
       } 
    }

}