<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\BranchModel;

class UserController extends BaseController
{
    protected UserModel $userModel;
    protected BranchModel $branchModel;

    public function __construct()
    {
        $this->userModel  = new UserModel();
        $this->branchModel = new BranchModel();
    }

    public function index()
    {
        if ($redirect = $this->enforceRoles(['admin'])) {
            return $redirect;
        }

        $users = $this->userModel
            ->orderBy('id', 'ASC')
            ->findAll();

        return view('users/index', [
            'users' => $users,
        ]);
    }

    public function new()
    {
        if ($redirect = $this->enforceRoles(['admin'])) {
            return $redirect;
        }

        $branches = $this->branchModel
            ->orderBy('name', 'ASC')
            ->findAll();

        return view('users/new', [
            'branches' => $branches,
        ]);
    }

    public function create()
    {
        if ($redirect = $this->enforceRoles(['admin'])) {
            return $redirect;
        }

        $data = [
            'name'      => $this->request->getPost('name'),
            'email'     => $this->request->getPost('email'),
            'password'  => $this->request->getPost('password'),
            'role'      => $this->request->getPost('role'),
            'branch_id' => $this->request->getPost('branch_id') ?: null,
            'status'    => $this->request->getPost('status') ?: 'active',
            'phone'     => $this->request->getPost('phone') ?: null,
        ];

        $passwordConfirm = $this->request->getPost('password_confirm');

        $errors = [];

        if (! $data['password'] || strlen($data['password']) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        }

        if ($data['password'] !== $passwordConfirm) {
            $errors[] = 'Password confirmation does not match.';
        }

        if (! empty($errors)) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        if (! $this->userModel->insert($data)) {
            return redirect()->back()->withInput()->with('errors', $this->userModel->errors());
        }

        return redirect()->to('/users')->with('message', 'User created successfully.');
    }

    public function resetPassword($id = null)
    {
        if ($redirect = $this->enforceRoles(['admin'])) {
            return $redirect;
        }

        $user = $this->userModel->find($id);

        if (! $user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('User not found');
        }

        return view('users/reset_password', [
            'user' => $user,
        ]);
    }

    public function updatePassword($id = null)
    {
        if ($redirect = $this->enforceRoles(['admin'])) {
            return $redirect;
        }

        $user = $this->userModel->find($id);

        if (! $user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('User not found');
        }

        $newPassword        = $this->request->getPost('new_password');
        $newPasswordConfirm = $this->request->getPost('new_password_confirm');

        $errors = [];

        if (! $newPassword || strlen($newPassword) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        }

        if ($newPassword !== $newPasswordConfirm) {
            $errors[] = 'Password confirmation does not match.';
        }

        if (! empty($errors)) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        if (! $this->userModel->update($id, ['password' => $newPassword])) {
            return redirect()->back()->withInput()->with('errors', $this->userModel->errors());
        }

        return redirect()->to('/users')->with('message', 'Password updated successfully for ' . ($user['email'] ?? 'user')); 
    }

    public function edit($id = null)
    {
        if ($redirect = $this->enforceRoles(['admin'])) {
            return $redirect;
        }

        $user = $this->userModel->find($id);

        if (! $user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('User not found');
        }

        $branches = $this->branchModel
            ->orderBy('name', 'ASC')
            ->findAll();

        return view('users/edit', [
            'user'     => $user,
            'branches' => $branches,
        ]);
    }

    public function update($id = null)
    {
        if ($redirect = $this->enforceRoles(['admin'])) {
            return $redirect;
        }

        $user = $this->userModel->find($id);

        if (! $user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('User not found');
        }

        // Prevent role changes for admin accounts
        if ($user['role'] === 'admin') {
            $newRole = $this->request->getPost('role');
            if ($newRole && $newRole !== 'admin') {
                return redirect()->back()->with('errors', ['Admin roles cannot be modified for security reasons.']);
            }
        }

        $data = [
            'name'       => $this->request->getPost('name'),
            'role'       => $this->request->getPost('role'),
            'branch_id'  => $this->request->getPost('branch_id') ?: null,
            'status'     => $this->request->getPost('status'),
        ];

        // For admin users, only allow name, branch, and status changes, not role changes
        if ($user['role'] === 'admin') {
            $data = [
                'name'       => $this->request->getPost('name'),
                'branch_id'  => $this->request->getPost('branch_id') ?: null,
                'status'     => $this->request->getPost('status'),
            ];
        }

        if (! $this->userModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('errors', $this->userModel->errors());
        }

        return redirect()->to('/users')->with('message', 'User updated successfully.');
    }
}
