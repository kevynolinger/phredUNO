<?php

  /**
   * @author: Kevin Olinger, 2016-06-21
   * @copyright: 2016+ Kevin Olinger
   *
   * Last modified: 2016-06-28
   */

  namespace phredUNO\system;
  use phredUNO\Core;

  class User {

    protected $users = array();

    private function exists($token): bool {
      if(array_key_exists($token, $this->users)) return true;
      else return false;
    }

    private function create($accountID, $token, $username) {
      $this->users[$token] = array();

      $this->users[$token]["accountID"] = $accountID;
      $this->users[$token]["username"] = $username;

      $this->users[$token]["status"] = 1;
      $this->users[$token]["uno"] = false;

      $this->users[$token]["clients"] = array();
      $this->users[$token]["cards"] = array();
    }

    public function update($accountID, $token, $client, $username, $gravatarHash) {
      if(!$this->exists($token)) $this->create($accountID, $token, $username);
      else $this->users[$token]["username"] = $username;

      $this->users[$token]["clients"][$client] = $client;
      $this->users[$token]["gravatarHash"] = $gravatarHash;

      Core::getClient()->setToken($client, $token);
    }

    public function delete($token, $client): bool {
      if($this->exists($token)) {
        if(array_key_exists($client, $this->users[$token]["clients"])) {
          unset($this->users[$token]["clients"][$client]);

          return true;
        } else return false;
      } else return false;
    }

    public function sendCards($token): bool {
      if($this->exists($token)) {
        $this->order($token, array(
          "cards" => $this->users[$token]["cards"]
        ), "cards");

        return true;
      } else return false;
    }

    public function order($token, $data, $order) {
      foreach($this->users[$token]["clients"] as $client) Core::getClient()->order($client, $data, $order);
    }

    /* ADD */
    public function addCard($token, $card): bool {
      if($this->exists($token)) {
        $this->users[$token]["cards"][$card] = $card;

        return true;
      } else return false;
    }

    /* REMOVE */
    public function removeCard($token, $card): bool {
      if($this->exists($token)) {
        if($this->getCard($token, $card)) {
          unset($this->users[$token]["cards"][$card]);

          return true;
        } else return false;
      } else return false;
    }

    public function removeCards($token): bool {
      if($this->exists($token)) {
        $this->users[$token]["cards"];

        return true;
      } return false;
    }

    public function removeUno($token): bool {
      if($this->exists($token)) {
        $this->users[$token]["uno"] = false;

        return true;
      } else return false;
    }

    /* SET */
    public function setStatus($token, $status): bool {
      if($this->exists($token)) {
        $this->users[$token]["status"] = $status;

        return true;
      } else return false;
    }

    public function setGame($token, $gameID): bool {
      if($this->exists($token)) {
        $this->users[$token]["game"] = $gameID;

        if($gameID != 0) {
          $this->setStatus($token, 2);
          $this->order($token, array(
            "message" => "You joined this game.",
            "gameID" => $gameID
          ), "joingame");
        } else {
          $this->setStatus($token, 1);
          $this->order($token, "You left a game.", "leftgame");
        }

        return true;
      } else return false;
    }

    public function setUno($token): bool {
      if($this->exists($token)) {
        $this->users[$token]["uno"] = true;

        return true;
      } else return false;
    }

    /* GET */
    public function getAccountID($token): int {
      if($this->exists($token)) return $this->users[$token]["accountID"];
      else return 0;
    }

    public function getUsername($token): string {
      if($this->exists($token)) return $this->users[$token]["username"];
      else return "Unknown";
    }

    public function getGravatarHash($token): string {
      if($this->exists($token)) return $this->users[$token]["gravatarHash"];
      else return "null";
    }

    public function getStatus($token): int {
      if($this->exists($token)) return $this->users[$token]["status"];
      else return 0;
    }

    public function getGame($token): int {
      if($this->exists($token)) return $this->users[$token]["game"];
      else return 0;
    }

    public function getCard($token, $card): bool {
      if($this->exists($token)) {
        if(array_key_exists($card, $this->users[$token]["cards"])) return true;
        else return false;
      } else return false;
    }

    public function getCardAmount($token): int {
      if($this->exists($token)) return sizeof($this->users[$token]["cards"]);
      else return 0;
    }

    public function getUno($token): bool {
      if($this->exists($token)) return $this->users[$token]["uno"];
      else return false;
    }

  }
