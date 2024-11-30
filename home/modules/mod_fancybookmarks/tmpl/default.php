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


if($params->get('enable_ssl') == 1 ) {
echo '<script type="text/javascript">var switchTo5x=true;</script>
<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
<script type="text/javascript">stLight.options({publisher: "'.$publisher_id.'", doNotHash: false, doNotCopy: false, hashAddressBar: false});</script> ';
} else{
echo '<script type="text/javascript">var switchTo5x=true;</script>
<script type="text/javascript" src="https://w.sharethis.com/button/buttons.js"></script>
<script type="text/javascript">stLight.options({publisher: "'.$publisher_id.'", doNotHash: false, doNotCopy: false, hashAddressBar: false});</script> ';

}
?>
<p><?php echo $heading_txt; ?></p>
<?php
if($style == 'style1') { ?>
<div id="style1" class="<?php echo $params->get('moduleclass_sfx'); ?>" >
<?php if($twitter == 1){ ?>
<span class='st_twitter_hcount' displayText='Tweet' st_via="<?php echo $twitter_username; ?>"></span>
<?php }if($facebook == 1){ ?>
<span class='st_facebook_hcount' displayText='Facebook'></span>
<?php }if($share == 1){ ?>
<span class='st_sharethis_hcount' displayText='ShareThis'></span>
<?php }if($email == 1){ ?>
<span class='st_email_hcount' displayText='Email'></span>

<?php }if($tumblr == 1){ ?>
<span class='st_tumblr_hcount' displayText='Tumblr'></span>
<?php }if($technorati == 1){ ?>
<span class='st_technorati_hcount' displayText='Technorati'></span>
<?php }if($stumble == 1){ ?>
<span class='st_stumbleupon_hcount' displayText='StumbleUpon'></span>
<?php }if($linkedin == 1){ ?>
<span class='st_linkedin_hcount' displayText='LinkedIn'></span>
<?php }if($reddit == 1){ ?>
<span class='st_reddit_hcount' displayText='Reddit'></span>
<?php }if($myspace == 1){ ?>
<span class='st_myspace_hcount' displayText='MySpace'></span>
<?php }if($delicious == 1){ ?>
<span class='st_delicious_hcount' displayText='Delicious'></span>
<?php }if($google == 1){ ?>
<span class='st_plusone_hcount' displayText='Google +1'></span>
<?php }if($facebook_like == 1){ ?>
<span class='st_fblike_hcount' displayText='Facebook Like'></span>
<?php }if($pintrest == 1){ ?>
<span class="pinterest-btn">
	<a href="javascript:exec_pinmarklet();" class="pin-it-btn" title="Pin It on Pinterest"></a>
</span>
<?php  } ?>

</div>
<?php } 

if($style == 'style2') { ?>
<div id="style2" class="<?php echo $params->get('moduleclass_sfx'); ?>"><table><tr>
<?php if($twitter == 1){ ?>

<td><span class='st_twitter_large' displayText='Tweet' st_via="<?php echo $twitter_username; ?>"></span></td>
<?php }if($facebook == 1){ ?>
<td><span class='st_facebook_large' displayText='Facebook'></span></td>
<?php }if($email == 1){ ?>
<td><span class='st_email_large' displayText='Email'></span></td>

<?php } if($tumblr == 1){ ?>
<td><span class='st_tumblr_large' displayText='Tumblr'></span></td>
<?php } if($technorati == 1){ ?>
<td><span class='st_technorati_large' displayText='Technorati'></span></td>
<?php } if($stumble == 1){ ?>
<td><span class='st_stumbleupon_large' displayText='StumbleUpon'></span></td>
<?php } if($linkedin == 1){ ?>
<td><span class='st_linkedin_large' displayText='LinkedIn'></span></td>
<?php }if($reddit == 1){ ?>
<td><span class='st_reddit_large' displayText='Reddit'></span></td>
<?php }if($myspace == 1){ ?>
<td><span class='st_myspace_large' displayText='MySpace'></span></td>
<?php } if($delicious == 1){ ?>
<td><span  class='st_delicious_large' ></span></td>
<?php }if($share == 1){ ?>
<td><span class='st_sharethis_large' displayText='ShareThis'></span></td>
<?php }if($google == 1){ ?>
<td><span class='st_plusone_large' displayText='Google +1'></span></td>
<?php }if($pintrest == 1){ ?>
<td><span class="pinterest-btn">
	<a href="javascript:exec_pinmarklet();" class="pin-it-btn" title="Pin It on Pinterest"></a>
</span></td>
<?php  } ?>

</tr></table>

</div>
<?php }

if($style == 'style3') { ?>
<div id='style3' class="<?php echo $params->get('moduleclass_sfx'); ?>">
<?php if($twitter == 1){ ?>
<span class='st_twitter_vcount' displayText='Tweet' st_via="<?php echo $twitter_username; ?>"></span>
<?php }if($email == 1){ ?>
<span class='st_email_vcount' displayText='Email'></span>

<?php }if($facebook == 1){ ?>
<span class='st_facebook_vcount' displayText='Facebook'></span>
<?php }if($tumblr == 1){ ?>
<span class='st_tumblr_vcount' displayText='Tumblr'></span>
<?php }if($technorati == 1){ ?>
<span class='st_technorati_vcount' displayText='Technorati'></span>
<?php }if($stumble == 1){ ?>
<span class='st_stumbleupon_vcount' displayText='StumbleUpon'></span>
<?php }if($linkedin == 1){ ?>
<span class='st_linkedin_vcount' displayText='LinkedIn'></span>
<?php }if($reddit == 1){ ?>
<span class='st_reddit_vcount' displayText='Reddit'></span>
<?php }if($myspace == 1){ ?>
<span class='st_myspace_vcount' displayText='MySpace'></span>
<?php }if($delicious == 1){ ?>
<span  class='st_delicious_vcount' displayText='Delicious'></span>
<?php }if($pintrest == 1){ ?>
<span class='st_pinterest_vcount' displayText='Pinterest'></span>
<?php }if($google == 1){ ?>
<span class='st_googleplus_vcount' displayText='Google +'></span>
<?php }if($share == 1){ ?>
<span class='st_sharethis_vcount' displayText='ShareThis'></span>
<?php }if($facebook_like == 1){ ?>
<span class='st_fblike_vcount' displayText='Facebook Like'></span>
<?php } ?>
</div>
<?php } 

if($style == 'style4') { ?>
<div id="style4" class="<?php echo $params->get('moduleclass_sfx'); ?>">
<?php if($twitter == 1){ ?>
<span class='st_twitter_button' displayText='Tweet' st_via="<?php echo $twitter_username; ?>"></span>
<?php }if($facebook == 1){ ?>
<span class='st_facebook_button' displayText='Facebook'></span>
<?php }if($email == 1){ ?>
<span class='st_email_button' displayText='Email'></span>
<?php } if($yahoo == 1){ ?>
<span  class='st_yahoo_bmarks_button' displayText='Bookmarks'></span>
<?php } if($tumblr == 1){ ?>
<span class='st_tumblr_button' displayText='Tumblr' ></span>
<?php } if($technorati == 1){ ?>
<span class='st_technorati_button' displayText='Technorati' ></span>
<?php } if($stumble == 1){ ?>
<span class='st_stumbleupon_button' displayText='StumbleUpon' ></span>
<?php } if($linkedin == 1){ ?>
<span class='st_linkedin_button' displayText='LinkedIn'></span>
<?php }if($reddit == 1){ ?>
<span class='st_reddit_button' displayText='Reddit' ></span>
<?php }if($myspace == 1){ ?>
<span class='st_myspace_button' displayText='MySpace' ></span>
<?php } if($delicious == 1){ ?>
<span class='st_delicious_button' displayText='Delicious' ></span>
<?php }if($share == 1){ ?>
<span class='st_sharethis_button' displayText='ShareThis'></span>
<?php }if($google == 1){ ?>
<span class='st_plusone_button' ></span>
<?php }if($pintrest == 1){ ?>
<span class="pinterest-btn">
	<a href="javascript:exec_pinmarklet();" class="pin-it-btn" title="Pin It on Pinterest"></a>
</span>
<?php  } ?>



</div>
<?php } ?> 
 
