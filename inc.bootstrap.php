<?php

require 'vendor/autoload.php';
require 'inc.functions.php';

session_start();

$db = db_sqlite::open(['database' => __DIR__ . '/db/forum.sqlite3']);

db_generic_model::$_db = $db;

require 'inc.ensure-db-schema.php';
require 'inc.models.php';
