<?php
require_once 'functions.php';
sendXKCDUpdatesToSubscribers();
file_put_contents(__DIR__ . '/cron_log.txt', date('Y-m-d H:i:s') . " - Cron ran\n", FILE_APPEND);


// This script should send XKCD updates to all registered emails.
// You need to implement this functionality.

// php src/cron.php  ....to run task
