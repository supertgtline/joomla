<?php
	/*
		# mod_sp_social - Social module by JoomShaper.com
		# Author    	JoomShaper http://www.joomshaper.com
		# Copyright 	2010 - 2013 JoomShaper.com. All Rights Reserved.
		# License	 	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
		# Website 		http://www.joomshaper.com
	*/
    // no direct access
    defined('_JEXEC') or die('Restricted access');	
?>	
	
	<div id="sp_social<?php echo $ID ?>" class="sp_social icon_size_<?php echo $icon_size ?> <?php echo $params->get('moduleclass_sfx'); ?>">
	<ul>
		<?php if ($rss) : //rss ?>
			<li><a <?php echo $target ?> title="<?php echo JText::_('SP_SOCIAL_RSS') ?>" href="<?php echo $rss ?>"><i class="fa fa-rss"></i></a></li>
		<?php endif; ?>	
		
		<?php if ($facebook) : //facebook ?>
			<li><a <?php echo $target ?>  title="<?php echo JText::_('SP_SOCIAL_FACEBOOK') ?>" href="http://www.facebook.com/<?php echo $facebook ?>"><i class="fa fa-facebook"> </i></a></li>
		<?php endif; ?>
		
		<?php if ($twitter) : //twitter ?>
			<li><a <?php echo $target ?> title="<?php echo JText::_('SP_SOCIAL_TWITTER') ?>" href="http://www.twitter.com/<?php echo $twitter ?>"><i class="fa fa-twitter"></i></a></li>
		<?php endif; ?>	
		
		<?php if ($goggleplus) : //google plus ?>
			<li><a <?php echo $target ?>  title="<?php echo JText::_('SP_SOCIAL_GOOGLE_PLUS') ?>" href="https://plus.google.com/<?php echo $goggleplus ?>"><i class="fa fa-google-plus"></i></a></li>
		<?php endif; ?>	
		
		<?php if ($youtube) : //youtube ?>
			<li><a <?php echo $target ?> title="<?php echo JText::_('SP_SOCIAL_YOUTUBE') ?>" href="http://www.youtube.com/<?php echo $youtube ?>"><i class="fa fa-youtube"></i></a></li>
		<?php endif; ?>	
		
		<?php if ($pinterest) : //pinterest ?>
			<li><a <?php echo $target ?> title="<?php echo JText::_('SP_SOCIAL_PINTEREST') ?>" href="http://www.pinterest.com/<?php echo $pinterest ?>"><i class="fa fa-pinterest"></i></a></li>
		<?php endif; ?>	
		
		<?php if ($linkedin) : //linkedin ?>
			<li><a <?php echo $target ?> title="<?php echo JText::_('SP_SOCIAL_LINKEDIN') ?>" href="http://www.linkedin.com/<?php echo $linkedin ?>"><i class="fa fa-linkedin"></i></a></li>
		<?php endif; ?>	
		
		<?php if ($flickr) : //flickr ?>
			<li><a <?php echo $target ?> title="<?php echo JText::_('SP_SOCIAL_FLICKR') ?>" href="http://www.flickr.com/<?php echo $flickr ?>"><i class="fa fa-flickr"></i></a></li>
		<?php endif; ?>	

		<?php if ($picasa) : //picasa ?>
			<li><a <?php echo $target ?> title="<?php echo JText::_('SP_SOCIAL_PICASA') ?>" href="http://www.picasa.com/<?php echo $picasa ?>"><i class="fa fa-picasa"></i></a></li>
		<?php endif; ?>	
		
		<?php if ($myspace) : //myspace ?>
			<li><a <?php echo $target ?> title="<?php echo JText::_('SP_SOCIAL_MYSPACE') ?>" href="http://www.myspace.com/<?php echo $myspace ?>"><i class="fa fa-myspace"></i></a></li>
		<?php endif; ?>	
		
		<?php if ($digg) : //digg ?>
			<li><a <?php echo $target ?> title="<?php echo JText::_('SP_SOCIAL_DIGG') ?>" href="http://www.digg.com/<?php echo $digg ?>"><i class="fa fa-digg"></i></a></li>
		<?php endif; ?>	
		
		<?php if ($skype) : //skype ?>
			<li><a <?php echo $target ?> title="<?php echo JText::_('SP_SOCIAL_SKYPE') ?>" href="skype:<?php echo $skype ?>?call"><i class="fa fa-skype"></i></a></li>
		<?php endif; ?>	

		<?php if ($vimeo) : //vimeo ?>
			<li><a <?php echo $target ?> title="<?php echo JText::_('SP_SOCIAL_VIMEO') ?>" href="http://www.vimeo.com/<?php echo $vimeo ?>"><i class="fa fa-vimeo-square"></i></a></li>
		<?php endif; ?>	

		<?php if ($delicious) : //delicious ?>
			<li><a <?php echo $target ?> title="<?php echo JText::_('SP_SOCIAL_DELICIOUS') ?>" href="http://www.delicious.com/<?php echo $delicious ?>"><i class="fa fa-delicious"></i></a></li>
		<?php endif; ?>	
	</ul>
	<div style="clear:both"></div>
</div>