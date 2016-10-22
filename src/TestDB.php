<?php
/**
 * Created by PhpStorm.
 * User: tveitan
 * Date: 2016-10-22
 * Time: 12:53
 */

namespace CreativeLoop;

use \PDO;

class TestDB
{

    private $dbConn;
    /**
     * TestDB constructor.
     */
    public function __construct($dbConn)
    {
        $this->dbConn = $dbConn;
    }

    public function testGet() {
        $stmt = $this->dbConn->query('SHOW TABLES;');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}