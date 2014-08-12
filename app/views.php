<?php
global $twig;
$loader = new Twig_Loader_Filesystem('app/views');
$twig = new Twig_Environment($loader);
$filter = new Twig_SimpleFilter('remove_first_word', function ($string) {
    return substr(strstr($string," "), 1);
});
$twig->addFilter($filter);