<?php

  /**
   * @author: Kevin Olinger, 2016-06-16
   * @copyright: 2016+ Kevin Olinger
   *
   * Last modified: 2016-06-18
   */

   namespace phredUNO\system;
   use phredUNO\system\utils\Log;
   use phredUNO\system\utils\Error;

   class Utils {

     protected static $logObj = null;
     protected static $errorObj = null;

     public function __construct() {
       $this->initLog();
       $this->initError();
     }

     protected function initLog() { self::$logObj = new Log(); }
     protected function initError() { self::$errorObj = new Error(); }

     public static final function log(): Log { return self::$logObj; }
     public static final function error(): Error { return self::$errorObj; }

     public function isJson($jsonString): bool {
       if(!preg_match('/[^,:{}\\[\\]0-9.\\-+Eaeflnr-u \\n\\r\\t]/', preg_replace('/"(\\.|[^"\\\\])*"/', '', $jsonString))) {
         json_decode($jsonString);

         return (json_last_error() == JSON_ERROR_NONE);
       } else return false;
     }

   }
