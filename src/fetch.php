<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

// Fetch GitHub commits and store them in a text file
$username = 'Ganishaverma19';
$repo = 'GH-TIMELINE';
$apiUrl = "https://api.github.com/repos/$username/$repo/commits";

// Setup curl
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'GH-TIMELINE-App'); // Proper User-Agent

$response = curl_exec($ch);
curl_close($ch);

// Decode response
$commits = json_decode($response, true);

if (!$commits || !is_array($commits)) {
    echo "Failed to fetch commits or API limit reached.";
    exit;
}

// Format commit messages
$output = "";
foreach ($commits as $commit) {
    $message = $commit['commit']['message'];
    $author = $commit['commit']['author']['name'];
    $date = $commit['commit']['author']['date'];
    $output .= "[$date] $author: $message\n";
}

// Save to file
file_put_contents(__DIR__ . "/github_commits.txt", $output);

echo "Commits fetched and saved successfully.";
?>
