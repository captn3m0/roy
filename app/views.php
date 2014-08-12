<?php
global $twig;
$loader = new Twig_Loader_Filesystem('app/views');
$twig = new Twig_Environment($loader);
$filter = new Twig_SimpleFilter('remove_first_word', function ($string) {
    $arr = explode(' ',$string);
    array_splice($arr, 0, 1);
    return join(' ', $arr);
});
$twig->addFilter($filter);