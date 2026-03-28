<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;

class User extends Controller
{
    // ─── DB TEST (delete after confirming everything works) ───────
    public function dbtest()
    {
        $db = \Config\Database::connect();
        try {
            $db->query('SELECT 1');
            echo '<h2 style="color:green">✅ Connected to: ' . $db->getDatabase() . '</h2>';
            echo '<p>Method test: ' . $this->request->getMethod() . '</p>';

            foreach (['users', 'reviews', 'watchlist'] as $table) {
                if ($db->tableExists($table)) {
                    echo '<p style="color:green">✅ Table <b>' . $table . '</b> exists (' . $db->table($table)->countAll() . ' rows)</p>';
                } else {
                    echo '<p style="color:red">❌ Table <b>' . $table . '</b> NOT found</p>';
                }
            }
        } catch (\Exception $e) {
            echo '<h2 style="color:red">❌ ' . $e->getMessage() . '</h2>';
        }
    }

    // ─── REGISTER ────────────────────────────────────────────────
    public function register()
    {
        if (session()->get('is_logged_in')) {
            return redirect()->to('/');
        }

        // Use request->is() which works on ALL CI4 versions
        if ($this->request->is('post')) {

            $rules = [
                'username' => [
                    'rules'  => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
                    'errors' => [
                        'required'   => 'Username is required.',
                        'min_length' => 'Username must be at least 3 characters.',
                        'max_length' => 'Username cannot exceed 50 characters.',
                        'is_unique'  => 'That username is already taken.',
                    ],
                ],
                'password' => [
                    'rules'  => 'required|min_length[6]',
                    'errors' => [
                        'required'   => 'Password is required.',
                        'min_length' => 'Password must be at least 6 characters.',
                    ],
                ],
                'confirm_password' => [
                    'rules'  => 'required|matches[password]',
                    'errors' => [
                        'required' => 'Please confirm your password.',
                        'matches'  => 'Passwords do not match.',
                    ],
                ],
            ];

            if (!$this->validate($rules)) {
                return view('register', [
                    'validation' => $this->validator,
                    'old'        => $this->request->getPost(),
                ]);
            }

            $userModel = new UserModel();
            $data = [
                'username'      => trim($this->request->getPost('username')),
                'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            ];

            if ($userModel->insert($data)) {
                session()->setFlashdata('success', 'Account created! You can now log in.');
                return redirect()->to('/user/login');
            }

            // Show DB errors if insert fails
            $errors = $userModel->errors();
            session()->setFlashdata('error', 'Failed to save: ' . implode(', ', $errors));
            return redirect()->to('/user/register');
        }

        return view('register', ['validation' => null, 'old' => []]);
    }

    // ─── LOGIN ────────────────────────────────────────────────────
    public function login()
    {
        if (session()->get('is_logged_in')) {
            return redirect()->to('/');
        }

        if ($this->request->is('post')) {

            $rules = [
                'username' => ['rules' => 'required', 'errors' => ['required' => 'Please enter your username.']],
                'password' => ['rules' => 'required', 'errors' => ['required' => 'Please enter your password.']],
            ];

            if (!$this->validate($rules)) {
                return view('login', [
                    'validation' => $this->validator,
                    'old'        => $this->request->getPost(),
                ]);
            }

            $userModel = new UserModel();
            $user = $userModel->where('username', $this->request->getPost('username'))->first();

            if ($user && password_verify($this->request->getPost('password'), $user['password_hash'])) {
                session()->set([
                    'user_id'      => $user['id'],
                    'username'     => $user['username'],
                    'is_logged_in' => true,
                ]);
                session()->setFlashdata('success', 'Welcome back, ' . esc($user['username']) . '!');
                return redirect()->to('/');
            }

            session()->setFlashdata('error', 'Incorrect username or password.');
            return view('login', [
                'validation' => null,
                'old'        => ['username' => $this->request->getPost('username')],
            ]);
        }

        return view('login', ['validation' => null, 'old' => []]);
    }

    // ─── LOGOUT ───────────────────────────────────────────────────
    public function logout()
    {
        session()->destroy();
        session()->setFlashdata('success', 'You have been logged out.');
        return redirect()->to('/');
    }
}
