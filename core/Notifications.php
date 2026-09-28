<?php

/**
 * Notifications
 * Builds and sends transactional emails. Bodies are plain text because the
 * Skoolyst Email API (see Mailer) only accepts plain-text messages.
 */
class Notifications
{
    public static function sendWelcomeEmail(array $teacher): bool
    {
        $fullName     = $teacher['full_name'] ?? 'there';
        $firstName    = Helpers::firstName($fullName);
        $profileUrl   = Helpers::url('/p/' . $teacher['slug']);
        $dashboardUrl = Helpers::url('/dashboard');

        $subject = 'Welcome to Skoolyst — Your Teacher Profile is Live';
        $body = <<<TEXT
Welcome aboard, {$firstName}!

Your Skoolyst teacher account has been created successfully. You now have a personal online profile — a professional page you can share anywhere, so schools, parents, and students can find and learn about you in one place.

Your public profile link:
{$profileUrl}

Log in and complete your profile:
{$dashboardUrl}

A few things worth adding today:
- Education — degrees, institutions, years
- Experience — past and current teaching roles
- Skills — subjects and teaching competencies
- Awards — recognitions and achievements
- Certificates — trainings and credentials

You can log in and update any of this anytime, from anywhere — your profile stays live and shareable the moment you save.

— Skoolyst Teachers

You're receiving this because an account was created for {$fullName} on Skoolyst Teachers. If this wasn't you, please contact support.
TEXT;

        return Mailer::send($teacher['email'], $subject, $body);
    }

    /**
     * Sent by an admin to nudge a teacher to fill in the sections of their
     * profile that are still empty.
     */
    public static function sendProfileReminderEmail(array $teacher, array $missingLabels): bool
    {
        $firstName    = Helpers::firstName($teacher['full_name'] ?? 'there');
        $dashboardUrl = Helpers::url('/dashboard');
        $profileUrl   = Helpers::url('/p/' . $teacher['slug']);

        $missing = $missingLabels
            ? "Still missing:\n- " . implode("\n- ", $missingLabels)
            : 'Just a final review — your profile looks complete!';

        $subject = 'Your Skoolyst profile is missing a few details';
        $body = <<<TEXT
Hi {$firstName}, your profile needs a bit more info.

Your Skoolyst profile is live, but a few sections are still empty. A complete profile helps schools, parents, and students trust and find you faster.

{$missing}

Complete your profile:
{$dashboardUrl}

Your public profile:
{$profileUrl}

— Skoolyst Teachers

Sent by the Skoolyst Teachers admin team. You can update or remove your profile anytime from your dashboard.
TEXT;

        return Mailer::send($teacher['email'], $subject, $body);
    }
}
