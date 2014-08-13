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
      // HTTP 1.1 RFC statas that Location headers must always be absolute links
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
      $team = $team = Team::find($team);
      if($team){
        $users = Team::get_users($team->team_id);
        $channels = Team::get_channels($team->team_id);
        $items = Team::get_items($team->team_id);

        foreach($items as &$item){
          $item->text = preg_replace_callback("/(<@U\w*>)/i", function($matches) use ($users){
            // We start from $matches[1]
            for($i=1;$i<count($matches);$i++){
              // Replace all user ids with user names
              $user_id = substr($matches[$i],2,-1);
              if(isset($users[$user_id]))
                return "@".$users[$user_id];
              else
                return "@".$user_id;
            }
          }, $item->text);

          $item->text = preg_replace_callback("/(<#C\w*>)/i", function($matches) use ($channels){
            for($i=1;$i<count($matches);$i++){
              // Replace all channel id with channel names
              $channel_id = substr($matches[$i],2,-1);
              if(isset($channels[$channel_id]))
                return "#".$channels[$channel_id];
              else
                return "#".$channel_id;
            }
          }, $item->text);

          $item->text = str_replace("<!channel>", "@channel", $item->text);

        }
        echo $this->render('team.twig', ['items'=>$items]);
      }
      else{
        throw new Exception("No such team: $team");
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
    $messages = [
      "aye aye, captain!",
      "your wish, my command",
      "Arrow approves of this",
      "I have a plan",
      "I am groot"
    ];
    $packet = new ConnorVG\Slack\SlackIncoming($_POST);
    if($packet->hasError()){
      echo $this->slack("Invalid incoming webhook");
    }
    else{
      Item::create($packet);
      echo $this->slack($messages[array_rand($messages)]);
    }
  }
}

class OAuthController extends Controller{
  function get(){
    $qs = http_build_query([
      'client_id' => $this->config('SLACK_ID'),
      'redirect_uri'=>$this->config('BASE_URI').'oauth/callback',
      'scope'=>'identify,read',
      'team'=>$_GET['team']
    ]);
    $this->redirect("https://slack.com/oauth/authorize?$qs");
  }
}

class CallbackController extends Controller{
  function get(){
    $qs = http_build_query([
      'client_id'=>     $this->config('SLACK_ID'),
      'client_secret'=> $this->config('SLACK_SECRET'),
      'code'=>          $_GET['code'],
      'redirect_uri'=>  $this->config('BASE_URI').'oauth/callback'
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
      throw new Exception("Error in oauth: {$json->ok}");
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
    $team = $team = Team::find($team);
    if($team){
      $token = Team::get_token($team->team_id);
      $slack = new ConnorVG\Slack\Slack($token);
      $userlist = $slack->prepare('users.list')->send();
      User::add($userlist->members, $team->team_id);
      echo "User list updated";
    }
    else{
      throw new Exception("No such team: $team");
    }
  }
}

class UpdateChannelsController extends Controller{
  function get($team){
    $team = $team = Team::find($team);
    if($team){
      $token = Team::get_token($team->team_id);
      $slack = new ConnorVG\Slack\Slack($token);
      $list = $slack->prepare('channels.list')->set(['exclude_archived'=>1])->send();
      Channel::add($list->channels, $team->team_id);
      echo "Channel list updated";
    }
    else{
      throw new Exception("No such team $team");
    }
  }
}

class ItemDoneController extends Controller{
  function post($item_id){
    Item::done($item_id);
    echo "Item was marked as done";
  }
}