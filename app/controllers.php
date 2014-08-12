<?php
use ConnorVG\Slack;
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
    if(substr($link,0,4) !== 'http'){
      $link = $this->config('BASE_URI').$link;
    }
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
    if($_SESSION['team'] == $team){
      $items = Item::get($team);
      $team = $team = Team::find($team);
      if($team){
        //$users = User::
        echo $this->render('team.twig', ['items'=>$items]);
      }
      else{
        throw new Exception("No such team");
      }
    }
    else{
      // We redirect to the oauth page
      // Get the team's slack id, if available
      $team = Team::find($team);
      if($team){
        $this->redirect("oauth?team={$team->team_id}");
      }
      else{
        $this->redirect("oauth");
      }
      
    }
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
      'scope'=>'identify,read',
      'team'=>$_GET['team']
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
      // Convert the code to an access token
      $slack = new ConnorVG\Slack\Slack($json->access_token);
      $response = $slack->prepare('auth.test')->send();

      // Store the access token and team
      $team_name = strtolower($response->team);
      Token::update_or_add($response->team_id, $json->access_token, $response->user);
      Team::find_or_create($team_name, $response->team_id);

      // Login the user
      $_SESSION['user'] = $response->user;
      $_SESSION['team'] = $team_name;

      $this->redirect($team_name);
    }
    else{
      throw new Exception("Error in oauth");
    }
  }
}

class SessionController extends Controller{
  function get(){
    echo $this->json($_SESSION);
  }
}

class UpdateUsersController extends Controller{
  // Expects team name
  function get($team){
    $items = Item::get($team);
    $team = $team = Team::find($team);
    if($team){
      $token = Team::get_token($team->team_id);
      $slack = new ConnorVG\Slack\Slack($token);
      $userlist = $slack->prepare('users.list')->send();
      User::add($userlist->members, $team->team_id);
      echo "User list updated";
    }
    else{
      throw new Exception("No such team");
    }
  }
}