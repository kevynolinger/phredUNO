<?php

  /**
   * @author: Kevin Olinger, 2016-06-16
   * @copyright: 2016+ Kevin Olinger
   *
   * Last modified: 2016-06-16
   */

  if(version_compare(PHP_VERSION, '7.0') < 0) exit("\033[36m". date("H:i:s", time()) ." \033[31m[ERROR] \033[0mphredUNO server requires PHP 7 (or higher) to run.\n");

  require_once("phredUNO/Core.php");
  new phredUNO\Core();
