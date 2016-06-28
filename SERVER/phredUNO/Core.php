<?php

  /**
   * @author: Kevin Olinger, 2016-06-16
   * @copyright: 2016+ Kevin Olinger
   *
   * Last modified: 2016-06-27
   */

   namespace phredUNO;
   use phredUNO\system\Utils;
   use phredUNO\system\utils\Log;
   use phredUNO\system\User;
   use phredUNO\system\Gameserver;
   use phredUNO\system\Game;
   use phredUNO\system\Client;
   use phredUNO\database\Database;

   use Ratchet\Server\IoServer;
   use Ratchet\Http\HttpServer;
   use Ratchet\WebSocket\WsServer;

   require dirname(__DIR__) ."/vendor/autoload.php";

   class Core {

     protected static $utilsObj = null;
     protected static $dbObj = null;
     protected static $servObj = null;
     protected static $userObj = null;
     protected static $gameObj = null;
     protected static $clientObj = null;

     public function __construct() {
       $this->defineGlobs();

       self::$utilsObj = new Utils();
       self::$userObj = new User();
       self::$gameObj = new Game();
       self::$clientObj = new Client();

       $this->initDB();
       $this->initServer();

       self::getGame()->management()->cleanup();
       self::getServ()->run();
       //self::getLog()->error(1);
     }

     protected function initDB() {
       self::$dbObj = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
     }

     protected function initServer() {
       self::$servObj = IoServer::factory(
         new HttpServer(
           new WsServer(
             new Gameserver()
           )
         ),
         PORT
       );

       self::getLog()->info("phredUNO server started on port ". PORT .".");
       //self::getLog()->debug(self::getGame()->management()->exist(1));
     }

     //Return class instances
     public static final function getUtils(): Utils { return self::$utilsObj; }
     public static final function getLog(): Log { return self::$utilsObj->log(); }
     public static final function getDB(): Database { return self::$dbObj; }
     public static final function getUser(): User { return self::$userObj; }
     public static final function getServ() { return self::$servObj; }
     public static final function getGame(): Game { return self::$gameObj; }
     public static final function getClient(): Client { return self::$clientObj; }

     protected function defineGlobs() {
       require_once(dirname(__DIR__) ."/config.inc.php");

       define("DB_HOST", $dbHost);
       define("DB_USER", $dbUser);
       define("DB_PASSWORD", $dbPass);
       define("DB_NAME", $dbName);
       define("DB_PREFIX", $dbPrefix);

       define("DEBUG", $debug);
       define("LOGFILE", $logFile);
       define("LOGDIR", $logDir);
       define("PORT", $socketPort);
       define("DIR", dirname(__DIR__));
     }

     public function shutdown() {
       self::getLog()->finishLogFile();
       self::getDB()->close();

       exit();
     }

   }
