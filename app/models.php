<?php
use Parse\ParseClient; 
use Parse\ParseObject;
use Parse\ParseQuery;
ParseClient::initialize($config['PARSE_APP_ID'], $config['PARSE_API_KEY'], $config['PARSE_MASTER_KEY']);

class Item{
  static function create($data){
    $parse_obj = ParseObject::create("Item");
    foreach($data as $key=>$obj){
      $parse_obj->$key = $obj;
    }
    $parse_obj->save();
    return $parse_obj->objectId;
  }
  static function get($team){
    $query = new ParseQuery("Item");
    $query->equalTo("team_domain", $team);
    return $query->find();
  }
}

class Team{
  static function create($name, $id){
    $parse_obj = ParseObject::create("Team");
    $parse_obj->name = $name;
    $parse_obj->team_id = $id;
    $parse_obj->save();
    return $parse_obj;
  }
  // Search for a team with its Slack ID (not name)
  static function get($id){
    $query = new ParseQuery("Team");
    $query->equalTo("team_id", $id);
    $result = $query->find();
    if(count($result) > 0)
      return $result[0];
    else
      return false;
  }
  static function find_or_create($name, $id){
    $team = self::get($id);
    if($team)
      return $team;
    else
      return self::create($name, $id);
  }
}

class User{
  static function create($nick, $id){
    $parse_obj = ParseObject::create("User");
    $parse_obj->nick = $nick;
    $parse_obj->user_id = $id;
    $parse_obj->save();
  }
  // Get a user by its unique slack id (not nick)
  static function get($id){
    $query = new ParseQuery("User");
    $query->equalTo("user_id", $id);
    $result = $query->find();
    if(count($result) > 0)
      return $result[0];
    else
      return false;
  }
  static function add($userlist){
    foreach($userlist as $user){
      $user_in_db = self::get($user->id);
      if(!$user_in_db)
        self::create($user->name, $user->id);
    }
  }
}

class Token{
  static function add($team_id, $token, $user_nick){
    $parse_obj = ParseObject::create("Token");
    $parse_obj->team_id = $team_id;
    $parse_obj->token = $token;
    $parse_obj->user  = $user_nick;
    $parse_obj->save();
  }
  // Will try to find a token for the team+user
  // Update the token if found, else creates a new one
  // The reason we store the user_nick and not the id is because the nick is shown to the user
  static function update_or_add($team_id, $token, $user_nick){
    $query = new ParseQuery("Token");
    $query->equalTo("team_id", $team_id);
    $query->equalTo("user", $user_nick);
    $result = $query->find();
    if(count($result)){
      $token_obj = $result[0];
      $token_obj->token = $token;
      $token_obj->save();
    }
    else{
      self::add($team_id, $token, $user_nick);
    }
  }
}