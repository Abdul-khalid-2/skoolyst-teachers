<?php

class AdminController extends Controller
{
    public function dashboard(): void
    {
        $this->requireAdmin();
        $total = Teacher::count("role = 'teacher'");
        $active = Teacher::count("role = 'teacher' AND status = 'active'");
        $pending = Teacher::count("role = 'teacher' AND status = 'pending'");
        $inactive = Teacher::count("role = 'teacher' AND status = 'inactive'");

        $page = max(1, (int) $this->input('page', 1));
        $result = Teacher::adminList($page, 5);

        View::render('admin/dashboard', [
            'title' => 'Admin Dashboard',
            'stats' => compact('total', 'active', 'pending', 'inactive'),
            'result' => $result,
        ]);
    }

    public function teachers(): void
    {
        $this->requireAdmin();
        $page = max(1, (int) $this->input('page', 1));
        $search = $this->input('search', '');
        $result = Teacher::adminList($page, 15, $search);

        View::render('admin/teachers', [
            'title' => 'Manage Teachers',
            'result' => $result,
            'search' => $search,
        ]);
    }

    public function updateStatus(int $id): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();

        $status = $this->input('status');
        if (!in_array($status, ['active', 'inactive', 'pending'], true)) {
            $this->redirect('/admin/teachers');
        }

        Teacher::updateProfile($id, ['status' => $status]);
        Helpers::flash('success', 'Teacher status updated.');
        $this->redirect('/admin/teachers');
    }

    public function delete(int $id): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();
        Teacher::delete($id);
        Helpers::flash('success', 'Teacher account deleted.');
        $this->redirect('/admin/teachers');
    }

    public function sendWelcomeEmail(int $id): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();

        $teacher = Teacher::find($id);
        if (!$teacher) {
            Helpers::flash('errors', 'Teacher not found.');
            $this->redirect('/admin/teachers');
        }

        $sent = Notifications::sendWelcomeEmail($teacher);
        Helpers::flash($sent ? 'success' : 'errors', $sent
            ? 'Welcome email sent to ' . $teacher['email'] . '.'
            : 'Could not send the welcome email. Check your SMTP settings and try again.');

        $this->redirect('/admin/teachers');
    }

    public function sendProfileReminder(int $id): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();

        $teacher = Teacher::find($id);
        if (!$teacher) {
            Helpers::flash('errors', 'Teacher not found.');
            $this->redirect('/admin/teachers');
        }

        $missing = Teacher::missingFields($teacher);
        $sent = Notifications::sendProfileReminderEmail($teacher, $missing);
        Helpers::flash($sent ? 'success' : 'errors', $sent
            ? 'Profile completion reminder sent to ' . $teacher['email'] . '.'
            : 'Could not send the reminder email. Check your SMTP settings and try again.');

        $this->redirect('/admin/teachers');
    }
}
