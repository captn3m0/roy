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
  function redirect($link){
    header("Location: $link");
    exit;
  }
  function config($setting){
    global $config;
    return $config[$setting];
  }
}
class TeamController extends Controller{
  function get($team){
    $items = Item::get($team);
    echo $this->render('team.twig', ['items'=>$items]);
  }
}

class ItemController extends Controller{
  function post(){
    $item = Item::create($_POST);
    echo $this->slack("Noted down in Roy");
  }
}

class OAuthController extends Controller{
  function get(){
    global $config;
    $qs = http_build_query([
      'client_id' => $this->config('SLACK_ID'),
      'redirect_uri'=>$config['BASE_URI'].'oauth/callback',
      'scope'=>'identify,read'
    ]);
    $this->redirect("https://slack.com/oauth/authorize?$qs");
  }
}

class CallbackController extends Controller{
  function get(){
    global $config;
    $qs = http_build_query([
      'client_id'=>$this->config('SLACK_ID'),
      'client_secret'=>$this->config('SLACK_SECRET'),
      'code'=>$_GET['code']
    ]);
    $response = file_get_contents("https://slack.com/api/oauth.access?$qs");
    $json = json_decode($response);
    if($json->ok == 'true'){
      
    }
    else{
      throw new Exception("Error in oauth");
    }
  }
}