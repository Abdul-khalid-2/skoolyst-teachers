<?php

/**
 * Generates /sitemap.xml on every request (no static file on disk, so it's
 * always in sync with current teacher data). Includes only the homepage and
 * currently public + active teacher profiles - see
 * Teacher::allPublicForSitemap() for the exact visibility rule. Auth,
 * dashboard, and admin routes are never listed here since nothing in this
 * controller references them.
 */
class SitemapController extends Controller
{
    public function index(): void
    {
        header('Content-Type: application/xml; charset=UTF-8');

        $urls = [
            [
                'loc'        => Helpers::url('/'),
                'changefreq' => 'daily',
                'priority'   => '1.0',
            ],
        ];

        foreach (Teacher::allPublicForSitemap() as $teacher) {
            $urls[] = [
                'loc'        => Helpers::url('/p/' . $teacher['slug']),
                'lastmod'    => date('Y-m-d', strtotime($teacher['updated_at'] ?? 'now')),
                'changefreq' => 'weekly',
                'priority'   => '0.8',
            ];
        }

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $url) {
            echo "  <url>\n";
            echo '    <loc>' . Helpers::e($url['loc']) . "</loc>\n";
            if (!empty($url['lastmod'])) {
                echo '    <lastmod>' . Helpers::e($url['lastmod']) . "</lastmod>\n";
            }
            if (!empty($url['changefreq'])) {
                echo '    <changefreq>' . Helpers::e($url['changefreq']) . "</changefreq>\n";
            }
            if (!empty($url['priority'])) {
                echo '    <priority>' . Helpers::e($url['priority']) . "</priority>\n";
            }
            echo "  </url>\n";
        }
        echo '</urlset>' . "\n";
    }
}
