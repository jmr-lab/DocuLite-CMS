<?php
/**
 * An Estancia object identifier.
 *
 * @package com.core.common
 * @author Jean-Marie Roy
 * @copyright Jean-Marie Roy 2011
 * @version 3.0
 */
class JfQuery
{
	/**
	* Query string
	*
	* @access private
	* @var String
	*/
	private $strQuery;

	/**
	* Batch size
	*
	* @access private
	* @var int
	*/
	private $batchSize = 30;

	/**
	* Batch page
	*
	* @access private
	* @var int
	*/
	private $batchOffset = 0;

	/**
	* Number of results
	*
	* @access private
	* @var int
	*/
	private $nbResults;

	/**
	 * Constructor
	 *
	 * This function initialize the query object
	 *
	 * @throws JfException if a server error occurs
	 */
	public function __construct()	{}

	/**
	 * Connect to the RDBMS.
	 *
	 * @access public
	 * @throws JfException if a server error occurs.
	 */
	public static function connect()
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		try
		{
			// Get the global var 'isConnected'
			global $isConnected;
			if ($isConnected)	{return;}
			else $isConnected = true;
			// Read the ini file
			$properties = JfUtils::getProperties(JfUtils::getIniFile('client'));
			// Get the server and database names
			$serverName = JfUtils::getServerName($properties);
			$databaseName = JfUtils::getDatabaseName($properties);
			$userName = JfUtils::getUserName($properties);
			$userPassword = JfUtils::getUserPassword($properties);
			// Get the user login details
			// Connect to the database
			$server = mysql_connect($serverName, $userName, $userPassword);
			if (!$server)
			{
				$exception = new JfException('SQL_SERVER_CONNECTION_FAILED', JfException::$JF_EXCEPTION_FATAL);
				$exception->append(mysql_error());
				throw $exception;
			}
			// Set database
			$link = mysql_select_db($databaseName, $server);
			if (!$link)
			{
				$exception = new JfException('SQL_DATABASE_CONNECTION_FAILED', JfException::$JF_EXCEPTION_FATAL);
				$exception->append(mysql_error());
				throw $exception;
			}
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Executes a SQL query.
	 *
	 * Before calling this method, assign a SQL statement to the query with the setSQL method.
	 *
	 * @access	public
	 * @param	JfSession		the current session
	 * @param	int				the type of query that you want to execute; The following list specifies the integer corresponding to all query types:
	 * Integer	Query Type
	 * 0		READ QUERY
	 * 1		QUERY
	 * 2		CACHE QUERY
	 * 3		EXECUTE QUERY
	 * 4		EXECUTE READ QUERY
	 * 5		APPLY
	 * @return	JfCollection	a collection object of query results.
	 * @throws	JfException		if the query fails to execute or if the specified session has timed out and cannot be re-established.
	 */
	public function execute($session, $queryType = 1)
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'($session, '.$queryType.')');
		try
		{
			// Prepare the query
			$strSQL = $this->prepareSQL($session, $this->getSQL());
			// Get the time (milliseconds) BEFORE the query was launched
			$before = floor(1000 * microtime());
			// Execute the query
			$query = mysql_query($strSQL);
			// Get the time (milliseconds) AFTER the query was launched
			$after = floor(1000 * microtime());
			// Get the duration of the quey
			$duration = $after - $before;
			while ($duration < 0)	{$duration = 1000 + $duration;}
			// Log an INFO event to know the time the query took to complete
			JfLogger::info('('.$duration.' ms) '.$strSQL);
			// If an error occured
			if (!$query)
			{
				$exception = new JfException('SQL_QUERY_EXECUTE_FAILED', JfException::$JF_EXCEPTION_FATAL);
				$exception->append(mysql_error());
				throw $exception;
			}
			// else the query has returned true (INSERT, UPDATE, DELETE, DROP, etc.)
			else if ($query === true)
			{
				$arr = array();
				// @todo set the number of results
				// $nbResults = mysql_num_rows($query);
				// Set the number of results
				// $this->nbResults = $nbResults;

				return new JfCollection($session, $arr);
			}
			// else the query has returned a resource
			else
			{
				$results = array();
				$nbResults = mysql_num_rows($query);

				// if ($nbResults == 1)		{$arr = mysql_fetch_assoc($query);}
				// else if ($nbResults > 1)	{while ($row = mysql_fetch_assoc($query))	{$arr[] = $row;}	}
				while ($row = mysql_fetch_assoc($query))	{$results[] = $row;}
				
				// Set the number of results
				$this->nbResults = $nbResults;

				return new JfCollection($session, $results);
			}
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Returns the current batch offset.
	 *
	 * @access public
	 * @return int the maximum number of rows that can be returned to the server in each call to the underlying RDBMS.
	 */
	public function getBatchOffset()
	{
		return $this->batchOffset;
	}
	
	/**
	 * Returns the maximum number of rows that can be returned to the server in each call to the underlying RDBMS.
	 *
	 * <strong>Usage Notes<strong> : each query that you make to a repository effectively queries the underlying RDBMS.
	 * If you know that the information returned from the RDBMS for each row is relatively small,
	 * use setBatchSize to set a higher maximum number of rows returned for each call to the RDBMS.
	 * This will reduce the number of calls to the RDBMS needed to complete a query, which provides better query performance.
	 *
	 * @access public
	 * @return int the maximum number of rows that can be returned to the server in each call to the underlying RDBMS.
	 */
	public function getBatchSize()
	{
		return $this->batchSize;
	}

	/**
	 * Get the number of results from a query.
	 * 
	 * @access public
	 * @return int the query's number of results.
	 */
	public function getResultCount()
	{
		// For each SELECT, SHOW, DESCRIBE, EXPLAIN, etc query
		// 'SELECT FOUND_ROWS()' is the best way to get the total number of rows that should have been returned.
		// $read = array('SELECT', 'SHOW', 'DESCRIBE', 'EXPLAIN');
		// if (in_array(preg_replace('/\W.*/', '', $this->getSQL()), $read))	{return mysql_query('SELECT FOUND_ROWS()');}
		// otherwise return mysql_num_rows().
		// else	{return mysql_num_rows();}
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'('.$this->nbResults.')');
		return $this->nbResults;
	}

	/**
	 * Specifies the SQL statement assigned to a query.
	 * 
	 * @access public
	 * @return String the query's SQL statement.
	 */
	public function getSQL() 
	{
		return $this->strQuery;
	}
	
	/**
	 * Assigns a SQL statement to a query. 
	 *
	 * @access	public
	 * @param	JfSession	The session
	 * @param	String		the SQL statement
	 * @return	int			The new SQL statement
	 */
	private function prepareSQL($session, $sqlStatement)
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'('.$sqlStatement.')');
		// Change the prefix for all documentum queries
		$repository = $session->getDocbaseConfig();
		if ($repository['TYPE'] == 'documentum')
		{
			$sqlPrefix   = array("jm_", "jmi_", "jmc_", "jmr_", "acl_id");
			$dqlPrefix   = array("dm_", "dmi_", "dmc_", "dmr_", "acl_name");
			$sqlStatement = str_ireplace($sqlPrefix, $dqlPrefix, $sqlStatement);
		}
		// Return the query
		return $sqlStatement;
	}

	/**
	 * Assigns the page offset.
	 *
	 * @access public
	 * @param int batchSize the maximum number of rows that can be returned to the server in each call to the underlying RDBMS.
	 */
	public function setBatchOffset($offset) 
	{
		$this->batchOffset = $offset;
	}

	/**
	 * Assigns the maximum number of rows that can be returned to the server in each call to the underlying RDBMS.
	 *
	 * <strong>Usage Notes<strong> : each query that you make to a repository effectively queries the underlying RDBMS.
	 * If you know that the information returned from the RDBMS for each row is relatively small,
	 * use setBatchSize to set a higher maximum number of rows returned for each call to the RDBMS.
	 * This will reduce the number of calls to the RDBMS needed to complete a query, which provides better query performance.
	 *
	 * @access public
	 * @param int batchSize the maximum number of rows that can be returned to the server in each call to the underlying RDBMS.
	 */
	public function setBatchSize($batchSize) 
	{
		$this->batchSize = $batchSize;
	}

	/**
	 * Assigns a SQL statement to a query. 
	 *
	 * @access	public
	 * @param	String	sqlStatement	the SQL statement
	 */
	public function setSQL($sqlStatement)
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'('.$sqlStatement.')');
		// For each SELECT query limit the number of rows returned by the RDBMS for better performances,
		// replace SELECT with SELECT SQL_CALC_FOUND_ROWS
		// and end the query with 'LIMIT 0, 30' where 0 is the offset and 30 is the maximum rows returned.
		// SELECT FOUND_ROWS() will return the total number of rows that should have been returned by this query.
		// Note that this change must not be done if the word LIMIT is already present in the query
		// $read = array('SELECT');
		// $firstWord = preg_replace('/\W.*/', '', $sqlStatement);
		// if (in_array($firstWord, $read) && stripos($sqlStatement, 'LIMIT') === false)
		// {
			// $sqlStatement = $firstWord.' SQL_CALC_FOUND_ROWS '.substr($sqlStatement, strlen($firstWord));
			// $sqlStatement .= ' LIMIT '.$this->getBatchOffset().', '.$this->getBatchSize();
		// }
		// Set the query
		$this->strQuery = $sqlStatement;
	}
}
?>