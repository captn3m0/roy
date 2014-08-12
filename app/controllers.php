<?php
class Controller {
  function render($template, $data = []){
    global $twig;
    return $twig->render($template, $data);
  }
}
class TeamController extends Controller{
  function get(){
    echo $this->render('team.twig');
  }
}