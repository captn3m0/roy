<?php
class Controller {
  function render($template, $data = []){
    global $twig;
    return $twig->render($template, $data);
  }
  function json($data){
    header("Content-Type: application/json");
    return json_encode($data);
  }
  function slack($message){
    $json = new StdClass;
    $json->text = $message;
    return $this->json($json);
  }
}
class TeamController extends Controller{
  function get(){
    echo $this->render('team.twig');
  }
}

class ItemController extends Controller{
  function post(){
    $item = Item::create($_POST);
    echo $this->slack("Noted down in Roy");
  }
}