<?php
// send_email.php - Place this in the same directory as your HTML file. Ensure your server supports PHP and mail().

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['team_name']) || !isset($data['members'])) {
  echo json_encode(['success' => false, 'error' => 'Invalid data']);
  exit;
}

$team_name = $data['team_name'];
$members = $data['members'];

$subject = 'Registration Confirmation - ShubhAarambh Hackathon 2025';
$message = "Thank you for registering your team: $team_name\n\nTeam Details:\n";
foreach ($members as $member) {
  $message .= "Name: {$member['name']}\nPhone: {$member['phone']}\nEmail: {$member['email']}\n\n";
}
$message .= "We look forward to seeing your innovations!\n\nBest,\nShubhAarambh Team";

$headers = "From: no-reply@shubhaarambh.com\r\n" .
           "Reply-To: support@shubhaarambh.com\r\n" .
           "X-Mailer: PHP/" . phpversion();

// Send email to each member
$success = true;
foreach ($members as $member) {
  if (!mail($member['email'], $subject, $message, $headers)) {
    $success = false;
    break;
  }
}

if ($success) {
  echo json_encode(['success' => true]);
} else {
  echo json_encode(['success' => false, 'error' => 'Failed to send emails']);
}
