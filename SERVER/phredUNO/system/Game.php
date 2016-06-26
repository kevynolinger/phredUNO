<?php

  /**
   * @author: Kevin Olinger, 2016-06-18
   * @copyright: 2016+ Kevin Olinger
   *
   * Last modified: 2016-06-20
   */

  namespace phredUNO\system;
  use phredUNO\Core;
  use phredUNO\system\game\Basic;
  use phredUNO\system\game\Player;
  use phredUNO\system\game\Management;

  class Game {

    protected static $basic = null;
    protected static $player = null;
    protected static $management = null;

    public function __construct() {
      self::$basic = new Basic();
      self::$player = new Player();
      self::$management = new Management();
    }

    public static final function basic(): Basic { return self::$basic; }
    public static final function player(): Player { return self::$player; }
    public static final function management(): Management { return self::$management; }

  }
