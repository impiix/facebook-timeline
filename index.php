<?php

$token = "";

require_once __DIR__.'/vendor/autoload.php';

$config = \Symfony\Component\Yaml\Yaml::parse(file_get_contents(__DIR__.'/config.yml'));

$face = new Facebook\Facebook([
    'app_id' => $config['parameters']['id'],
    'app_secret' => $config['parameters']['secret'],
    'default_graph_version' => 'v2.7'
]);

$timeline = new \App\Timeline($face);
$results = $timeline->generate($token);

$loader = new Twig_Loader_Filesystem(__DIR__.'/html');
$twig = new Twig_Environment($loader);
echo $twig->render("content.html.twig", ['results' => $results]);
