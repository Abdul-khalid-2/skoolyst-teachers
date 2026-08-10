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

        View::render('portfolio/show', [
            'title'   => $teacher['full_name'] . ' | Teacher Portfolio',
            'teacher' => $teacher,
            'isOwner' => $isOwner,
            'template'=> $template,
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
     * Phone numbers are hidden from anonymous visitors. A logged-in user
     * clicking "Call Me" triggers this (async, fire-and-forget) so the
     * teacher can see a history of who tried to contact them by phone.
     * The tel: link itself works client-side regardless of this call
     * succeeding, so a network hiccup never blocks the actual phone call.
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
        if (!$teacher || $teacher['role'] !== 'teacher') {
            $this->json(['success' => false, 'message' => 'Teacher not found.'], 404);
            return;
        }

        $callerId = Auth::id();
        // Don't log an owner "calling" their own portfolio while previewing it.
        if ((int) $teacher['id'] !== (int) $callerId) {
            ContactCall::log((int) $teacher['id'], (int) $callerId);
        }

        $this->json(['success' => true]);
    }
}
