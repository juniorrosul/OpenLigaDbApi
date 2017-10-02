<?php

require 'vendor/autoload.php';

$client = new \juniorrosul\OpenLigaDbApi\Client();

$matches = $client->getMatchesByLeagueSeason('bl1', 2015);

foreach($matches as $match) {
    var_dump($match->getTimeZone());
}
