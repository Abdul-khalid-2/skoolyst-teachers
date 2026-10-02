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
     */
    public function skoolystCallback(): void
    {
        if (!SkoolystAuth::verifyState($_GET['state'] ?? null)) {
            Helpers::flash('errors', 'Your Skoolyst sign-in expired or was invalid. Please try again.');
            $this->redirect('/login');
        }

        $code = $_GET['code'] ?? '';
        $identity = is_string($code) && $code !== '' ? SkoolystAuth::exchangeCode($code) : null;
        if (!$identity) {
            Helpers::flash('errors', 'Could not sign you in with Skoolyst. Please try again.');
            $this->redirect('/login');
        }

        $this->loginWithProvider('skoolyst_id', $identity);
    }

    /**
     * "Continue with Google" — send the browser to Google's consent screen.
     */
    public function googleRedirect(): void
    {
        if (Auth::check()) $this->redirect('/dashboard');

        if (!GoogleAuth::isConfigured()) {
            Helpers::flash('errors', 'Continue with Google is not available right now. Please use your email and password.');
            $this->redirect('/login');
        }

        header('Location: ' . GoogleAuth::authorizeUrl());
        exit;
    }

    /**
     * Google redirects back here with ?code=&state=, or ?error=access_denied
     * when the user cancels on the consent screen.
     */
    public function googleCallback(): void
    {
        $stateOk = GoogleAuth::verifyState($_GET['state'] ?? null);

        if (($_GET['error'] ?? '') === 'access_denied') {
            $this->redirect('/login'); // user cancelled — no error needed
        }

        if (!$stateOk) {
            Helpers::flash('errors', 'Your Google sign-in expired or was invalid. Please try again.');
            $this->redirect('/login');
        }

        $code = $_GET['code'] ?? '';
        $identity = is_string($code) && $code !== '' ? GoogleAuth::exchangeCode($code) : null;
        if (!$identity) {
            Helpers::flash('errors', 'Could not sign you in with Google. Please try again.');
            $this->redirect('/login');
        }

        $this->loginWithProvider('google_id', $identity);
    }

    /**
     * Shared by every external login (Skoolyst, Google). $column is the
     * teachers column holding that provider's stable account id; $identity
     * is ['id', 'name', 'email', 'email_verified'] from the provider.
     *
     *  - Known provider id               -> log in.
     *  - Email exists, column empty,
     *    email verified, not super-admin -> link the provider id, log in.
     *  - Email exists otherwise          -> refuse; use password instead.
     *  - Unknown email                   -> create a teacher account exactly
     *                                       like the register form does.
     */
    private function loginWithProvider(string $column, array $identity): void
    {
        $user = Teacher::findBy($column, $identity['id']);

        if (!$user && ($existing = Teacher::findByEmail($identity['email']))) {
            // Never link on email alone: the provider must have verified the
            // address, the account must not already be linked to a different
            // provider account, and the super-admin is never auto-linked.
            if (!$identity['email_verified'] || !empty($existing[$column]) || $existing['role'] === 'super-admin') {
                Helpers::flash('errors', 'An account with this email already exists. Please log in with your password.');
                Helpers::setOld(['email' => $identity['email']]);
                $this->redirect('/login');
            }
            Teacher::updateProfile((int) $existing['id'], array_filter([
                $column             => $identity['id'],
                'email_verified_at' => $existing['email_verified_at'] ? null : date('Y-m-d H:i:s'),
            ]));
            $user = Teacher::find((int) $existing['id']);
        }

        if (!$user) {
            $fullName = $identity['name'] !== '' ? $identity['name'] : strstr($identity['email'], '@', true);
            $id = Teacher::create([
                'slug'              => Helpers::uniqueSlug($fullName),
                'role'              => 'teacher',
                // No local password: this account signs in through the provider.
                'password'          => password_hash(bin2hex(random_bytes(32)), PASSWORD_BCRYPT),
                'email'             => $identity['email'],
                'email_verified_at' => $identity['email_verified'] ? date('Y-m-d H:i:s') : null,
                $column             => $identity['id'],
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
