<?php
global $config;
use Symfony\Component\Yaml\Yaml;
if(file_exists('.env')){
  $config = Yaml::parse('.env');
}
else{
  $config = $_ENV;
}