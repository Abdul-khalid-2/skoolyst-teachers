<?php

class PortfolioController extends Controller
{
    public function show(string $slug): void
    {
        $teacher = Teacher::findBySlug($slug);

        if (!$teacher || $teacher['role'] !== 'teacher') {
            http_response_code(404);
            View::render('errors/404');
            return;
        }

        // Owner previewing their own (possibly hidden) portfolio is always allowed
        $isOwner = Auth::check() && Auth::id() == $teacher['id'];
        if (!$teacher['is_public'] && !$isOwner) {
            http_response_code(404);
            View::render('errors/404');
            return;
        }

        if (!$isOwner) {
            Teacher::incrementViews($teacher['id']);
        }

        $template = in_array($teacher['template'], ['default'], true) ? $teacher['template'] : 'default';

        // Same eligibility rule as the public directory/sitemap
        // (status='active' AND is_public=1). A profile that fails it is
        // still reachable directly (e.g. the owner previewing it, or an
        // old shared link) but must never be indexed.
        $isDirectoryEligible = $teacher['status'] === 'active' && (bool) $teacher['is_public'];

        View::render('portfolio/show', [
            'title'       => Teacher::seoTitle($teacher),
            'description' => Teacher::seoDescription($teacher),
            'canonical'   => Helpers::url('/p/' . $teacher['slug']),
            'robots'      => $isDirectoryEligible ? 'index, follow' : 'noindex, follow',
            'teacher'     => $teacher,
            'isOwner'     => $isOwner,
            'template'    => $template,
        ]);
    }

    public function resume(string $slug): void
    {
        $teacher = Teacher::findBySlug($slug);
        if (!$teacher || $teacher['role'] !== 'teacher') {
            http_response_code(404);
            View::render('errors/404');
            return;
        }

        $isOwner = Auth::check() && Auth::id() == $teacher['id'];

        // Teacher-controlled: resume can be public, or restricted to logged-in teachers.
        $requiresLogin = ($teacher['resume_access'] ?? 'everyone') === 'login_required';
        if ($requiresLogin && !Auth::check() && !$isOwner) {
            View::render('portfolio/resume-locked', [
                'title'   => 'Login required — ' . $teacher['full_name'],
                'teacher' => $teacher,
            ]);
            return;
        }

        // If teacher uploaded their own PDF resume, serve that file directly
        if (!empty($teacher['resume_file'])) {
            $path = ASSETS_PATH . '/' . $teacher['resume_file'];
            if (file_exists($path)) {
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="' . Helpers::slugify($teacher['full_name']) . '-resume.pdf"');
                header('Content-Length: ' . filesize($path));
                readfile($path);
                exit;
            }
        }

        // Otherwise render the dynamic, printable resume view
        View::render('portfolio/resume', [
            'title'   => $teacher['full_name'] . ' — Resume',
            'teacher' => $teacher,
        ]);
    }

    /**
     * Phone numbers are never rendered into the page HTML - not even for
     * logged-in users - so they can't be read via "view source" or
     * inspect element. The number only ever lives here, in the
     * controller, and is handed to the browser in an authenticated
     * on-click response, which also logs who called whom for the
     * teacher's contact history. The tel: navigation happens client-side
     * once the number arrives.
     */
    public function logCall(string $slug): void
    {
        if (!Auth::check()) {
            $this->json(['success' => false, 'message' => 'Login required to contact by phone.'], 401);
            return;
        }

        if (!Helpers::checkCsrf($this->input('_csrf'))) {
            $this->json(['success' => false, 'message' => 'Invalid or expired session.'], 419);
            return;
        }

        $teacher = Teacher::findBySlug($slug);
        if (!$teacher || $teacher['role'] !== 'teacher' || empty($teacher['phone'])) {
            $this->json(['success' => false, 'message' => 'Teacher not found.'], 404);
            return;
        }

        $callerId = Auth::id();
        // Don't log an owner "calling" their own portfolio while previewing it.
        if ((int) $teacher['id'] !== (int) $callerId) {
            ContactCall::log((int) $teacher['id'], (int) $callerId);
        }

        $this->json(['success' => true, 'phone' => $teacher['phone']]);
    }
}
