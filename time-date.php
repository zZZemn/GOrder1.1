<?php 
// Set the time zone to Philippines (Asia/Manila)
date_default_timezone_set('Asia/Manila');

// Get the current date in Philippines time
$currentDate = date('Y-m-d');

// Get the current time in Philippines time
$currentTime = date('H:i:s');

$currentDateTime = (new DateTime())->format('Y-m-d H:i:s');

$sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));