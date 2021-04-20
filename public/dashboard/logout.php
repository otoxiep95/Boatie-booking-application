<?php

// session_destroy();
require_once(__DIR__ . '/../../src/init.php');

$users = new Users();

$users->logout();

Redirect::page(__DIR__ . '/index.php');
