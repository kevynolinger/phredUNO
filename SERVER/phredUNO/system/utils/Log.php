<?php

  /**
   * @author: Kevin Olinger, 2016-06-16
   * @copyright: 2016+ Kevin Olinger
   *
   * Last modified: 2016-06-18
   */

   namespace phredUNO\system\utils;
   use phredUNO\Core;

   class Log {

     protected static $logContent = array();
     protected static $logNew = true;
     protected static $logTemp = "";
     protected static $logFile = "";
     protected static $lastDay = 0;

     //public static function info($message, $options) {}

     public static function info($message, $client = 0) {
       self::log(32, "INFO", $message, $client);
     }

     public static function warn($errCode, $client = 0): bool {
       if(Core::getUtils()->error()->exists($errCode)) {
         self::log(33, "WARN", Core::getUtils()->error()->get($errCode) ." (Code: ". $errCode .")", $client);

         return true;
       } else return false;
     }

     public static function customWarn($message, $client = 0) {
       self::log(33, "WARN", $message, $client);
     }

     public static function error($errCode, $client = 0) {
       if(Core::getUtils()->error()->exists($errCode)) {
         self::log(31, "ERROR", Core::getUtils()->error()->get($errCode) ." (Code: ". $errCode .")", $client);

         return true;
       } else return false;
     }

     public static function customError($message, $client = 0) {
       self::log(31, "ERROR", $message, $client);
     }

     public static function debug($message, $client = 0) {
       if(DEBUG) self::log(35, "DEBUG", $message, $client);
     }

     protected function log($color, $prefix, $message, $client) {
       echo "\033[36m". date("H:i:s", time()) ." \033[". $color ."m[". $prefix ."] \033[0m". debug_backtrace()[2]['class'] ." > ". debug_backtrace()[2]['function'] ."() > ". $message . ($client != 0 ? " (Client: ". $client .")" : "") ."\n";

       if(LOGFILE) {
         self::$logContent[] = date("Y-m-d H:i:s", time()) . " [". $prefix ."] ". debug_backtrace()[2]['class'] ." > ". debug_backtrace()[2]['function'] ."() > ". $message . ($client != 0 ? " (Client: ". $client .")" : "") ."\n";

         if(sizeof(self::$logContent) > 25 || self::$logNew) {
           if(self::$lastDay != date("d", time())) self::setupLogFile();

           foreach(self::$logContent as $value) self::$logTemp .= $value;

           $log = file_get_contents(self::$logFile) . self::$logTemp;
           file_put_contents(self::$logFile, $log);

           self::$logContent = array();
           self::$logTemp = "";
         }
       }
     }

     protected function setupLogFile() {
       $logDir = DIR ."/". LOGDIR ."/";
       $logList = glob($logDir . date("Y-m-d", time()) ."_*.log");

       self::$lastDay = date("d", time());
       self::$logFile = $logDir . date("Y-m-d", time()) ."_0.log";

       if(sizeof($logList) != 0) self::$logFile = $logDir . date("Y-m-d", time()) ."_". (str_replace(".log", "", substr(end($logList), -5)) + 1) .".log";
       if(!file_exists($logDir) && !is_dir($logDir)) mkdir($logDir, 0777);

       file_put_contents(self::$logFile, date("Y-m-d H:i:s", time()) . " [INFO] Log file '". self::$logFile ."' created. File ". (self::$logNew ? "starts a new" : "continues the previous") ." log.\n\n");

       self::$logNew = false;
     }

     public function finishLogFile() {
       foreach(self::$logContent as $value) self::$logTemp .= $value;

       self::$logTemp .= "\n". date("Y-m-d H:i:s", time()) . " [INFO] Log file '". self::$logFile ."' finished properly.";

       $log = file_get_contents(self::$logFile) . self::$logTemp;
       file_put_contents(self::$logFile, $log);

       self::$logContent = array();
       self::$logTemp = "";
     }

   }
