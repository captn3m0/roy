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
    $parse_obj->id = $id;
    $parse_obj->save();
  }
}

class User{
  static function create($nick, $id){
    $parse_obj = ParseObject::create("User");
    $parse_obj->nick = $nick;
    $parse_obj->id = $id;
    $parse_obj->save();
  }
}