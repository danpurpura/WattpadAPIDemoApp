<?php

require_once('lib/autoload.php');

use Wattpad\WattpadAPI;
use Wattpad\WattpadStoryStats;

$api = new WattpadAPI();

$action = (isset($_GET) ? key($_GET) : null);

if ($action == 'categories') {
    // get the categories
    $categories = $api->getCategories();
    // sort them
    asort($categories);
    // rebuild the array, we'll lose the order othwerise
    $sorted_categories = array();
    foreach($categories as $key => $value) {
        $sorted_categories[] = array('id' => $key, 'descr' => $value);
    }
    // return them
    echo json_encode($sorted_categories);
    die();
} else if ($action == 'stories' && isset($_GET['id'])) {
    // grab the first 50 hot stories in the category
    $stories = $api->getStoriesInCategory($_GET['id'], 'hot', 50);
    // load our stats engine
    $stats = new WattpadStoryStats($stories);
    // process the results
    $stats->process();
    // return the results
    echo json_encode(array('id' => $_GET['id'], 'stats' => $stats->getResults()));
    die();
}

