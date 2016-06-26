<?php

  /**
   * @author: Kevin Olinger, 2016-05-01
   * @copyright: 2016+ Kevin Olinger
   *
   * Last modified: 2016-06-18
   */

   namespace phredUNO\database;
   use phredUNO\Core;

   class Database {

     protected $dbHandler;
     protected $stmt;

     public function __construct($dbHost, $dbUser, $dbPass, $dbName) {
       $dataSource = "mysql:host=". $dbHost .";dbname=". $dbName;
       $pdoAttributes = array(
         \PDO::ATTR_PERSISTENT => true,
         \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
       );

       try { $this->dbHandler = new \PDO($dataSource, $dbUser, $dbPass, $pdoAttributes); }
       catch(\PDOException $ex) {
         Core::getLog()->error(1);
         Core::shutdown();
       }
     }

     public function query($query) {
       $this->stmt = $this->dbHandler->prepare($query);
     }

     public function bind($param, $value, $type = null) {
       if(is_null($type)) {
         switch(true) {

           case is_int($value):
             $type = \PDO::PARAM_INT;
             break;

           case is_bool($value):
             $type = \PDO::PARAM_BOOL;
             break;

           case is_null($value):
             $type = \PDO::PARAM_NULL;
             break;

           default:
             $type = \PDO::PARAM_STR;

        }
      }

      $this->stmt->bindValue($param, $value, $type);
    }

    public function execute() {
      return $this->stmt->execute();
    }

    public function resultset(): array {
      $this->execute();

      return $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function single() {
      $this->execute();

      return $this->stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function rowCount(): int {
      return $this->stmt->rowCount();
    }

    public function lastInsertId(): int {
      return $this->dbHandler->lastInsertId();
    }

    public function beginTransaction() {
      return $this->dbHandler->beginTransaction();
    }

    public function endTransaction() {
      return $this->dbHandler->commit();
    }

    public function cancelTransaction() {
      return $this->dbHandler->rollBack();
    }

    public function debugDumpParams() {
      return $this->stmt->debugDumpParams();
    }

    public function close() {
      unset($this->dbHandler);
    }

  }
