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
}
