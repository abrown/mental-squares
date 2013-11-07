<?php
require('../pocket-knife/start.php');
set_include_path('./classes');

// if no tokens are added to the URL, the user probably wants to view the library
try{ WebUrl::getTokens(); }
catch(Error $e){ WebHttp::redirect(WebUrl::create('/index.php')); }

// start service
$configuration = new Settings();
$configuration->load('config.json'); // load settings from a JSON file
$service = new Service($configuration);
$service->execute();