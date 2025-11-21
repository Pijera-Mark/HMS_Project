<?php

namespace App\Controllers;

use App\Models\BranchModel;

class BranchController extends BaseController
{
    protected BranchModel $branchModel;

    public function __construct()
    {
        $this->branchModel = new BranchModel();
    }

    public function index()
    {
        if ($redirect = $this->enforceRoles(['admin'])) {
            return $redirect;
        }

        $branches = $this->branchModel->findAll();

        return view('branches/index', [
            'branches' => $branches,
        ]);
    }

    public function new()
    {
        if ($redirect = $this->enforceRoles(['admin'])) {
            return $redirect;
        }

        return view('branches/new');
    }

    public function create()
    {
        if ($redirect = $this->enforceRoles(['admin'])) {
            return $redirect;
        }

        $data = $this->request->getPost();

        if (! $this->branchModel->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->branchModel->errors());
        }

        return redirect()->to('/branches');
    }

    public function edit($id = null)
    {
        if ($redirect = $this->enforceRoles(['admin'])) {
            return $redirect;
        }

        $branch = $this->branchModel->find($id);

        if (! $branch) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Branch not found');
        }

        return view('branches/edit', [
            'branch' => $branch,
        ]);
    }

    public function update($id = null)
    {
        if ($redirect = $this->enforceRoles(['admin'])) {
            return $redirect;
        }

        $data = $this->request->getPost();

        if (! $this->branchModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('errors', $this->branchModel->errors());
        }

        return redirect()->to('/branches');
    }

    public function delete($id = null)
    {
        if ($redirect = $this->enforceRoles(['admin'])) {
            return $redirect;
        }

        $this->branchModel->delete($id);

        return redirect()->to('/branches');
    }
}
