<?php
// Enable error reporting for debugging (disable in production)
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Set CORS headers
header('Access-Control-Allow-Origin: *'); // Adjust to your domain in production
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($data['team_name']) || !isset($data['members']) || empty($data['members'])) {
  echo json_encode(['success' => false, 'error' => 'Invalid or missing team data']);
  exit;
}

$team_name = htmlspecialchars($data['team_name'], ENT_QUOTES, 'UTF-8');
$members = $data['members'];

// Build email message
$subject = 'ShubhAarambh Hackathon 2025 - Registration Confirmation';
$headers = "From: junctioniinnovation@gmail.comr\n" .
           "Reply-To: junctioniinnovation@gmail.com\r\n" .
           "X-Mailer: PHP/" . phpversion();

$message = "Dear Team $team_name,\n\n";
$message .= "Thank you for registering for the ShubhAarambh Hackathon 2025!\n\n";
$message .= "Team Details:\n";
foreach ($members as $index => $member) {
  $name = htmlspecialchars($member['name'], ENT_QUOTES, 'UTF-8');
  $phone = htmlspecialchars($member['phone'], ENT_QUOTES, 'UTF-8');
  $email = htmlspecialchars($member['email'], ENT_QUOTES, 'UTF-8');
  $message .= "Member " . ($index + 1) . ":\n";
  $message .= "Name: $name\nPhone: $phone\nEmail: $email\n\n";
}
$message .= "We look forward to your innovative solutions!\n\n";
$message .= "Best regards,\nShubhAarambh Hackathon Team";

$success = true;
foreach ($members as $member) {
  $to = htmlspecialchars($member['email'], ENT_QUOTES, 'UTF-8');
  if (!mail($to, $subject, $message, $headers)) {
    $success = false;
    break;
  }
}

echo json_encode([
  'success' => $success,
  'error' => $success ? null : 'Failed to send confirmation emails'
]);
?>
