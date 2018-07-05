<?php

use App\User;

require 'vendor/autoload.php';
require 'inc.functions.php';

session_start();

/** @var db_generic $db */
$db = db_sqlite::open(['database' => __DIR__ . '/db/forum.sqlite3']);

db_generic_model::$_db = $db;

$db->ensureSchema(require 'inc.db-schema.php');

/** @var User $g_user */
$g_user = null;
