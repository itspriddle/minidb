<?php

/**
 * MiniDB
 *
 * @package   MiniDB
 * @author    Joshua Priddle <jpriddle@nevercraft.net>
 * @copyright Copyright (c) 2011, ViaTalk, LLC
 * @version   0.0.1
 * @link      https://github.com/itspriddle/minidb
 */

// ----------------------------------------------------------------------

/**
 * MiniDB is the main class for connecting to a database and executing
 * queries.
 */

class MiniDB {

  /**
   * The PDO connection object
   */

  public $connection;

  /**
   * Create a new DB connection. If a PDOException is raised, the $connection
   * variable is set to FALSE and $error will hold the exception
   *
   * $conf should be an array with keys: database, hostname, username, and
   * password
   *
   * @param array $conf - database config
   */

  public function __construct($hostname, $username, $password, $database, $driver = 'mysql') {
    try {
      $dsn = "{$driver}:dbname={$database};host={$hostname}";
      $this->connection = new PDO($dsn, $username, $password);
    } catch(PDOException $e) {
      $this->connection = FALSE;
      $this->error      = $e;
    }
  }

  // --------------------------------------------------------------------

  /**
   * Execute a DB query
   *
   * This method takes any number of arguments. It is assumed the first
   * argument is the SQL string to be used in the query. Any additional
   * arguments are bound variables to be replaced in the SQL string.
   *
   * Returns false if called with no arguments.
   *
   * @param  variable
   * @return object|false
   */

  public function query() {
    $argc = func_num_args();
    $argv = func_get_args();

    if ($argc > 1) {
      $sql   = array_shift($argv);
      $query = $this->connection->prepare($sql);
      $query->execute($argv);
      return new MiniDB_Result($query, $this->connection);
    } elseif ($argc == 1) {
      $query = $this->connection->query($argv[0]);
      return new MiniDB_Result($query, $this->connection);
    }
    return FALSE;
  }

  // --------------------------------------------------------------------

  /**
   * Quote a string
   *
   * @param  string $str - raw string to quote
   * @return string
   */

  public function quote($str) {
    return $this->connection->quote($str);
  }

  // --------------------------------------------------------------------

} // END CLASS MiniDB

// ----------------------------------------------------------------------

/**
 * MiniDB_Result is used to represents query results
 */

class MiniDB_Result {

  /**
   * The query object for this result, should be a PDOStatement
   */

  protected $query;

  /**
   * The results (rows) of this query
   */

  protected $result = FALSE;

  /**
   * The current row of results (used with $this->row())
   */

  protected $row_pos = 0;

  /**
   * Create a new result instance
   *
   * @param object $query - PDOStatement instance
   * @param object $connection - PDO instance
   */

  public function __construct($query, &$connection) {
    $this->connection = $connection;
    $this->query      = $query;
  }

  // --------------------------------------------------------------------

  /**
   * Returns the number of rows for $this->query
   *
   * @return integer
   */

  public function num_rows() {
    return count($this->result());
  }

  // --------------------------------------------------------------------

  /**
   * Return all results for this query
   *
   * @return array
   */

  public function result() {
    if ( ! $this->result) {
      $this->result = $this->query->fetchAll(PDO::FETCH_CLASS, 'stdClass');
    }
    return $this->result;
  }

  // --------------------------------------------------------------------

  /**
   * Returns the current row on query. Increments row position, making
   * it useful in loops; eg: while ($row = $query->row()). Returns false
   * if the current row doesn't exist in the query results.
   *
   * @param  row index to retrieve
   * @return object|false
   */

  public function row($i = FALSE) {
    $res = $this->result();
    $pos = $i === FALSE ? $this->row_pos : $i;

    if (isset($res[$pos])) {
      $out = $res[$pos];
      if ($i === FALSE) {
        $this->row_pos++;
      }
    } else {
      $out = FALSE;
    }

    return $out;
  }

  // --------------------------------------------------------------------

  /**
   * Return the number of rows affected by this query
   *
   * @return integer
   */

  public function affected_rows() {
    return $this->query->rowCount();
  }

  // --------------------------------------------------------------------

  /**
   * Return the insert ID from the last query
   *
   * @return integer
   */

  public function insert_id() {
    return $this->connection->lastInsertId();
  }

  // --------------------------------------------------------------------

} // END CLASS MiniDB_Result

/* End of file minidb.php */
/* Location: ./minidb/minidb.php */
