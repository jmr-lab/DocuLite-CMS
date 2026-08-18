<?php
/**
 * An Estancia JwDataGridTag tag.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwDataGridTag extends JwTag
{
	/**
	 * Render the custom tag :
	 * <jm:datagrid id="objectgrid" data="..."/>
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
		echo '<div id="'.$attributes['id'].'">';
		// Get the data values
		if (isset($attributes['data']))
		{
			// Initialise the variables
			$dataValues = unserialize(html_entity_decode($attributes['data']));
			// Render the tag
			// Show actions
			if (!self::isModal($dataValues))
			{
				$actions = new JcActions($dataValues['session'], $dataValues['folderObj'], $dataValues['objects']);
				$actions->showActions();
			}
			// Show the objects depending on the view
			if ($dataValues['view'] == 'details')
			{
				// JcDataGridDetails
				$datagrid = new JcDataGridDetails($dataValues['user'], $dataValues['nlsProperties']);
				$datagrid->setObjectGridContent($dataValues['objectgridcontent']);
				$datagrid->setColumns($dataValues['columns']);
				$datagrid->setColumnsProperties($dataValues['columnsProps']);
				$datagrid->setComponent($dataValues['component']);
				$datagrid->setEmptyMessage($dataValues['empty_message']);
				$datagrid->setFastSearch($dataValues['fastsearch']);
				$datagrid->setPageNumber($dataValues['pageNumber']);
				$datagrid->setSearch($dataValues['strSearch']);
				$datagrid->setSort($dataValues['sort'], $dataValues['order']);
				$datagrid->setTitle($dataValues['name']);
				$datagrid->setObjects($dataValues['objects']);
				if (self::isModal($dataValues))	{$datagrid->setModal(true);}
				$datagrid->render();
			}
			elseif ($dataValues['view'] == 'thumbnails')
			{
				$datagrid = new JcDataGridThumbnails($dataValues['user'], $dataValues['nlsProperties']);
				$datagrid->setTitle($dataValues['name']);
				$datagrid->setObjects($dataValues['objects']);
				$datagrid->render();
			}
		}
		// Close the tag
		echo '</div>';
		// Return the tag
		$output = ob_get_clean();
		return $output;
	}

	/**
	 * Is modal
	 *
	 * @access	private
	 * @return	Boolean	whether the current component is modal or not
	 */
	private static function isModal($dataValues)
	{
		$isModal = false;
		if (isset($dataValues['modal']) && $dataValues['modal'] == 'true')	{$isModal = true;}
		return $isModal;
	}
}
?>