<?php

class DashboardController extends Controller
{
    public function index(): void
    {
        $user = $this->requireAuth();
        View::render('dashboard/edit', [
            'title' => 'My Dashboard',
            'user'  => $user,
            'tab'   => $this->input('tab', 'basic'),
        ]);
    }

    /** Basic info + about section */
    public function updateBasic(): void
    {
        $user = $this->requireAuth();
        $this->verifyCsrf();

        $data = [
            'full_name'        => $this->input('full_name'),
            'profession_title' => $this->input('profession_title'),
            'teacher_type'     => $this->input('teacher_type') ?: null,
            'subject'          => $this->input('subject'),
            'qualification'    => $this->input('qualification'),
            'phone'            => $this->input('phone'),
            'city'             => $this->input('city'),
            'country'          => $this->input('country'),
            'gender'           => $this->input('gender') ?: null,
            'birthday'         => $this->input('birthday') ?: null,
            'bio'              => $this->input('bio'),
            'website'          => $this->input('website'),
            'years_experience' => $this->input('years_experience') ?: null,
            'freelance_status' => $this->input('freelance_status') ?: null,
        ];

        Teacher::updateProfile($user['id'], $data);
        Helpers::flash('success', 'Basic information updated.');
        $this->redirect('/dashboard?tab=basic');
    }

    /** Generic handler for all repeatable JSON sections */
    public function updateSection(string $section): void
    {
        $user = $this->requireAuth();
        $this->verifyCsrf();

        if (!in_array($section, Teacher::JSON_FIELDS, true)) {
            http_response_code(400);
            die('Unknown section.');
        }

        $items = json_decode($this->input('payload', '[]'), true);
        if (!is_array($items)) $items = [];

        // strip empty rows & sanitize strings
        $clean = [];
        foreach ($items as $item) {
            if (!is_array($item)) continue;
            $hasValue = false;
            foreach ($item as $k => $v) {
                $item[$k] = is_string($v) ? trim($v) : $v;
                if ($item[$k] !== '' && $item[$k] !== null) $hasValue = true;
            }
            if ($hasValue) $clean[] = $item;
        }

        Teacher::updateJsonField($user['id'], $section, $clean);
        Helpers::flash('success', ucfirst(str_replace('_', ' ', $section)) . ' updated.');
        $this->redirect('/dashboard?tab=' . $section);
    }

    public function updateTemplate(): void
    {
        $user = $this->requireAuth();
        $this->verifyCsrf();

        $template = $this->input('template', 'default');
        $allowed = ['default']; // future themes get added here
        if (!in_array($template, $allowed, true)) $template = 'default';

        Teacher::updateProfile($user['id'], ['template' => $template]);
        Helpers::flash('success', 'Template updated.');
        $this->redirect('/dashboard?tab=template');
    }

    public function uploadPhoto(): void
    {
        $user = $this->requireAuth();
        $this->verifyCsrf();

        if (empty($_FILES['profile_photo']['name'])) {
            Helpers::flash('errors', 'Please choose an image to upload.');
            $this->redirect('/dashboard?tab=basic');
        }

        $file = $_FILES['profile_photo'];
        if ($file['size'] > MAX_PROFILE_PHOTO_SIZE || !in_array(mime_content_type($file['tmp_name']), ALLOWED_PHOTO_TYPES, true)) {
            Helpers::flash('errors', 'Photo must be JPG/PNG/WEBP under 3MB.');
            $this->redirect('/dashboard?tab=basic');
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'p_' . $user['uuid'] . '_' . time() . '.' . strtolower($ext);
        move_uploaded_file($file['tmp_name'], UPLOAD_PROFILE_DIR . '/' . $filename);

        // Remove the previous photo now that the new one is safely saved
        if (!empty($user['profile_photo'])) {
            $oldPath = ASSETS_PATH . '/' . $user['profile_photo'];
            if (is_file($oldPath)) {
                @unlink($oldPath);
            }
        }

        Teacher::updateProfile($user['id'], ['profile_photo' => 'uploads/profile/' . $filename]);
        Helpers::flash('success', 'Profile photo updated.');
        $this->redirect('/dashboard?tab=basic');
    }

    public function uploadResume(): void
    {
        $user = $this->requireAuth();
        $this->verifyCsrf();

        if (empty($_FILES['resume_file']['name'])) {
            Helpers::flash('errors', 'Please choose a PDF file to upload.');
            $this->redirect('/dashboard?tab=resume');
        }

        $file = $_FILES['resume_file'];
        if ($file['size'] > MAX_RESUME_SIZE || mime_content_type($file['tmp_name']) !== 'application/pdf') {
            Helpers::flash('errors', 'Resume must be a PDF under 5MB.');
            $this->redirect('/dashboard?tab=resume');
        }

        $filename = 'r_' . $user['uuid'] . '_' . time() . '.pdf';
        move_uploaded_file($file['tmp_name'], UPLOAD_RESUME_DIR . '/' . $filename);

        Teacher::updateProfile($user['id'], ['resume_file' => 'uploads/resume/' . $filename]);
        Helpers::flash('success', 'Resume uploaded — visitors will download this file directly.');
        $this->redirect('/dashboard?tab=resume');
    }

    public function visibility(): void
    {
        $user = $this->requireAuth();
        $this->verifyCsrf();
        $isPublic = $this->input('is_public') ? 1 : 0;
        Teacher::updateProfile($user['id'], ['is_public' => $isPublic]);
        Helpers::flash('success', $isPublic ? 'Your portfolio is now public.' : 'Your portfolio is now hidden from the directory.');
        $this->redirect('/dashboard?tab=settings');
    }

    public function resumeAccess(): void
    {
        $user = $this->requireAuth();
        $this->verifyCsrf();
        $access = $this->input('resume_access') === 'login_required' ? 'login_required' : 'everyone';
        Teacher::updateProfile($user['id'], ['resume_access' => $access]);
        Helpers::flash('success', $access === 'login_required'
            ? 'Only logged-in teachers can now download your resume.'
            : 'Your resume can now be downloaded by everyone.');
        $this->redirect('/dashboard?tab=settings');
    }
}
