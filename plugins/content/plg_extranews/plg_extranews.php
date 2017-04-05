<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Content.plg_extranews
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Extranews plugin class.
 *
 * @package     Joomla.Plugin
 * @subpackage  Content.plg_extranews
 * @since       1.5
 */

$com_path = JPATH_SITE.'/components/com_content/';
require_once $com_path.'router.php';
require_once $com_path.'helpers/route.php';

//JModelLegacy::addIncludePath($com_path . '/models', 'ContentModel');

class plgContentPlg_extranews extends JPlugin
{
	var $type, $term, $IsIphone = false, $extranewsOn, $catid;
	var $mainframe, $doc;
	
	public function __construct(& $subject, $config)
	{  
		parent::__construct($subject, $config);    
		$this->mainframe = JFactory::getApplication();
		$this->doc = JFactory::getDocument();
		if(strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') || strpos($_SERVER['HTTP_USER_AGENT'], 'iPod')) $this->IsIphone = true;
		$this->extranewsOn = true;
		$this->type = 'n';
		$this->catid = -1;
		$this->term = null;
		if (JPlugin::loadLanguage('plg_content_extranews')) ;  //Call language file 
        else JPlugin::loadLanguage('plg_content_extranews', JPATH_ADMINISTRATOR);
	} 

	/**
	 * Extranews prepare content method
	 *
	 * Method is called by the view
	 *
	 * @param	string	The context of the content being passed to the plugin.
	 * @param	object	The content object.  Note $article->text is also available
	 * @param	object	The content params
	 * @since	1.6
	 */
	public function onContentBeforeDisplay($context, &$article, &$params, $limitstart = 0) //onContentPrepare //onContentBeforeDisplay
	{
		$option     = JRequest::getCmd('option'); //JRequest::getCmd
		$document = JFactory::getDocument();
		$view    = JRequest::getCmd('view'); //JRequest::getCmd
		$plugin_enabled = $this->params->get('enabled','1');
		
		$regex = "#{extranews\s*(.*?)}(.*?){/extranews}#s";
		if($this->doc->gettype()=='pdf' || !$plugin_enabled){
			$article->text = preg_replace($regex, '', $article->text);
			return;
		}

		if($plugin_enabled && $option=="com_content" && $view=="article"){

			$this->extranewsOn = true;
			
			//Check for Category(ies)
			$render_method = $this->params->get("render_method", 0);
			$render_cat = $this->params->get("render_cat", null);
			
			if($render_method && $render_cat) {
				if(!is_array($render_cat)) $render_cat = array($render_cat);
				if($render_method == 1) $this->extranewsOn = in_array($article->catid, $render_cat);
				else $this->extranewsOn = !in_array($article->catid, $render_cat);
			}
			//Revise all any existed extranews tag(s)
			if (preg_match_all($regex, $article->text, $matches, PREG_SET_ORDER) > 0) {
				foreach ($matches as $match) {
					$match[2] = trim(strip_tags($match[2]));
					$theparams = JUtility::parseAttributes(htmlspecialchars_decode(trim(strip_tags($match[1])))); //parameters
					$nitems = isset($theparams["items"])?$theparams["items"]:1;
					$title = isset($theparams["title"])?$theparams["title"]:'';
					$this->catid = isset($theparams["catid"])?$theparams["catid"]:-1;
					$this->term = isset($theparams["term"])?$theparams["term"]:null;
					
					$replace = '';
					switch($match[2]) {
						case 'off':
							
							$this->extranewsOn = false;
							$article->text = preg_replace($regex, '', $article->text);
							//return;						
						break;
						case 'related':
						case 'newer':
						case 'older':
						case 'latest':
						case 'random':
						case 'user':
						case 'popular':
							$this->type = $match[2][0]; 
							if($match[2] == 'random') $this->type = 'm';
							$article->text = preg_replace($regex, '<!--extranews-->'.$this->built_news_list($article,$nitems,$title).'<!--/extranews-->', $article->text, 1);
							JHTML::_('behavior.tooltip');
							/* add styles and scripts */ 
							$document->addStyleSheet(JURI::base() . 'plugins/content/plg_extranews/css/extranews.css');						
						break;
					}					
				}
			}
		
			if($this->extranewsOn){

				JHTML::_('behavior.tooltip');
				/* add styles and scripts */ 
				$document->addStyleSheet(JURI::base() . 'plugins/content/plg_extranews/css/extranews.css');				
				$content = '';

				$showextranews = $this->params->get('showextranews', 'r,n,o');
				if(strpos($showextranews,',')>0) $showextranews = explode(',',$showextranews); else $showextranews = array($showextranews);
				
				foreach($showextranews as $thenews) switch($thenews){
					case 'r': //Realted
					case 'n': //Newer
					case 'l': //Latest
					case 'o': //Older
					case 'm': //Random
					case 'u': //User
					case 'p': //Most read
						$this->type = $thenews;
						$content .= $this->built_news_list($article,$this->params->get("{$thenews}_items",''),$this->params->get("{$thenews}_title",''));
						//reset
						$this->catid = -1;						
					break;
					default: $content .= '';			
				}
				//assigned content
				if($content) 
					$article->text .= '<div class="extranews_separator"></div>' . $content;
			}
		}
	}
	
	function built_news_list(& $article, $numitems = false, $overidetitle = false) {
		$listtitle = array('n'=>JText::_('LBL_NEWERNAME'), 'o'=>JText::_('LBL_OLDERNAME'),'r'=>JText::_('LBL_RELATEDNAME'),'l'=>JText::_('LBL_LATESTNAME'),'m'=>JText::_('LBL_RANDOMNAME'), 'p'=>JText::_('LBL_HITNAME'));
		$listclass = array('n'=>'newer', 'o'=>'older','r'=>'related','l'=>'latest', 'm'=>'random', 'p'=>'popular');
		$type = $this->type;
		$content = '';
		if ($type == 'l') $heading = $this->params->get("n_heading",'h3');
		else $heading = $this->params->get("{$type}_heading",'h3'); 
		
		if($overidetitle == false) $overidetitle = $listtitle[$type];
		
		if($ItemsList = $this->ExtranewsItems($article,$numitems)){	
			$content .= '<div class="extranews_box"><'.$heading.'><strong>'.JText::_($overidetitle).'</strong></'.$heading.'><div class="extranews_'.$listclass[$type].'"><ul class="'.$listclass[$type].'">';
			$tooltip = $this->params->get("{$type}_enable_tooltip",'1'); 
			foreach($ItemsList as $item){
						$content .= '<li>'.$this->built_tooltip($item, $tooltip).'</li>';
			}
			$content .= '</ul></div></div>';
		}
		return $content;
		
	}
	
	function built_tooltip(& $item, $tooltip = '1')
	{
		if($this->type == 'l') $type = 'n'; else $type = $this->type;
		
		$title = JText::sprintf($this->params->get($type.'_linkedtitleformat','%1$s - %2$s - %3$s'), $item['title'], '<span class="extranews_date">'.$item['date'].'</span>','<span class="extranews_hit">'.$item['hits'].'</span>');
		$t_content = '<p class="'.$this->params->get($type.'_textalign','extranews_justify').'">'.$item['intro'].'</p>'; 
		$img_align = $this->params->get($type.'_imgalign','icenter');
		$img = ''; 
		if($item['img']) $img = '<img src="'.$item['img'].'" alt="" class="iextranews '.$img_align.'" />';

		if($img_align == 'icenter2') $t_content = $t_content.'<pan class="extranews_clear"></span>'.$img;
		else $t_content = $img.$t_content;
		if($tooltip=='1') return JHTML::tooltip($t_content, '<b>'.$item['title'].'</b>', '', $title, $item['link']);
		else return '<a href="'.$item['link'].'" title="'.$item['title'].'">'.$title.'</a>';
	}
	
///////////////////////////////////////////////////////////////////////////////////////////
	function ExtranewsItems(&$article, $article_items = null) 
	{
		$db			= JFactory::getDbo();
		$app		= JFactory::getApplication();
		$user		= JFactory::getUser();
		$groups		= implode(',', $user->getAuthorisedViewLevels());
		$date		= JFactory::getDate();
		
		/* prepare database */
		$query		= $db->getQuery(true);
		$nullDate	= $db->getNullDate();
		$now		= $date->toSql(); //date('Y-m-d H:i:s');
		$related	= array();
		
		//$type
		//o: older news
		//n: newer news
		//r: related news
		//l: latest news -> like newer
		//m: random news
		$type = $this->type;
		if($type == 'l') $type= 'n';
		$nitems 		= $this->params->get ($type.'_items',7);
		if($article_items) $nitems = (int) $article_items;
		$ordering 		= $this->params->get ($type.'_ordering',5);// 1 = ordering | 2 = popular | 3 = random 
		$chars 			= $this->params->get ($type.'_chars',150);
		$allow_tags		= $this->params->get ($type.'_allow_tags');
		$allow_tags = str_replace(" />", ">", $allow_tags);			
		$date_format	= $this->params->get ($type.'_dateformat',' (Y-m-d H:m)');
		$time_zone	= $this->params->get ('timezone',1); //0: Global 1: User timezone
		
		$imgW 		= $this->params->get ($type.'_imgw',90);
		$imgH 		= $this->params->get ($type.'_imgh',68);
		

		
		$contentConfig = JComponentHelper::getParams( 'com_content' );
		$access		= ! JComponentHelper::getParams('com_content')->get('show_noauth');

		$config = JFactory::getConfig();
		if($time_zone) $offset = $user->getParam('timezone');
		else $offset = $config->get('config.offset');
			
		
/***********************************************************/
					$query->clear();
					
					//$query->select('a.id');
					//$query->select('a.title');
					//$query->select('a.introtext');
					//$query->select('DATE_FORMAT(a.created, "%Y-%m-%d") as created');
					//$query->select('a.catid');
					$query->select('a.*');
					$query->select('cc.access AS cat_access');
					$query->select('cc.published AS cat_state');
					$query->select('cc.title AS cattitle');

					// Sqlsrv changes
					$case_when = ' CASE WHEN ';
					$case_when .= $query->charLength('a.alias', '!=', '0');
					$case_when .= ' THEN ';
					$a_id = $query->castAsChar('a.id');
					$case_when .= $query->concatenate(array($a_id, 'a.alias'), ':');
					$case_when .= ' ELSE ';
					$case_when .= $a_id.' END as slug';
					$query->select($case_when);

					$case_when = ' CASE WHEN ';
					$case_when .= $query->charLength('cc.alias', '!=', '0');
					$case_when .= ' THEN ';
					$c_id = $query->castAsChar('cc.id');
					$case_when .= $query->concatenate(array($c_id, 'cc.alias'), ':');
					$case_when .= ' ELSE ';
					$case_when .= $c_id.' END as catslug';
					$query->select($case_when);
					$query->from('#__content AS a');
					//$query->leftJoin('#__content_frontpage AS f ON f.content_id = a.id');
					$query->leftJoin('#__categories AS cc ON cc.id = a.catid');
					
					$query->where('a.state = 1');				
					$query->where('a.access IN (' . $groups . ')');
					
					if($this->type !='l')
						$query->where('a.id != ' . (int) $article->id);
					
					/* set items order */
					$ord = array(
						1=>'ordering',
						2=>'hits DESC',
						3=>'RAND()',
						4=>'a.created ASC',
						5=>'a.created DESC'
					);
				
					switch ($this->type){
						case 'n'://newer
							$query->where('a.created >= '.$db->Quote($article->created));
							$ordering = 4;
						break;				
						case 'l':
							$ordering = 5;
						break;
						case 'o'://older
							$ordering = 5;
							$query->where('a.created <= '.$db->Quote($article->created));
						break;
						case 'm'://Random
							$ordering = 3;
						break;
						case 'p'://Most read - hits
							$ordering = 2;
						break;			
						case 'u'://User
							$ordering = 5;
							$query->where('a.created_by = '. (int) $article->created_by);
						break;			
						case 'r':
							$this->catid = 'all';
							$likes = array();
							if(is_null($this->term) && empty($article->metakey)) return null;
							if($this->term) $keys = explode( ',', $this->term );
							else $keys = explode( ',', $article->metakey );
							// assemble any non-blank word(s)
							foreach ($keys as $key) {
								$key = trim( $key );
								if ($key) {
									$likes[] = $db->escape( $key );
								}
							}
					
							$concat_string = $query->concatenate(array('","', ' REPLACE(a.metakey, ", ", ",")', ' ","'));
							$query->where('('.$concat_string.' LIKE "%'.implode('%" OR '.$concat_string.' LIKE "%', $likes).'%")'); //remove single space after commas in keywords)
						default:
					}

					$order = $ord[$ordering];
					
					if ($this->catid < 0) $catid = $article->catid; else $catid = $this->catid;
					
					if($catid != 'all'){						
						if(strpos($catid, ',') > 0) {
							$catlist = explode ( ',' , $catid);
								for($i = 0; $i < count($catlist); $i++) $catlist[$i] = intval($catlist[$i]);
							$query->where('cc.id IN (' . implode ( ',' , $catlist).')');				
							}
						else $query->where('cc.id = '. (int) $catid);
					}
	
					
					$query->where('(a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).')');
					$query->where('(a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).')');

					// Filter by language
					if ($app->getLanguageFilter()) {
						$query->where('a.language in (' . $db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')');
					}
					
					$query->order($order);					
					
					//if($this->type =='n') JError::raiseWarning(500, $catid." SQL: <br />". $query);
					$db->setQuery($query,0,$nitems);
					try
					{
						$loaded_items = $db->loadObjectList();
					}
					catch (RuntimeException $e)
					{
						JError::raiseWarning(500, $e->getMessage());
						return false;
					}
					//Reverse Ordering list
					if($this->type =='n') $loaded_items = array_reverse($loaded_items);
		//Reconstrruct article items
		$extranews_items =array();

		foreach ( $loaded_items as $item ) {
			//$item->slug = $item->id.':'.$item->alias;
			//$item->catslug = $item->catid.':'.$item->category_alias;

			if ($access || in_array($item->access, $authorised)) {
				// We know that user has the privilege to view the article
				$item->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug));
			} else {
				$item->link = JRoute::_('index.php?option=com_users&view=login');
			}
			
			$intro = trim($this->removetag(strip_tags($item->introtext,$allow_tags)));

			$intro = $this->truncate($intro, $this->params->get ($type.'_chars',150));
			
			$extranews_item = array( //Offset TIME
					'date' 	=> JHTML::_('date', $item->created,JText::_($date_format),$offset),
					'intro' 	=> $intro,
					'link' 	=> htmlspecialchars($item->link, ENT_QUOTES, 'UTF-8'),
					'img' 		=>$this->sized_image($item, $imgW,$imgH),
					'title' 	=> htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'),
					'id'	 	=> $item->id,
					'hits'	 	=> JText::sprintf(JText::_('LBL_HITSTRING'),$item->hits), //LBL_HITSTRING
					'ctitle' 	=> htmlspecialchars($item->cattitle, ENT_QUOTES, 'UTF-8')
				);

				$extranews_items[] = $extranews_item;				
			}			
		return $extranews_items;			
	} 
private function make_folder($rootpath, $imagefolder, $chmod = 0755){
	$folders = explode(DS,$imagefolder);         
	$tmppath = $rootpath;
	for($i=0;$i <= count($folders)-1; $i++){
        if(!file_exists($tmppath.$folders[$i])) {
		if(!mkdir($tmppath.$folders[$i],$chmod)) return 0; //can not create folder
	} //Folder exist
        $tmppath = $tmppath.$folders[$i].DS;
        //make a blank content
        $ffilename = $tmppath . 'index.html';
	        if(!file_exists($ffilename)){
	                $filecontent = '<html><body bgcolor="#FFFFFF"></body></html>';
	                $handle = fopen($ffilename, 'x+');
	                fwrite($handle, $filecontent);
	                fclose($handle);  
	        }       
        }
	return 1;
}	

///////////////////////////////////////////////////////////////////////////////////////////
	public static function _cleanIntrotext($introtext)
	{
		$introtext = str_replace('<p>', ' ', $introtext);
		$introtext = str_replace('</p>', ' ', $introtext);
		$introtext = strip_tags($introtext, '<a><em><strong>');

		$introtext = trim($introtext);

		return $introtext;
	}

	/**
	* Method to truncate introtext
	*
	* The goal is to get the proper length plain text string with as much of
	* the html intact as possible with all tags properly closed.
	*
	* @param string   $html       The content of the introtext to be truncated
	* @param integer  $maxLength  The maximum number of charactes to render
	*
	* @return  string  The truncated string
	*/
	public static function truncate($html, $maxLength = 0)
	{
		$baseLength = strlen($html);
		$diffLength = 0;

		// First get the plain text string. This is the rendered text we want to end up with.
		$ptString = JHtml::_('string.truncate', $html, $maxLength, $noSplit = true, $allowHtml = false);

		for ($maxLength; $maxLength < $baseLength;)
		{
			// Now get the string if we allow html.
			$htmlString = JHtml::_('string.truncate', $html, $maxLength, $noSplit = true, $allowHtml = true);

			// Now get the plain text from the html string.
			$htmlStringToPtString = JHtml::_('string.truncate', $htmlString, $maxLength, $noSplit = true, $allowHtml = false);

			// If the new plain text string matches the original plain text string we are done.
			if ($ptString == $htmlStringToPtString)
			{
				return $htmlString;
			}
			// Get the number of html tag characters in the first $maxlength characters
			$diffLength = strlen($ptString) - strlen($htmlStringToPtString);

			// Set new $maxlength that adjusts for the html tags
			$maxLength += $diffLength;
			if ($baseLength <= $maxLength || $diffLength <= 0)
			{
				return $htmlString;
			}
		}
		return $html;
	}	

/***************************************************************************************************/
	function sized_image($article, $w=88, $h=68)
	{
		$w = (int) $w;
		$h = (int) $h;
		$rootpath = JPATH_SITE.DS.'images'.DS;
		$imagefolder = 'plg_imagesized';
		
		if(!file_exists($rootpath.$imagefolder)) $this->make_folder($rootpath,$imagefolder);
		
		$urlbaseimage = JURI::base(true).'/images/' . $imagefolder . '/';
		$images = json_decode($article->images);
		$articleImag = $images->image_intro;
		if( $articleImag == '' ) $articleImag = $images->image_fulltext;
		if( $articleImag == '' ) $articleImag = $this->search_extranews_image( $article->introtext );
		if( $articleImag == '' ) $articleImag= $this->search_extranews_image( $article->fulltext );
		
		if($articleImag){			
			$filepart = explode('/', $articleImag);
			$filename  = array_pop($filepart);
			$thumb = "thumb_{$w}_{$h}_".$filename;
			$thenewimg = $rootpath.$imagefolder.DS.$thumb;
			$imgx = $this->extranews_image_fixlink($articleImag);
			if(!file_exists($thenewimg) && $this->isimage($imgx) ){
				/*******Load class SimpleImage****************************************************/
				   if (!class_exists('SimpleImage'))
				   ////plg_imagesized
					  include(JPATH_SITE.DS.'plugins'.DS.'content'.DS.'plg_extranews'.DS.'lib'.DS.'simpleimage.php');
				/*********************************************************************************/  			
				$image = new SimpleImage();								 
				$image->load($imgx);
				$image->resize($w,$h);
				$returnvalue = $image->save($thenewimg);
				if(!$returnvalue)return $articleImag;
			}
			if(file_exists($thenewimg)) $articleImag= $urlbaseimage.$thumb;
		}
				
		return $articleImag;		
	}

	/**
	 * Searches for all images inside a text and returns the first one found
	 *
	 * @param string $content
	 * @return string
	 */
	function search_extranews_image( $content )
	{		
		preg_match_all("#\<img(.*)src\=\"(.*)\"#Ui", $content, $mathes);		
		return isset($mathes[2][0]) ? $mathes[2][0] : '';			
	}	

	function extranews_image_fixlink($articleImag){
		if(strpos($articleImag, 'http://') === 0) return $articleImag; 
		else return (JPATH_SITE.DS.str_replace('/',DS,$articleImag));
	}
	function isimage($imageurl)
	{
		return in_array(strtolower(substr(strrchr($imageurl, "."), 1)), array('gif','jpg','png','jpeg'));
	} 

	function strip_newline($text){
		//strip \r\n
		$order = array("\r\n","\n","\r");
		//$replace = '<br />';
		$replace = ' ';
		$text=str_replace($order,$replace,$text);
	return $text;
	}
  
	function removetag($string){
		  $pattern = "|{[^}]+}(.*){/[^}]+}|U";
		  $replacement = '';
		  return preg_replace($pattern, $replacement, $string);
	}
}