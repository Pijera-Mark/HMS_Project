<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class ApiController extends ResourceController
{
    use ResponseTrait;

    protected $format = 'json';
    protected $modelName;

    public function __construct()
    {
        $this->modelName = $this->getModelName();
    }

    /**
     * Get model name based on controller
     */
    protected function getModelName(): ?string
    {
        $controllerName = strtolower(str_replace('Controller', '', class_basename($this)));
        
        $modelMap = [
            'patients' => 'PatientModel',
            'doctors' => 'DoctorModel',
            'appointments' => 'AppointmentModel',
            'medicines' => 'MedicineModel',
            'admissions' => 'AdmissionModel',
            'invoices' => 'InvoiceModel',
            'users' => 'UserModel'
        ];

        return $modelMap[$controllerName] ?? null;
    }

    /**
     * Get all records with filtering and pagination
     */
    public function index()
    {
        if (!$this->checkPermission('view_' . strtolower(str_replace('Model', '', $this->modelName)))) {
            return $this->failUnauthorized('You do not have permission to view this resource');
        }

        $model = new $this->modelName();
        $page = $this->request->getVar('page') ?? 1;
        $limit = $this->request->getVar('limit') ?? 20;
        $search = $this->request->getVar('search');
        $branchId = $this->request->getVar('branch_id');

        $builder = $model->builder();

        // Apply branch filter if not admin
        $user = session()->get('user');
        if ($user && $user['role'] !== 'admin' && $branchId) {
            $builder->where('branch_id', $branchId);
        }

        // Apply search filter
        if ($search) {
            $builder->groupStart()
                    ->like('name', $search)
                    ->orLike('email', $search)
                    ->orLike('phone', $search)
                    ->groupEnd();
        }

        $total = $builder->countAllResults(false);
        $records = $builder->limit($limit, ($page - 1) * $limit)->get()->getResultArray();

        $data = [
            'records' => $records,
            'pagination' => [
                'page' => (int)$page,
                'limit' => (int)$limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
        ];

        return $this->respond($data);
    }

    /**
     * Get single record
     */
    public function show($id = null)
    {
        if (!$this->checkPermission('view_' . strtolower(str_replace('Model', '', $this->modelName)))) {
            return $this->failUnauthorized('You do not have permission to view this resource');
        }

        $model = new $this->modelName();
        $record = $model->find($id);

        if (!$record) {
            return $this->failNotFound('Record not found');
        }

        // Check branch access for non-admin users
        $user = session()->get('user');
        if ($user && $user['role'] !== 'admin' && isset($record['branch_id'])) {
            if ($record['branch_id'] !== $user['branch_id']) {
                return $this->failForbidden('Access denied to this record');
            }
        }

        return $this->respond($record);
    }

    /**
     * Create new record
     */
    public function create()
    {
        if (!$this->checkPermission('manage_' . strtolower(str_replace('Model', '', $this->modelName)))) {
            return $this->failUnauthorized('You do not have permission to create this resource');
        }

        $data = $this->request->getJSON(true);
        
        if (!$data) {
            return $this->fail('Invalid JSON data');
        }

        // Sanitize input
        $data = sanitize_input($data);

        // Add branch_id for non-admin users
        $user = session()->get('user');
        if ($user && $user['role'] !== 'admin' && !isset($data['branch_id'])) {
            $data['branch_id'] = $user['branch_id'];
        }

        $model = new $this->modelName();

        if (!$model->validate($data)) {
            return $this->fail($model->errors());
        }

        try {
            $id = $model->insert($data);
            
            if (!$id) {
                return $this->fail('Failed to create record');
            }

            $record = $model->find($id);
            log_activity('created_' . strtolower(str_replace('Model', '', $this->modelName)), ['id' => $id]);

            return $this->respondCreated([
                'message' => 'Record created successfully',
                'data' => $record
            ]);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * Update record
     */
    public function update($id = null)
    {
        if (!$this->checkPermission('manage_' . strtolower(str_replace('Model', '', $this->modelName)))) {
            return $this->failUnauthorized('You do not have permission to update this resource');
        }

        $model = new $this->modelName();
        $record = $model->find($id);

        if (!$record) {
            return $this->failNotFound('Record not found');
        }

        // Check branch access for non-admin users
        $user = session()->get('user');
        if ($user && $user['role'] !== 'admin' && isset($record['branch_id'])) {
            if ($record['branch_id'] !== $user['branch_id']) {
                return $this->failForbidden('Access denied to this record');
            }
        }

        $data = $this->request->getJSON(true);
        
        if (!$data) {
            return $this->fail('Invalid JSON data');
        }

        // Sanitize input
        $data = sanitize_input($data);

        // Prevent changing branch_id for non-admin users
        if ($user && $user['role'] !== 'admin') {
            unset($data['branch_id']);
        }

        if (!$model->validate($data)) {
            return $this->fail($model->errors());
        }

        try {
            if (!$model->update($id, $data)) {
                return $this->fail('Failed to update record');
            }

            $updatedRecord = $model->find($id);
            log_activity('updated_' . strtolower(str_replace('Model', '', $this->modelName)), ['id' => $id]);

            return $this->respond([
                'message' => 'Record updated successfully',
                'data' => $updatedRecord
            ]);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * Delete record
     */
    public function delete($id = null)
    {
        if (!$this->checkPermission('manage_' . strtolower(str_replace('Model', '', $this->modelName)))) {
            return $this->failUnauthorized('You do not have permission to delete this resource');
        }

        $model = new $this->modelName();
        $record = $model->find($id);

        if (!$record) {
            return $this->failNotFound('Record not found');
        }

        // Check branch access for non-admin users
        $user = session()->get('user');
        if ($user && $user['role'] !== 'admin' && isset($record['branch_id'])) {
            if ($record['branch_id'] !== $user['branch_id']) {
                return $this->failForbidden('Access denied to this record');
            }
        }

        try {
            if (!$model->delete($id)) {
                return $this->fail('Failed to delete record');
            }

            log_activity('deleted_' . strtolower(str_replace('Model', '', $this->modelName)), ['id' => $id]);

            return $this->respondDeleted([
                'message' => 'Record deleted successfully'
            ]);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * Check user permission
     */
    protected function checkPermission(string $permission): bool
    {
        $user = session()->get('user');
        if (!$user) {
            return false;
        }

        $userModel = new \App\Models\UserModel();
        return $userModel->hasPermission($user['id'], $permission);
    }

    /**
     * Get statistics
     */
    public function stats()
    {
        if (!$this->checkPermission('view_dashboard')) {
            return $this->failUnauthorized('You do not have permission to view statistics');
        }

        $model = new $this->modelName();
        $user = session()->get('user');
        $branchId = ($user && $user['role'] !== 'admin') ? $user['branch_id'] : null;

        $builder = $model->builder();
        
        if ($branchId) {
            $builder->where('branch_id', $branchId);
        }

        $total = $builder->countAllResults();
        
        // Get recent activity (last 7 days)
        $recentBuilder = $model->builder();
        if ($branchId) {
            $recentBuilder->where('branch_id', $branchId);
        }
        $recentBuilder->where('created_at >=', date('Y-m-d', strtotime('-7 days')));
        $recent = $recentBuilder->countAllResults();

        return $this->respond([
            'total' => $total,
            'recent' => $recent,
            'growth_rate' => $total > 0 ? (($recent / $total) * 100) : 0
        ]);
    }
}
