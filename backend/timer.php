<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Set timezone
date_default_timezone_set('UTC');

// Set the countdown end time (24 hours from now, but you can set a fixed date)
// Option 1: Rolling 24-hour countdown (resets daily)
$endTime = strtotime('tomorrow 00:00:00 UTC');

// Option 2: Fixed end date (uncomment and modify as needed)
// $endTime = strtotime('2025-08-25 23:59:59 UTC');

$currentTime = time();
$timeLeft = $endTime - $currentTime;

// If countdown has expired, reset to 24 hours
if ($timeLeft <= 0) {
    $endTime = strtotime('+24 hours');
    $timeLeft = $endTime - $currentTime;
}

// Calculate hours, minutes, seconds
$hours = floor($timeLeft / 3600);
$minutes = floor(($timeLeft % 3600) / 60);
$seconds = $timeLeft % 60;

// Get current stats (you can make these dynamic or pull from database)
$downloadCount = 523 + floor((time() % 86400) / 100); // Simulated growth
$remainingCopies = 1000 - $downloadCount;

// Ensure minimum values for urgency
if ($remainingCopies < 50) {
    $remainingCopies = rand(50, 200);
}

$response = [
    'success' => true,
    'countdown' => [
        'hours' => max(0, $hours),
        'minutes' => max(0, $minutes),
        'seconds' => max(0, $seconds),
        'totalSeconds' => max(0, $timeLeft)
    ],
    'stats' => [
        'downloadCount' => min($downloadCount, 950), // Cap at 950
        'remainingCopies' => max($remainingCopies, 50), // Minimum 50
        'socialProof' => [
            'monthlyDownloads' => 12847,
            'rating' => '4.9'
        ]
    ],
    'timestamp' => $currentTime,
    'timezone' => 'UTC'
];

echo json_encode($response);
?>
