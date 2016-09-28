<?php

require_once __DIR__.'/vendor/autoload.php';

$config = \Symfony\Component\Yaml\Yaml::parse(file_get_contents(__DIR__.'/config.yml'));

$loader = new Twig_Loader_Filesystem(__DIR__.'/html');
$twig = new Twig_Environment($loader);

if (!isset($_POST['token'])) {
    echo $twig->render("intro.html.twig", ['appId' => $config['parameters']['id']]);
    exit;
}

$token = $_POST['token'];

$face = new Facebook\Facebook([
    'app_id' => $config['parameters']['id'],
    'app_secret' => $config['parameters']['secret'],
    'default_graph_version' => 'v2.7'
]);

$timeline = new \App\Timeline($face);
try {
    $results = $timeline->generate($token);
} catch (\Facebook\Exceptions\FacebookAuthenticationException $e) {
    echo "Problem with token.\n";
    exit;
}

echo $twig->render("content.html.twig", ['results' => $results]);
