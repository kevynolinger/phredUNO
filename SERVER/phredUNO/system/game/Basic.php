<?php

  /**
   * @author: Kevin Olinger, 2016-06-18
   * @copyright: 2016+ Kevin Olinger
   *
   * Last modified: 2016-06-20
   */

  namespace phredUNO\system\game;

  class Basic {

    protected $games = array();

    public function exists($gameID): bool {
      if(array_key_exists($gameID, $this->games)) return false;
      else return true;
    }

    public function create($gameID, $name, $displayName, $password, $slots, $cards): bool {
      if($this->exists($gameID)) return false;
      else {
        $this->games[$gameID] = array();
        $this->games[$gameID]["status"] = 1;

        $this->games[$gameID]["name"] = $name;
        $this->games[$gameID]["displayName"] = $displayName;
        $this->games[$gameID]["password"] = $password;

        $this->games[$gameID]["slots"] = $slots;
        $this->games[$gameID]["cardsamount"] = $cards;

        $this->games[$gameID]["players"] = array();
        $this->games[$gameID]["cards"] = array();
        $this->games[$gameID]["usedcards"] = array();

        $this->games[$gameID]["currentcard"] = "";
        $this->games[$gameID]["lastturn"] = "";
        $this->games[$gameID]["currentplayer"] = 1;

        return true;
      }
    }

    public function prepare($gameID): bool {
      if($this->exists($gameID)) {
        $this->games[$gameID]["cards"] = array(
          "red0_1" => "red0_1",
          "red1_1" => "red1_1",
          "red1_2" => "red1_2",
          "red2_1" => "red2_1",
          "red2_2" => "red2_2",
          "red3_1" => "red3_1",
          "red3_2" => "red3_2",
          "red4_1" => "red4_1",
          "red4_2" => "red4_2",
          "red5_1" => "red5_1",
          "red5_2" => "red5_2",
          "red6_1" => "red6_1",
          "red6_2" => "red6_2",
          "red7_1" => "red7_1",
          "red7_2" => "red7_2",
          "red8_1" => "red8_1",
          "red8_2" => "red8_2",
          "red9_1" => "red9_1",
          "red9_2" => "red9_2",
          "yellow0_1" => "yellow0_1",
          "yellow1_1" => "yellow1_1",
          "yellow1_2" => "yellow1_2",
          "yellow2_1" => "yellow2_1",
          "yellow2_2" => "yellow2_2",
          "yellow3_1" => "yellow3_1",
          "yellow3_2" => "yellow3_2",
          "yellow4_1" => "yellow4_1",
          "yellow4_2" => "yellow4_2",
          "yellow5_1" => "yellow5_1",
          "yellow5_2" => "yellow5_2",
          "yellow6_1" => "yellow6_1",
          "yellow6_2" => "yellow6_2",
          "yellow7_1" => "yellow7_1",
          "yellow7_2" => "yellow7_2",
          "yellow8_1" => "yellow8_1",
          "yellow8_2" => "yellow8_2",
          "yellow9_1" => "yellow9_1",
          "yellow9_2" => "yellow9_2",
          "green0_1" => "green0_1",
          "green1_1" => "green1_1",
          "green1_2" => "green1_2",
          "green2_1" => "green2_1",
          "green2_2" => "green2_2",
          "green3_1" => "green3_1",
          "green3_2" => "green3_2",
          "green4_1" => "green4_1",
          "green4_2" => "green4_2",
          "green5_1" => "green5_1",
          "green5_2" => "green5_2",
          "green6_1" => "green6_1",
          "green6_2" => "green6_2",
          "green7_1" => "green7_1",
          "green7_2" => "green7_2",
          "green8_1" => "green8_1",
          "green8_2" => "green8_2",
          "green9_1" => "green9_1",
          "green9_2" => "green9_2",
          "blue0_1" => "blue0_1",
          "blue1_1" => "blue1_1",
          "blue1_2" => "blue1_2",
          "blue2_1" => "blue2_1",
          "blue2_2" => "blue2_2",
          "blue3_1" => "blue3_1",
          "blue3_2" => "blue3_2",
          "blue4_1" => "blue4_1",
          "blue4_2" => "blue4_2",
          "blue5_1" => "blue5_1",
          "blue5_2" => "blue5_2",
          "blue6_1" => "blue6_1",
          "blue6_2" => "blue6_2",
          "blue7_1" => "blue7_1",
          "blue7_2" => "blue7_2",
          "blue8_1" => "blue8_1",
          "blue8_2" => "blue8_2",
          "blue9_1" => "blue9_1",
          "blue9_2" => "blue9_2"
        );
      } else return false;
    }

    public function delete($gameID): bool {
      if($this->exists($gameID)) {
        unset($this->games[$gameID]);

        return true;
      } else return false;
    }

    public function hasStarted($gameID): bool {
      if($this->exists($gameID)) {
        if($this->games[$gameID]["status"] == 2) return true;
        else return false;
      } else return false;
    }

    /* ADD methods */
    public function addPlayer($gameID, $ply): bool {
      if($this->exists($gameID)) {
        $this->games[$gameID]["players"][$ply] = $ply;

        return true;
      } else return false;
    }

    /* REMOVE methods */
    public function removePlayer($gameID, $ply): bool {
      if($this->exists($gameID)) {
        if(array_key_exists($ply, $this->games[$gameID]["players"])) {
          unset($this->games[$gameID]["players"][$ply]);

          return true;
        } else return false;
      } else return false;
    }

    public function removeCard($gameID, $card): bool {
      if($this->exists($gameID)) {
        if(isset($this->games[$gameID]["cards"][$card])) {
          $this->games[$gameID]["usedcards"][$card] = $card;

          unset($this->games[$gameID]["cards"][$card]);

          return true;
        } else return false;
      } else return false;
    }

    /* SET methods */
    public function setStatus($gameID, $status): bool {
      if($this->exists($gameID)) {
        $this->games[$gameID]["status"] = $status;

        return true;
      } else return false;
    }

    public function setCurrentCard($gameID, $card): bool {
      if($this->exists($gameID)) {
        $this->games["gameID"]["currentcard"] = $card;
        $this->removeCard($gameID, $card);

        return true;
      } else return false;
    }

    public function setCurrentPlayer($gameID): bool {
      if($this->exists($gameID)) {
        if($this->getCurrentPlayer($gameID) == $this->games[$gameID]["slots"]) $this->games[$gameID]["currentplayer"] = 1;
        else $this->games[$gameID]["currentplayer"] = ($this->games[$gameID]["currentplayer"] + 1);

        return true;
      } else return false;
    }

    public function setLastTurner($gameID, $token): bool {
      if($this->exists($gameID)) {
        $this->games[$gameID]["lastturn"] = $token;

        return true;
      } else return false;
    }

    /* GET methods */
    public function getName($gameID): string {
      if($this->exists($gameID)) return $this->games[$gameID]["displayName"];
      else return null;
    }

    public function getPassword($gameID): string {
      if($this->exists($gameID)) return $this->games[$gameID]["password"];
      else return null;
    }

    public function getPlayers($gameID): array {
      if($this->exists($gameID)) return $this->games[$gameID]["players"];
      else return null;
    }

    public function getStatus($gameID): int {
      if($this->exists($gameID)) return $this->games[$gameID]["status"];
      else return 0;
    }

    public function getSlots($gameID): int {
      if($this->exists($gameID)) return $this->games[$gameID]["slots"];
      else return 0;
    }

    public function getUsedSlots($gameID): int {
      if($this->exists($gameID)) return sizeof($this->games[$gameID]["players"]);
      else return 0;
    }

    public function getCards($gameID): array {
      if($this->exists($gameID)) return $this->games[$gameID]["cards"];
      else return null;
    }

    public function getCardAmount($game): int {
      if($this->exists($gameID)) return $this->games[$gameID]["cardsamount"];
      else return 0;
    }

    public function getCurrentCard($gameID): string {
      if($this->exists($gameID)) {
        $card = $this->games["gameID"]["currentcard"];

        if($card != "") return $card;
        else return null;
      } else return null;
    }

    public function getCurrentPlayer($gameID): int {
      if($this->exists($gameID)) return $this->games["gameID"]["currentplayer"];
      else return 0;
    }

    public function getCurrentPlayerUsername($gameID): string {
      if($this->exists($gameID)) {
        if($this->hasStarted($gameID)) {
          $i = 1;

          foreach($this->games[$gameID]["players"] as $ply) {
            if($i == $this->games[$gameID]["currentplayer"]) return Core::getUser()->getUsername($ply);

            $i++;
          }
        } else return null;
      } else return null;
    }

    public function getLastTurner($gameID): string {
      if($this->exists($gameID)) return $this->games[$gameID]["lastturn"];
      else return null;
    }

  }
