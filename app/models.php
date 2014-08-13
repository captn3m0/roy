<?php
use Parse\ParseClient; 
use Parse\ParseObject;
use Parse\ParseQuery;
ParseClient::initialize($config['PARSE_APP_ID'], $config['PARSE_API_KEY'], $config['PARSE_MASTER_KEY']);

class Item{
  static function create($data){
    $parse_obj = ParseObject::create("Item");
    $parse_obj->token = $data->token;
    $parse_obj->team_id = $data->team_id;
    $parse_obj->channel_id = $data->channel_id;
    $parse_obj->channel_name = $data->channel_name;
    $parse_obj->timestamp = $data->timestamp;
    $parse_obj->user_id = $data->user_id;
    $parse_obj->user_name = $data->user_name;
    $parse_obj->text = $data->text;
    $parse_obj->trigger_word = $data->trigger_word;
    $parse_obj->done = false;
    $parse_obj->save();
    return $parse_obj->getObjectId();
  }
  static function get($id){
    $query = new ParseQuery("Item");
    return $query->get($id);
  }

  static function done($id){
    $item = self::get($id);
    $item->done = true;
    $item->save();
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
    return $query->first();
  }
  static function find($name){
    $query = new ParseQuery("Team");
    $query->equalTo("name", $name);
    return $query->first();
  }
  static function find_or_create($name, $id){
    $team = self::get($id);
    if($team)
      return $team;
    else
      return self::create($name, $id);
  }
  static function get_token($team_id){
    $query = new ParseQuery("Token");
    $query->equalTo("team_id", $team_id);
    return $query->first()->token;
  }

  static function get_users($team_id){
    $query = new ParseQuery("User");
    $query->equalTo("team", $team_id);
    $result = $query->find();
    $arr = [];
    foreach($result as $user)
      $arr[$user->user_id] = $user->nick;
    return $arr;
  }
  static function get_channels($team_id){
    $query = new ParseQuery("Channel");
    $query->equalTo("team", $team_id);
    $result = $query->find();
    $arr = [];
    foreach($result as $channel)
      $arr[$channel->channel_id] = $channel->name;
    return $arr;
  }
  static function get_items($team){
    $query = new ParseQuery("Item");
    $query->equalTo("team_id", $team);
    $query->notEqualTo("done", true);
    return $query->find();
  }
}

class User{
  static function create($nick, $id, $team_id){
    $parse_obj = ParseObject::create("User");
    $parse_obj->nick = $nick;
    $parse_obj->user_id = $id;
    $parse_obj->team = $team_id;
    $parse_obj->save();
  }
  // Get a user by its unique slack id (not nick)
  static function get($id){
    $query = new ParseQuery("User");
    $query->equalTo("user_id", $id);
    return $query->first();
  }

  static function add($userlist, $team_id){
    foreach($userlist as $user){
      $user_in_db = self::get($user->id);
      if(!$user_in_db)
        self::create($user->name, $user->id, $team_id);
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
    $token_obj = $query->first();
    if($token_obj){
      $token_obj->token = $token;
      $token_obj->save();
    }
    else{
      self::add($team_id, $token, $user_nick);
    }
  }
}

class Channel{
  static function add($list, $team_id){
    foreach($list as $channel){
      $channel_in_db = self::get($channel->id);
      if(!$channel_in_db)
        self::create($channel->name, $channel->id, $team_id);
    }
  }
  static function get($id){
    $query = new ParseQuery("Channel");
    $query->equalTo("channel_id", $id);
    return $query->first();
  }
  static function create($name, $id, $team){
    $parse_obj = ParseObject::create("Channel");
    $parse_obj->name = $name;
    $parse_obj->channel_id = $id;
    $parse_obj->team = $team;
    $parse_obj->save();
  }
}