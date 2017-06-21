<?php

// Define app routes

$app->get('/{whatever}', function ($request, $response, $args) {
    return $response->write("Url " . $args['whatever'] . " has no function");
});