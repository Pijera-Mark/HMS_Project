<?php

namespace App\Controllers;

use App\Models\MedicineModel;

class MedicineController extends BaseController
{
    protected MedicineModel $medicineModel;

    public function __construct()
    {
        $this->medicineModel = new MedicineModel();
    }

    public function index()
    {
        if ($redirect = $this->enforceRoles(['admin', 'pharmacist'])) {
            return $redirect;
        }

        $user = session()->get('user');
        $branchId = $user['branch_id'] ?? null;
        $isGlobal = $user && (empty($branchId) || $user['role'] === 'admin');

        $search = $this->request->getGet('search');

        $builder = $this->medicineModel;

        if (! $isGlobal && $branchId) {
            $builder = $builder->where('branch_id', $branchId);
        }

        if ($search) {
            $builder = $builder
                ->groupStart()
                ->like('name', $search)
                ->orLike('sku', $search)
                ->orLike('batch_number', $search)
                ->groupEnd();
        }

        $medicines = $builder->orderBy('name', 'ASC')->findAll();

        return view('medicines/index', [
            'medicines' => $medicines,
            'search'    => $search,
        ]);
    }

    public function new()
    {
        if ($redirect = $this->enforceRoles(['admin', 'pharmacist'])) {
            return $redirect;
        }

        return view('medicines/new');
    }

    public function create()
    {
        if ($redirect = $this->enforceRoles(['admin', 'pharmacist'])) {
            return $redirect;
        }

        $data = $this->request->getPost();

        $user = session()->get('user');
        if ($user && ! empty($user['branch_id'])) {
            $data['branch_id'] = $user['branch_id'];
        }

        if (! $this->medicineModel->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->medicineModel->errors());
        }

        return redirect()->to('/medicines');
    }

    public function edit($id = null)
    {
        if ($redirect = $this->enforceRoles(['admin', 'pharmacist'])) {
            return $redirect;
        }

        $medicine = $this->medicineModel->find($id);

        if (! $medicine) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Medicine not found');
        }

        return view('medicines/edit', [
            'medicine' => $medicine,
        ]);
    }

    public function update($id = null)
    {
        if ($redirect = $this->enforceRoles(['admin', 'pharmacist'])) {
            return $redirect;
        }

        $data = $this->request->getPost();

        if (! $this->medicineModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('errors', $this->medicineModel->errors());
        }

        return redirect()->to('/medicines');
    }

    public function delete($id = null)
    {
        if ($redirect = $this->enforceRoles(['admin', 'pharmacist'])) {
            return $redirect;
        }

        $this->medicineModel->delete($id);

        return redirect()->to('/medicines');
    }
}
