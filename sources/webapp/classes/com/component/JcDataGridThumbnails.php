<?php
/**
 * The JcDataGridThumbnails class.
 * Usage :
 *
 * $datagrid = new JcDataGridThumbnails($this->user, $this->nlsProperties);
 * $datagrid->setModal(true);
 * $datagrid->setTitle($this->name);
 * $datagrid->setObjects($this->objects);
 * $datagrid->render();
 *
 * @package		com.component
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JcDataGridThumbnails extends JcDataGrid
{
	/**
	 * Render the grid
	 *
	 * @access	public
	 */
	public function render()
	{
		$objects = $this->getObjects();
		// No Result Found
		if (!isset($objects) || sizeof($objects) == 0)
		{
			echo '<div id="objectgridcontent" style="height: 24px; line-height:24px; padding: 0px;">';
			echo '<div style="border: 0px solid black; float: left; text-align: left; width: 24px; margin: 32px 0 0 32px;"><img src="'._APP_ROOT_.'/webapp/themes/default/images/icons/warning_16.png" style="width: 16px; height: 16px; margin: 4px 0 0 4px;"></div>';
			echo '<div style="border: 0px solid black; float: left; text-align: left; width: 38%; margin: 32px 0 0 16px;">'.$this->getString('EMPTY_FOLDER').'</a></div>';

			echo '</div>';
			return;
		}
		// Content (no title)
		echo '<div id="objectgridcontent">';
		$images = new JcIconList($this->getUser());
		foreach ($objects as $object)
		{
			$link = $object->getValue('_link_name_');
			$icon = $images->getIcon($object, 64);
			$flag = $object->getValue('_lock_icon_');
			$width = 64; $height = 64; $top = '';
			if (strpos($icon, 'tn_') > -1) list($width, $height) = getimagesize(_DOCUMENT_ROOT_.$icon);
			// JcLogger::info('icon ('.$width.', '.$height.'): '.$icon);
			// Resize the image if needed
			if ($width < $height)
			{
				$width = 64 * $width / $height;
				$height = 64;
			}
			else if ($width > $height)
			{
				$height = 64 * $height / $width;
				$top = (64 - $height) / 2;
				$top = ' margin-top: '.$top.'px;';
				$width = 64;
			}

			$this->showBox(array(	'objectId' => $object->getValue('r_object_id'),
									'lock' => $flag,
									'icon' => $link['open'].'<img src="'.$icon.'" style="width: '.$width.'px; height: '.$height.'px;'.$top.'">'.$link['close'],
									'content' => $link['open'].$object->getValue($this->getTitle()).$link['close']
								));
		}
		echo '</div>';
	}

	/**
	 * Show the content of a cell
	 * $param = array('objectId' => '24', 'lock' => '&nbsp;', 'link' => '&nbsp;', 'icon' => '&nbsp;', 'content' => '&nbsp;');
	 *
	 * @param	array	param	an array of details to display
	 * @access	private
	 */
	private function showBox($param)
	{
		$objectId = ((isset($param['objectId'])) ? $param['objectId'] : '');
		$lock = ((isset($param['lock'])) ? $param['lock'] : '');
		$link = ((isset($param['link'])) ? $param['link'] : '');
		$icon = ((isset($param['icon'])) ? $param['icon'] : '');
		$content = ((isset($param['content'])) ? $param['content'] : '');

		echo '<div class="box '.$this->getObjectClass().'" id="'.$objectId.'">';
		echo '<div class="boxmain">';
		echo '<div class="boxlock">'.$lock.'</div><div id="clear"></div>';
		echo '<div class="boximg">';
		echo $icon;
		echo '</div>';
		echo '</div>';
		echo '<div class="boxtitle">';
		echo $content;
		echo '</div>';
		echo '</div>';
	}
}
?>