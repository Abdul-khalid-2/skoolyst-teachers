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

        // Best-effort: a failed send is logged and never blocks registration.
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

    /**
     * "Login with Skoolyst" — send the browser to skoolyst.com to sign in.
     */
    public function skoolystRedirect(): void
    {
        if (Auth::check()) $this->redirect('/dashboard');

        if (!SkoolystAuth::isConfigured()) {
            Helpers::flash('errors', 'Login with Skoolyst is not available right now. Please use your email and password.');
            $this->redirect('/login');
        }

        header('Location: ' . SkoolystAuth::authorizeUrl());
        exit;
    }

    /**
     * skoolyst.com redirects back here with ?code=&state= (or ?error=).
     * Finds the local teacher by Skoolyst id, links an existing account
     * with the same verified email, or creates a new teacher account
     * exactly like the normal register form does.
     */
    public function skoolystCallback(): void
    {
        if (!SkoolystAuth::verifyState($_GET['state'] ?? null)) {
            Helpers::flash('errors', 'Your Skoolyst sign-in expired or was invalid. Please try again.');
            $this->redirect('/login');
        }

        $code = $_GET['code'] ?? '';
        $skoolystUser = is_string($code) && $code !== '' ? SkoolystAuth::exchangeCode($code) : null;
        if (!$skoolystUser) {
            Helpers::flash('errors', 'Could not sign you in with Skoolyst. Please try again.');
            $this->redirect('/login');
        }

        $user = Teacher::findBySkoolystId($skoolystUser['id']);

        if (!$user && ($existing = Teacher::findByEmail($skoolystUser['email']))) {
            // Only link by email when skoolyst.com has verified the address,
            // and never auto-link the super-admin account.
            if (!$skoolystUser['email_verified'] || $existing['role'] === 'super-admin') {
                Helpers::flash('errors', 'An account with this email already exists. Please log in with your password.');
                Helpers::setOld(['email' => $skoolystUser['email']]);
                $this->redirect('/login');
            }
            Teacher::updateProfile((int) $existing['id'], array_filter([
                'skoolyst_id'       => $skoolystUser['id'],
                'email_verified_at' => $existing['email_verified_at'] ? null : date('Y-m-d H:i:s'),
            ]));
            $user = Teacher::find((int) $existing['id']);
        }

        if (!$user) {
            $fullName = $skoolystUser['name'] !== '' ? $skoolystUser['name'] : strstr($skoolystUser['email'], '@', true);
            $id = Teacher::create([
                'slug'              => Helpers::uniqueSlug($fullName),
                'role'              => 'teacher',
                // No local password: this account signs in through Skoolyst.
                'password'          => password_hash(bin2hex(random_bytes(32)), PASSWORD_BCRYPT),
                'email'             => $skoolystUser['email'],
                'email_verified_at' => $skoolystUser['email_verified'] ? date('Y-m-d H:i:s') : null,
                'skoolyst_id'       => $skoolystUser['id'],
                'status'            => 'active',
                'is_public'         => 1,
                'full_name'         => $fullName,
                'template'          => 'default',
            ]);
            $user = Teacher::find($id);
            Auth::login($user);

            // Best-effort: a failed send is logged and never blocks registration.
            Notifications::sendWelcomeEmail($user);

            Helpers::flash('success', 'Welcome! Let\'s build your portfolio — fill in your details below.');
            $this->redirect('/dashboard');
        }

        if ($user['status'] !== 'active') {
            Helpers::flash('errors', 'Your account is inactive. Please contact support.');
            $this->redirect('/login');
        }

        Auth::login($user);
        $this->redirect($user['role'] === 'super-admin' ? '/admin' : '/dashboard');
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect('/login');
    }
}
