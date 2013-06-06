<?php

require_once('lib/autoload.php');

try {
    echo Main::route($_GET);
} catch (Exception $e) {
    echo json_encode(array('error' => $e->getMessage()));
}
