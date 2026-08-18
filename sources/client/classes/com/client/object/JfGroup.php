<?php
/**
 * An Estancia group object.
 *
 * @package		com.core.object
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JfGroup extends JfSysObject
{
	 /**
	 * Add a group to the current group
	 *
	 * @access	public
	 * @param	String			The group Id to add.
	 * @throws	JfException		if a server error occurs
	 */
	public function addGroup($groupId)
	{
		try
		{
			$this->appendValue('groups_ids', $groupId);
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Add a user to the current group
	 *
	 * @access	public
	 * @param	String			The user Id to add.
	 * @throws	JfException		if a server error occurs
	 */
	public function addUser($userId)
	{
		try
		{
			$this->appendValue('users_ids', $userId);
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Remove a group from the current group
	 *
	 * @access	public
	 * @param	String			The group Id to remove.
	 * @throws	JfException		if a server error occurs
	 */
	public function removeGroup($groupId)
	{
		try
		{
			$valueIndex = $this->findValue('groups_ids', $groupId);
			if ($valueIndex >= 0)	{$this->remove('groups_ids', $valueIndex);}
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Remove a user from the current group
	 *
	 * @access	public
	 * @param	String			The user Id to remove.
	 * @throws	JfException		if a server error occurs
	 */
	public function removeUser($userId)
	{
		try
		{
			$valueIndex = $this->findValue('users_ids', $userId);
			if ($valueIndex >= 0)	{$this->remove('users_ids', $valueIndex);}
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}
}
?>