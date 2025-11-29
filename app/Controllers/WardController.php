<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\WardModel;
use App\Models\BranchModel;
use App\Services\ValidationService;

/**
 * Ward Management Controller
 * Manages hospital wards and bed allocation
 */
class WardController extends BaseController
{
    protected WardModel $wardModel;
    protected BranchModel $branchModel;
    protected ValidationService $validationService;

    public function __construct()
    {
        $this->wardModel = new WardModel();
        $this->branchModel = new BranchModel();
        $this->validationService = new ValidationService();
    }

    /**
     * Display wards list
     */
    public function index()
    {
        if ($redirect = $this->enforceRoles(['admin', 'receptionist', 'doctor'])) {
            return $redirect;
        }

        $filters = [
            'branch_id' => $this->request->getGet('branch_id'),
            'status' => $this->request->getGet('status'),
            'search' => $this->request->getGet('search')
        ];

        $wards = $this->wardModel->getWardsWithFilters($filters);
        $branches = $this->branchModel->findAll();

        return view('wards/index', [
            'wards' => $wards,
            'branches' => $branches,
            'filters' => $filters
        ]);
    }

    /**
     * Display ward details
     */
    public function show($id)
    {
        if ($redirect = $this->enforceRoles(['admin', 'receptionist', 'doctor'])) {
            return $redirect;
        }

        $ward = $this->wardModel->find($id);
        
        if (!$ward) {
            return redirect()->to('/wards')->with('error', 'Ward not found');
        }

        $beds = $this->wardModel->getWardBeds($id);
        $patients = $this->wardModel->getWardPatients($id);

        return view('wards/show', [
            'ward' => $ward,
            'beds' => $beds,
            'patients' => $patients
        ]);
    }

    /**
     * Display create ward form
     */
    public function create()
    {
        if ($redirect = $this->enforceRoles(['admin'])) {
            return $redirect;
        }

        $branches = $this->branchModel->findAll();

        return view('wards/create', [
            'branches' => $branches
        ]);
    }

    /**
     * Store new ward
     */
    public function store()
    {
        if ($redirect = $this->enforceRoles(['admin'])) {
            return $redirect;
        }

        $data = $this->request->getPost();

        // Validate ward data
        $validation = $this->validationService->validateAndSanitize($data, 'ward');
        
        if (!$validation['success']) {
            return redirect()->back()->withInput()->with('errors', $validation['errors']);
        }

        $sanitizedData = $validation['data'];

        if ($this->wardModel->createWard($sanitizedData)) {
            return redirect()->to('/wards')->with('success', 'Ward created successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create ward');
    }

    /**
     * Display edit ward form
     */
    public function edit($id)
    {
        if ($redirect = $this->enforceRoles(['admin'])) {
            return $redirect;
        }

        $ward = $this->wardModel->find($id);
        
        if (!$ward) {
            return redirect()->to('/wards')->with('error', 'Ward not found');
        }

        $branches = $this->branchModel->findAll();

        return view('wards/edit', [
            'ward' => $ward,
            'branches' => $branches
        ]);
    }

    /**
     * Update ward
     */
    public function update($id)
    {
        if ($redirect = $this->enforceRoles(['admin'])) {
            return $redirect;
        }

        $data = $this->request->getPost();

        $validation = $this->validationService->validateAndSanitize($data, 'ward');
        
        if (!$validation['success']) {
            return redirect()->back()->withInput()->with('errors', $validation['errors']);
        }

        $sanitizedData = $validation['data'];

        if ($this->wardModel->updateWard($id, $sanitizedData)) {
            return redirect()->to('/wards')->with('success', 'Ward updated successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update ward');
    }

    /**
     * Delete ward
     */
    public function delete($id)
    {
        if ($redirect = $this->enforceRoles(['admin'])) {
            return $redirect;
        }

        $ward = $this->wardModel->find($id);
        
        if (!$ward) {
            return redirect()->to('/wards')->with('error', 'Ward not found');
        }

        if ($this->wardModel->hasOccupiedBeds($id)) {
            return redirect()->to('/wards')->with('error', 'Cannot delete ward with occupied beds');
        }

        if ($this->wardModel->delete($id)) {
            return redirect()->to('/wards')->with('success', 'Ward deleted successfully');
        }

        return redirect()->back()->with('error', 'Failed to delete ward');
    }
}
