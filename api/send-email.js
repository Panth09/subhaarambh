const { Resend } = require('resend');

const resend = new Resend(process.env.RESEND_API_KEY);

export default async function handler(req, res) {
  // Enable CORS
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'POST');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type');

  if (req.method !== 'POST') {
    return res.status(405).json({ success: false, error: 'Method not allowed' });
  }

  const { team_name, members } = req.body;

  if (!team_name || !members || !Array.isArray(members) || members.length === 0) {
    return res.status(400).json({ success: false, error: 'Invalid or missing team data' });
  }

  // Sanitize inputs (basic sanitization)
  const sanitizedTeamName = team_name.replace(/[<>"'&]/g, '');
  const sanitizedMembers = members.map((member) => ({
    name: member.name.replace(/[<>"'&]/g, ''),
    email: member.email.replace(/[<>"'&]/g, ''),
    phone: member.phone.replace(/[<>"'&]/g, ''),
  }));

  // Build email content
  const subject = 'ShubhAarambh Hackathon 2025 - Registration Confirmation';
  let message = `Dear Team ${sanitizedTeamName},\n\n`;
  message += 'Thank you for registering for the ShubhAarambh Hackathon 2025!\n\n';
  message += 'Team Details:\n';
  sanitizedMembers.forEach((member, index) => {
    message += `Member ${index + 1}:\n`;
    message += `Name: ${member.name}\nPhone: ${member.phone}\nEmail: ${member.email}\n\n`;
  });
  message += 'We look forward to your innovative solutions!\n\n';
  message += 'Best regards,\nShubhAarambh Hackathon Team';

  try {
    // Send email to each member
    for (const member of sanitizedMembers) {
      await resend.emails.send({
        from: 'ShubhAarambh Hackathon <onboarding@resend.dev>', // Use Resend's default or your verified domain
        to: member.email,
        subject,
        text: message,
      });
    }

    return res.status(200).json({ success: true });
  } catch (error) {
    console.error('Error sending emails:', error);
    return res.status(500).json({ success: false, error: 'Failed to send confirmation emails' });
  }
}