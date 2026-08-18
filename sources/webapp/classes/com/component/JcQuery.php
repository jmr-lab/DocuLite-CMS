<?php
/**
 * The Query class.
 *
 * @package		com.component
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JcQuery
{
	/**
	 * SQL Statement
	 *
	 * @access	private
	 * @var		String
	 */
	private $m_strStatement;

	/**
	 * Limit clauses
	 *
	 * @access	private
	 * @var		array
	 */
	private $m_arrLimitClauses = array('offset' => 0, 'row_count' => 30);

	/**
	 * List of order by clauses
	 *
	 * @access	private
	 * @var		array
	 */
	private $m_arrOrderByAttr = array();

	/**
	 * Constructor
	 *
	 * This function initialize the current query object.
	 *
	 * @access	public
	 */
	public function __construct($strQuery = '')
	{
		if (trim($strQuery) == '')	{return true;}
		$this->setStatement($strQuery);
	}

	/**
	 * Returns the statement.
	 *
	 * @access	public
	 * @return	String	the statement including the order by and limit clauses.
	 */
	public function getStatement()
	{
		$strQuery = $this->m_strStatement;
		// Look for the word 'ORDER BY' in the query string, add it if not found
		if (strpos($strQuery, 'ORDER BY') === false && sizeof($this->m_arrOrderByAttr) > 0)
		{
			$strQuery .= ' ORDER BY ';
			foreach ($this->m_arrOrderByAttr as $key=>$value)	{$strQuery .= $key.' '.$value.', ';}
			$strQuery = substr($strQuery, 0, -2);
		}
		// Look for the word 'LIMIT' in the query string, add it if not found
		if (strpos($strQuery, 'LIMIT') === false)	{$strQuery .= ' LIMIT '.$this->m_arrLimitClauses['offset'].', '.$this->m_arrLimitClauses['row_count'];}
		return $strQuery;
	}

	/**
	 * Set the limit clauses.
	 *
	 * @access	public
	 * @param	array	limit	the limit clauses.
	 */
	public function setLimitClauses($limit)
	{
		if (!isset($limit['offset']) || !isset($limit['row_count']))	{return false;}
		$this->m_arrLimitClauses = array('offset' => $limit['offset'], 'row_count' => $limit['row_count']);
	}

	/**
	 * Set the order by clauses.
	 *
	 * @access	public
	 * @param	array	order	the order by clauses.
	 */
	public function setOrderByClauses($order)
	{
		foreach ($order as $key=>$value)
		{
			if (!(strpos($this->m_strStatement, $key) === false) || substr($key, 0, 4) == 'CASE')	{$this->m_arrOrderByAttr[$key] = $value;}
		}
	}

	/**
	 * Set the query statement.
	 *
	 * @access	public
	 * @param	String	strQuery	the query statement.
	 */
	public function setStatement($strQuery)
	{
		$this->m_strStatement = $strQuery;
	}
}
?>