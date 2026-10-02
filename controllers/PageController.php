<?php

/**
 * Static legal pages. Their public URLs are what the Google Cloud OAuth
 * consent screen links to, so keep the paths stable.
 */
class PageController extends Controller
{
    public function privacy(): void
    {
        View::render('pages/privacy', [
            'title'       => 'Privacy Policy | Skoolyst Teachers',
            'description' => 'How Skoolyst Teachers collects, uses, shares and protects your personal information, including data received through Google and Skoolyst sign-in.',
            'canonical'   => Helpers::url('/privacy-policy'),
        ]);
    }

    public function terms(): void
    {
        View::render('pages/terms', [
            'title'       => 'Terms of Service | Skoolyst Teachers',
            'description' => 'The terms that apply when you use Skoolyst Teachers to create a teacher portfolio or browse the teacher directory.',
            'canonical'   => Helpers::url('/terms-of-service'),
        ]);
    }
}
