# MiniDB

MiniDB is a small wrapper for the PHP PDO database class. It loosely resembles
the [CodeIgniter Database Library](http://codeigniter.com/user_guide/database/index.htm)
because I like it's API. MiniDB doesn't encapsulate all of the functionality
of PDO, but rather a small subset of it I've used with my own projects. This
includes connecting to a database, running queries, and iterating/counting
results. If you need more than this, MiniDB probably isn't for you.

## Installation

Simple copy `minidb.php` to your project and include.  Eg:

    cp minidb/minidb.php myapp/includes

## Connecting

Connect to a database by instantiating a new `MiniDB` object.

    $db = new MiniDB(array(
      'hostname' => 'localhost',
      'database' => 'testdb',
      'username' => 'root',
      'password' => 'root'
    ));

Check for connection errors:

    if (isset($db->error)) {
      die("Error connecting to database!");
    }

## Running Database Queries

Query the database using the `query()` method.

    $query = $db->query("SELECT * FROM users");

If you need to escape variables for use in a query, add them as parameters to
the `query()` method:

    $sql = "SELECT * FROM users WHERE first_name = ? AND last_name = ? AND email = ?";
    $query = $db->query($sql, 'Joshua', 'Priddle', 'jpriddle@nevercraft.net');

If you need to manually quote a variable for some reason, use the `quote()`
method:

    $quoted = $db->quote("I'm a baaaa'aaaad boy!");

## Working with Query Results

The `query()` method returns a `MiniDB_Result` object, which contains methods
commonly used with database record sets.

Get the number of rows returned in a query:

    $query->num_rows();

Get the number of rows affected by a query:

    $query->affected_rows();

Get an array of all records (each record will be an object):

    $query->result();

Get a single row:

    $query->row();

Get the 2nd row:

    $query->row(1);

Iterate through records:

    while ($row = $query->row()) {
      // do something with $row
    }

Foreach style:

    forach ($query->results() as $row) {
      // do something with $row
    }

## Gotchas

If you're running a query in a loop, you need to `unset` the query variable
at the end of the loop. Failure to do so will generate a PDO exception.

    while (something) {
      $query = $db->query("SOME QUERY");
      //... do something with query

      // Unset $query at the end of the loop
      unset($query);
    }

## License

Copyright 2011 Joshua Priddle <jpriddle@nevercraft.net> under the MIT
License, see `MIT-LICENSE` in this repo.
