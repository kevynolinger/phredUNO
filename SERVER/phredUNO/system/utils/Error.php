<?php

  /**
   * @author: Kevin Olinger, 2016-06-18
   * @copyright: 2016+ Kevin Olinger
   *
   * Last modified: 2016-06-25
   */

   namespace phredUNO\system\utils;

   class Error {

     protected static $errors = array(
       0 => "An unknown error occured",
       1 => "Unable to establish a MySQL connection thus the phredUNO server can not be started",
       2 => "Tried to send message to non-existant client",
       3 => "The game which you tried to join already started",
       4 => "The game which you tried to join is full",
       5 => "The game which you tried to join does not exists (anymore)",
       6 => "Execution cancelled. Not all requirements are met",
       7 => "The connection got dropped because of an unknown error",
       8 => "Given data not in JSON format",
       9 => "No action given/defined",
       10 => "You cannot execute the given action without being authenticated",
       11 => "Tried to execute invalid action",
       12 => "You are not allowed to authenticate yourself. Authentication alredy done for this client",
       13 => "Given token does not exist or is invalid",
       14 => "You cannot execute this action with the player's current status.",
       15 => "The game you searched for is not available (anymore)",
       16 => "The password you entered is not correct",
       17 => "Unable to leave a game, because the given game does not exist (anymore)",
       18 => "The name you have chosen for the game is already taken. Please choose another one.",
       19 => "You do not own the card you tried to play",
       20 => "You cannot lay this card down because it is not your turn",
       21 => "Tried to play a turn in a non-existing game",
       22 => "Tried to play a turn in a not-yet started game",
       23 => "You can not take a turn in a game you are not a part of",
       24 => "Can not play the selected card",
       25 => "You forgot to UNO",
       26 => "It is not your turn"
     );

     public function exists($errCode): bool {
       if(array_key_exists($errCode, self::$errors)) return true;
       else return false;
     }

     public function get($errCode): string {
       if($this->exists($errCode)) return self::$errors[$errCode];
       else return self::$errors[0];
     }

   }
