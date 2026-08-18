<?php
/**
 * An Estancia JwBreadCrumbTag tag.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwBreadCrumbTag extends JwTag
{
	/**
	 * Render the custom tag :
	 * <jm:breadcrumb class="Class" pageNumber="1" pageCount="3" pageFirst="..." pagePrev="..." pageNext="..." pageLast="..." resultCount="15" displayLinks="true" displayView="true"/>
	 *
	 * <div class="breadcrumb">
	 * 	<div class="breadcrumbnav">
	 * 		<div class="breadcrumbinnernav">
	 * 			<?php echo $class->getPageLink('first');?>
	 * 			<?php echo $class->getPageLink('prev');?>
	 * 			<span><?php echo $class->getPageNumber();?> / <?php echo $class->getPageCount();?></span>
	 * 			<?php echo $class->getPageLink('next');?>
	 * 			<?php echo $class->getPageLink('last');?>
	 * 		</div>
	 * 	</div>
	 * 	<div class="breadcrumbresults">
	 * 		<?php echo $class->getResultsCount();?>
	 * 	</div>
	 * 	<div class="breadcrumbview">
	 * 		<?php echo $class->getPageView();?>
	 * 	</div>
	 * </div>
	 *
	 * @access	public
	 * @param	array	the array to modify
	 * @return	String	the string to display
	 */
	public static function render($tag)
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Starts the output buffer
		ob_start();
		// Initialize the attributes
		$attributes = $tag['attributes'];
		// Get localized messages
		if (isset($tag['properties']))	{$nlsProperties = $tag['properties'];}
		// Get the breadcrumb values
		if (isset($attributes['values']))
		{
			$bcValues = unserialize(html_entity_decode($attributes['values']));
			$bcValues['properties'] = $nlsProperties;
			// Initialise the variables
//			$bcValues = $objectList->getBreadCrumbValues();
			$pageNumber = $bcValues['pageNumber'];
			$pageCount = self::getPageCount($bcValues);
			// Render the tag
			echo '<div class="breadcrumb">';
			echo '<div class="breadcrumbresults">'.self::getResultsCount($bcValues).'</div>';
			if (isset($attributes['displayLinks']) && (($attributes['displayLinks'] == '' && $pageCount > 1) || ($attributes['displayLinks'] == 'true')))
			{
				echo '<div class="breadcrumbnav">';
				echo self::getPageLink($bcValues, 'first');
				echo self::getPageLink($bcValues, 'prev');
				echo '<span>'.$pageNumber.' / '.$pageCount.'</span>';
				echo self::getPageLink($bcValues, 'next');
				echo self::getPageLink($bcValues, 'last');
				echo '</div>';
			}
			echo '</div>';
		}
		// Return the tag
		$output = ob_get_clean();
		return $output;
	}

	/**
	 * Get the page count
	 *
	 * @access	private
	 * @return	String	the page count
	 */
	private static function getPageCount($bcValues)
	{
		$pageCount = (int)($bcValues['resultsCount']/$bcValues['resultsSize']);
		if ($pageCount < $bcValues['resultsCount']/$bcValues['resultsSize'])	{$pageCount = $pageCount + 1;}
		if ($pageCount == 0)	{$pageCount = 1;}
		return $pageCount;
	}

	/**
	 * Get the page link
	 *
	 * @access	public
	 * @return	String	the page link
	 */
	private static function getPageLink($bcValues, $target)
	{
		$icon = '';
		$page = 0;
		$pageNumber = $bcValues['pageNumber'];
		switch ($target)
		{
			case 'first' :	
						if ($pageNumber == 1)	{$icon = 'player_rew_grey';}
						else	{$icon = 'player_rew';$page = 1;}
						break;
			case 'prev' :
						if ($pageNumber == 1)	{$icon = 'player_back_grey';}
						else	{$icon = 'player_back';$page = $pageNumber - 1;}
						break;
			case 'next' :
						if ($pageNumber == self::getPageCount($bcValues))	{$icon = 'player_play_grey';}
						else	{$icon = 'player_play';$page = $pageNumber + 1;}
						break;
			case 'last' :
						if ($pageNumber == self::getPageCount($bcValues))	{$icon = 'player_fwd_grey';}
						else	{$icon = 'player_fwd';$page = self::getPageCount($bcValues);}
						break;
		}
		$link = '<img src="'._APP_ROOT_.'/webapp/themes/default/images/icons/'.$icon.'.png">';
		if ($page > 0)
		{
			// // Get the Http Session info
			$httpsession = new JcHttpSession();
			$componentList = $httpsession->getAttribute('component');
			$component = (is_array($componentList)) ? end($componentList) : '';
			// Get the search string
			$search = (($bcValues['strSearch'] <> '') ? ';search='.$bcValues['strSearch'].';' : '');
			// Get the sort string
			$sort = '';
			if ($bcValues['sort'] <> '')	{$sort = ';sort='.$bcValues['sort'].';order='.$bcValues['order'].';';}
			// Set the script
			$jscript = "'objectlist', 'jump', '".$component."', null, 'path=.;page=".$page.$sort.$search."'";
			$link = '<a href="javascript:postServerEvent('.$jscript.');">'.$link.'</a>';
		}
		return $link;
	}

	/**
	 * Get the results count
	 *
	 * @access	public
	 * @return	String	the results count
	 */
	private static function getResultsCount($bcValues)
	{
		$result = self::getString($bcValues['properties'], 'RESULT');
		if ($bcValues['resultsCount'] > 1)	{$result = self::getString($bcValues['properties'], 'RESULTS');}
		$msgResultCount = self::getString($bcValues['properties'], 'FOUND').' '.$bcValues['resultsCount'].' '.$result;
		return '<span>'.$msgResultCount.'</span>';
	}

	/**
	 * Get the results count
	 *
	 * @access	public
	 * @return	String	the results count
	 */
	private static function getString($nlsProperties, $message)
	{
		return JcUtils::getString($nlsProperties, $message);
	}
}
?>