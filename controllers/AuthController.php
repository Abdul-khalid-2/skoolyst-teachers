<?php

class AuthController extends Controller
{
    public function registerForm(): void
    {
        if (Auth::check()) $this->redirect('/dashboard');
        View::render('auth/register', ['title' => 'Create your teacher account']);
    }

    public function register(): void
    {
        $this->verifyCsrf();

        $fullName = $this->input('full_name');
        $email    = strtolower($this->input('email'));
        $password = $this->input('password');
        $confirm  = $this->input('password_confirmation');

        $errors = [];
        if (strlen($fullName) < 3) $errors[] = 'Please enter your full name.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
        if (strlen($password) < PASSWORD_MIN_LENGTH) $errors[] = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters.';
        if ($password !== $confirm) $errors[] = 'Passwords do not match.';
        if (Teacher::findByEmail($email)) $errors[] = 'An account with this email already exists.';

        if ($errors) {
            Helpers::flash('errors', implode('|', $errors));
            Helpers::setOld(['full_name' => $fullName, 'email' => $email]);
            $this->redirect('/register');
        }

        $id = Teacher::create([
            'slug'     => Helpers::uniqueSlug($fullName),
            'role'     => 'teacher',
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'email'    => $email,
            'status'   => 'active',
            'is_public'=> 1,
            'full_name'=> $fullName,
            'template' => 'default',
        ]);

        $user = Teacher::find($id);
        Auth::login($user);

        // Best-effort: don't let a slow/broken SMTP connection block registration.
        Notifications::sendWelcomeEmail($user);

        Helpers::flash('success', 'Welcome! Let\'s build your portfolio — fill in your details below.');
        $this->redirect('/dashboard');
    }

    public function loginForm(): void
    {
        if (Auth::check()) $this->redirect('/dashboard');
        View::render('auth/login', ['title' => 'Login']);
    }

    public function login(): void
    {
        $this->verifyCsrf();

        $email = strtolower($this->input('email'));
        $password = $this->input('password');

        if (Auth::attempt($email, $password)) {
            $user = Auth::user();
            if ($user['role'] === 'super-admin') {
                $this->redirect('/admin');
            }
            $this->redirect('/dashboard');
        }

        Helpers::flash('errors', 'Invalid email/password, or your account is inactive.');
        Helpers::setOld(['email' => $email]);
        $this->redirect('/login');
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect('/login');
    }
}
