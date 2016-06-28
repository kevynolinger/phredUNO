<?php

  /**
   * @author: Kevin Olinger, 2016-06-20
   * @copyright: 2016+ Kevin Olinger
   *
   * Last modified: 2016-06-28
   */

  namespace phredUNO\system\game;
  use phredUNO\Core;

  class Management {

    public function new($name, $password, $slots, $cards, $token): int {
      $displayName = $name;
      $name = iconv("UTF-8", "ISO-8859-1//TRANSLIT//IGNORE", str_replace("/\s+/", "_", strtolower($name)));
      $password = ($password != "" ? hash("sha256", $password) : "");

      $slots = ($slots < 2 ? 2 : $slots);
      $cards = ($cards < 3 ? 3 : $cards);
      $cards = ($cards > 8 ? 8 : $cards);

      Core::getDB()->query("SELECT gameID FROM ". DB_PREFIX ."game WHERE name = :name AND ended = :date LIMIT 1");
      Core::getDB()->bind(":name", $name);
      Core::getDB()->bind(":date", "0000-00-00 00:00:00");
      $result = Core::getDB()->single();

      if(Core::getDB()->rowCount() == 1) return 0;

      Core::getDB()->query("INSERT INTO ". DB_PREFIX ."game (name, password, numPlayers, numCards, created) VALUES (:name, :password, :players, :cards, :date)");
      Core::getDB()->bind(":name", $name);
      Core::getDB()->bind(":password", $password);
      Core::getDB()->bind(":players", $slots);
      Core::getDB()->bind(":cards", $cards);
      Core::getDB()->bind(":date", date("Y-m-d-H-i-s"));
      Core::getDB()->execute();

      $gameID = Core::getDB()->lastInsertId();

      Core::getGame()->basic()->create($gameID, $name, $displayName, $password, $slots, $cards, $token);

      Core::getLog()->info("Game '". $displayName ."' (ID: ". $gameID . ($password != "" ? ", password protected" : "") .") successfully created");
      Core::getLog()->debug("Game '". $displayName ."' parameters: Slots -> ". $slots ." ; Cards -> ". $slots);

      return $gameID;
    }

    public function start($gameID) {
      Core::getGame()->basic()->setStatus($gameID, 2);
      Core::getGame()->basic()->prepare($gameID);

      foreach(Core::getGame()->basic()->getPlayers($gameID) as $ply) {
        Core::getUser()->removeCards($ply);

        for($i = 1; $i <= Core::getGame()->basic()->getCardAmount($gameID); $i++) $this->getRandomCard($gameID, $ply);

        Core::getUser()->order($ply, array(
          "gameID" => $gameID,
          "status" => 2
        ), "gamestatus");
        Core::getUser()->sendCards($ply);
        Core::getUser()->removeUno($ply);
      }

      Core::getDB()->query("UPDATE ". DB_PREFIX ."game SET started = :date WHERE gameID = :gameID");
      Core::getDB()->bind(":date", date("Y-m-d-H-i-s"));
      Core::getDB()->bind(":gameID", $gameID);
      Core::getDB()->execute();

      Core::getGame()->basic()->setCurrentCard($gameID, array_rand(Core::getGame()->basic()->getCards($gameID), 1));

      $this->sendCurrentCard($gameID);
      $this->sendCurrentPlayer($gameID);

      Core::getLog()->info("Game '". Core::getGame()->basic()->getName($gameID) ."' (ID: ". $gameID .") started");
    }

    public function stop($gameID, $token) {
      foreach(Core::getGame()->basic()->getPlayers($gameID) as $ply) {
        Core::getUser()->removeCards($ply);
        Core::getUser()->removeUno($ply);
        Core::getUser()->setGame($ply, 0);
        Core::getUser()->setStatus($token, 0);

        Core::getUser()->order($ply, array(
          "gameID" => $gameID,
          "winner" => Core::getUser()->getUsername($token)
        ), "gameend");

        Core::getDB()->query("INSERT INTO ". DB_PREFIX ."play VALUES (:gameID, :accountID, :score, :status)");
        Core::getDB()->bind(":gameID", $gameID);
        Core::getDB()->bind(":accountID", Core::getUser()->getAccountID($ply));
        Core::getDB()->bind(":score", ($token == $ply ? 1 : 0));
        Core::getDB()->bind(":status", 1);
        Core::getDB()->execute();
      }

      Core::getDB()->query("UPDATE ". DB_PREFIX ."game SET ended = :date WHERE gameID = :gameID");
      Core::getDB()->bind(":date", date("Y-m-d-H-i-s"));
      Core::getDB()->bind(":gameID", $gameID);
      Core::getDB()->execute();

      Core::getUtils()->log()->info("Game '". Core::getGame()->basic()->getName($gameID) ."' (ID: ". $gameID .") won by ". Core::getUser()->getUsername($token));
      Core::getUtils()->log()->info("Game '". Core::getGame()->basic()->getName($gameID) ."' (ID: ". $gameID .") stopped, closed and removed");

      unset($this->games[$gameID]);
    }

    public function cleanup() {
      $this->games = array();

      Core::getDB()->query("UPDATE ". DB_PREFIX ."game SET ended = :date WHERE ended = :dateEmpty");
      Core::getDB()->bind(":date", date("Y-m-d-H-i-s"));
      Core::getDB()->bind(":dateEmpty", "0000-00-00 00:00:00");
      Core::getDB()->execute();
    }

    /* SEND methods */
    public function sendCurrentCard($gameID): bool {
      if(Core::getGame()->basic()->hasStarted($gameID)) {
        $currentCard = Core::getGame()->basic()->getCurrentCard($gameID);

        foreach(Core::getGame()->basic()->getPlayers($gameID) as $ply) {
          Core::getUser()->order($ply, array(
            "message" => "This is the current card",
            "card" => $currentCard
          ), "currentcard");
        }

        return true;
      } else return false;
    }

    public function sendCurrentPlayer($gameID): bool {
      if(Core::getGame()->basic()->hasStarted($gameID)) {
        $currentPly = Core::getGame()->basic()->getCurrentPlayerUsername($gameID);

        foreach(Core::getGame()->basic()->getPlayers($gameID) as $ply) {
          Core::getUser()->order($ply, array(
            "message" => "This is the current player",
            "player" => $currentPly
          ), "currentplayer");
        }

        return true;
      } else return false;
    }

    /* GET */
    public function getRandomCard($gameID, $token, $notify = false) {
      if(Core::getGame()->basic()->hasStarted($gameID)) {
        $randomCard = array_rand(Core::getGame()->basic()->getCards($gameID), 1);

        Core::getGame()->basic()->removeCard($gameID, $randomCard);

        Core::getUser()->addCard($token, $randomCard);

        if($notify) {
          Core::getUser()->order($token, array(
            "message" => "You got a new card",
            "card" => $randomCard
          ), "newcard");
        }

        return true;
      } else return false;
    }

  }
