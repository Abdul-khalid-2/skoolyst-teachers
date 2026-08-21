<?php

/**
 * Notifications
 * Builds and sends transactional emails. Keeping templates here (rather than
 * in /views) since these are plain-HTML emails, not app pages rendered by
 * View::render().
 */
class Notifications
{
    public static function sendWelcomeEmail(array $teacher): bool
    {
        $name       = Helpers::e($teacher['full_name'] ?? 'there');
        $firstName  = Helpers::e(Helpers::firstName($teacher['full_name'] ?? 'there'));
        $profileUrl = Helpers::url('/p/' . $teacher['slug']);
        $dashboardUrl = Helpers::url('/dashboard');

        $subject = 'Welcome to Skoolyst — Your Teacher Profile is Live';
        $html = self::welcomeTemplate($firstName, $name, $profileUrl, $dashboardUrl);

        return Mailer::send($teacher['email'], $subject, $html);
    }

    /**
     * Sent by an admin to nudge a teacher to fill in the sections of their
     * profile that are still empty.
     */
    public static function sendProfileReminderEmail(array $teacher, array $missingLabels): bool
    {
        $firstName    = Helpers::e(Helpers::firstName($teacher['full_name'] ?? 'there'));
        $dashboardUrl = Helpers::url('/dashboard');
        $profileUrl   = Helpers::url('/p/' . $teacher['slug']);

        $subject = 'Your Skoolyst profile is missing a few details';
        $html = self::profileReminderTemplate($firstName, $dashboardUrl, $profileUrl, $missingLabels);

        return Mailer::send($teacher['email'], $subject, $html);
    }

    private static function profileReminderTemplate(string $firstName, string $dashboardUrl, string $profileUrl, array $missingLabels): string
    {
        $itemsHtml = '';
        foreach ($missingLabels as $label) {
            $itemsHtml .= '<tr><td style="padding:6px 0;color:#4a4a4a;font-size:14px;">• &nbsp;<strong>' . Helpers::e($label) . '</strong></td></tr>';
        }
        if ($itemsHtml === '') {
            $itemsHtml = '<tr><td style="padding:6px 0;color:#4a4a4a;font-size:14px;">Just a final review — your profile looks complete!</td></tr>';
        }

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Complete your Skoolyst profile</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f6f8;font-family:'Segoe UI',Helvetica,Arial,sans-serif;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f8;padding:32px 0;">
    <tr>
      <td align="center">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background-color:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,0.06);">

          <tr>
            <td style="background:linear-gradient(135deg,#1a73e8,#0d47a1);padding:32px 40px;text-align:center;">
              <h1 style="margin:0;color:#ffffff;font-size:22px;font-weight:600;">Skoolyst Teachers</h1>
            </td>
          </tr>

          <tr>
            <td style="padding:36px 40px 8px 40px;">
              <h2 style="margin:0 0 16px 0;color:#1a1a1a;font-size:20px;">Hi {$firstName}, your profile needs a bit more info</h2>
              <p style="margin:0 0 20px 0;color:#4a4a4a;font-size:15px;line-height:1.6;">
                Your Skoolyst profile is live, but a few sections are still empty. A complete profile helps
                schools, parents, and students trust and find you faster.
              </p>
            </td>
          </tr>

          <tr>
            <td style="padding:0 40px;">
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#fff7e6;border-radius:8px;padding:16px 20px;margin:8px 0 24px 0;">
                <tr><td style="font-size:13px;color:#8a6d1a;padding-bottom:6px;font-weight:600;">Still missing:</td></tr>
                {$itemsHtml}
              </table>
            </td>
          </tr>

          <tr>
            <td style="padding:0 40px 32px 40px;text-align:center;">
              <a href="{$dashboardUrl}" style="display:inline-block;background-color:#1a73e8;color:#ffffff;text-decoration:none;font-size:15px;font-weight:600;padding:14px 32px;border-radius:8px;">
                Complete My Profile
              </a>
              <p style="margin:16px 0 0 0;font-size:13px;color:#999;">
                Your public profile: <a href="{$profileUrl}" style="color:#1a73e8;">{$profileUrl}</a>
              </p>
            </td>
          </tr>

          <tr>
            <td style="padding:24px 40px 32px 40px;border-top:1px solid #eee;">
              <p style="margin:0;color:#999;font-size:12px;line-height:1.6;">
                Sent by the Skoolyst Teachers admin team. You can update or remove your profile anytime from your dashboard.
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;
    }

    private static function welcomeTemplate(string $firstName, string $fullName, string $profileUrl, string $dashboardUrl): string
    {
        $logoUrl = Helpers::asset('image/logo.png');

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Welcome to Skoolyst</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f6f8;font-family:'Segoe UI',Helvetica,Arial,sans-serif;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f8;padding:32px 0;">
    <tr>
      <td align="center">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background-color:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,0.06);">

          <tr>
            <td style="background:linear-gradient(135deg,#1a73e8,#0d47a1);padding:32px 40px;text-align:center;">
              <h1 style="margin:0;color:#ffffff;font-size:22px;font-weight:600;">Skoolyst Teachers</h1>
            </td>
          </tr>

          <tr>
            <td style="padding:36px 40px 8px 40px;">
              <h2 style="margin:0 0 16px 0;color:#1a1a1a;font-size:20px;">Welcome aboard, {$firstName}! 🎉</h2>
              <p style="margin:0 0 16px 0;color:#4a4a4a;font-size:15px;line-height:1.6;">
                Your Skoolyst teacher account has been created successfully. You now have a personal
                <strong>online profile</strong> — a professional page you can share anywhere, so schools,
                parents, and students can find and learn about you in one place.
              </p>
            </td>
          </tr>

          <tr>
            <td style="padding:0 40px;">
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0f6ff;border-radius:8px;padding:16px 20px;margin:8px 0 24px 0;">
                <tr>
                  <td style="font-size:13px;color:#666;padding-bottom:4px;">Your public profile link</td>
                </tr>
                <tr>
                  <td style="font-size:14px;color:#1a73e8;word-break:break-all;font-weight:600;">
                    <a href="{$profileUrl}" style="color:#1a73e8;text-decoration:none;">{$profileUrl}</a>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <tr>
            <td style="padding:0 40px;text-align:center;">
              <a href="{$dashboardUrl}" style="display:inline-block;background-color:#1a73e8;color:#ffffff;text-decoration:none;font-size:15px;font-weight:600;padding:14px 32px;border-radius:8px;">
                Log In &amp; Complete Your Profile
              </a>
            </td>
          </tr>

          <tr>
            <td style="padding:32px 40px 8px 40px;">
              <p style="margin:0 0 12px 0;color:#1a1a1a;font-size:15px;font-weight:600;">
                A few things worth adding today:
              </p>
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                  <td style="padding:6px 0;color:#4a4a4a;font-size:14px;">🎓 &nbsp;<strong>Education</strong> — degrees, institutions, years</td>
                </tr>
                <tr>
                  <td style="padding:6px 0;color:#4a4a4a;font-size:14px;">💼 &nbsp;<strong>Experience</strong> — past and current teaching roles</td>
                </tr>
                <tr>
                  <td style="padding:6px 0;color:#4a4a4a;font-size:14px;">🛠️ &nbsp;<strong>Skills</strong> — subjects and teaching competencies</td>
                </tr>
                <tr>
                  <td style="padding:6px 0;color:#4a4a4a;font-size:14px;">🏆 &nbsp;<strong>Awards</strong> — recognitions and achievements</td>
                </tr>
                <tr>
                  <td style="padding:6px 0 20px 0;color:#4a4a4a;font-size:14px;">📜 &nbsp;<strong>Certificates</strong> — trainings and credentials</td>
                </tr>
              </table>
              <p style="margin:0 0 24px 0;color:#4a4a4a;font-size:14px;line-height:1.6;">
                You can log in and update any of this anytime, from anywhere — your profile stays live and
                shareable the moment you save.
              </p>
            </td>
          </tr>

          <tr>
            <td style="padding:24px 40px 32px 40px;border-top:1px solid #eee;">
              <p style="margin:0;color:#999;font-size:12px;line-height:1.6;">
                You're receiving this because an account was created for {$fullName} on Skoolyst Teachers.
                If this wasn't you, please contact support.
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;
    }
}
