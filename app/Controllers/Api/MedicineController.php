<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\MedicineModel;
use CodeIgniter\HTTP\ResponseInterface;

class MedicineController extends BaseController
{
    protected $medicineModel;

    public function __construct()
    {
        $this->medicineModel = new MedicineModel();
    }

    /**
     * Get all medicines with optional filters
     */
    public function index()
    {
        $search = $this->request->getGet('search');
        $lowStock = $this->request->getGet('low_stock');
        $expiring = $this->request->getGet('expiring');
        $branchId = $this->request->getGet('branch_id');

        if ($lowStock) {
            $medicines = $this->medicineModel->getLowStockMedicines($branchId);
        } elseif ($expiring) {
            $days = $this->request->getGet('days') ?? 30;
            $medicines = $this->medicineModel->getExpiringMedicines($days, $branchId);
        } elseif ($search) {
            $builder = $this->medicineModel
                ->like('name', $search)
                ->orLike('sku', $search)
                ->orLike('batch_number', $search);

            if ($branchId) {
                $builder = $builder->where('branch_id', $branchId);
            }

            $medicines = $builder->findAll();
        } else {
            if ($branchId) {
                $medicines = $this->medicineModel->where('branch_id', $branchId)->findAll();
            } else {
                $medicines = $this->medicineModel->findAll();
            }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $medicines
        ]);
    }

    /**
     * Get single medicine
     */
    public function show($id = null)
    {
        $medicine = $this->medicineModel->find($id);

        if (!$medicine) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Medicine not found'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $medicine
        ]);
    }

    /**
     * Create new medicine
     */
    public function create()
    {
        $data = $this->request->getJSON(true);

        // Allow client to pass branch_id to associate medicine with a branch
        // If not provided, branch_id will remain null (global or unspecified).

        if ($this->medicineModel->insert($data)) {
            return $this->response->setStatusCode(201)->setJSON([
                'status'  => 'success',
                'message' => 'Medicine added successfully',
                'data'    => $this->medicineModel->find($this->medicineModel->getInsertID())
            ]);
        }

        return $this->response->setStatusCode(400)->setJSON([
            'status'  => 'error',
            'message' => 'Failed to add medicine',
            'errors'  => $this->medicineModel->errors()
        ]);
    }

    /**
     * Update medicine
     */
    public function update($id = null)
    {
        $data = $this->request->getJSON(true);

        if (!$this->medicineModel->find($id)) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Medicine not found'
            ]);
        }

        if ($this->medicineModel->update($id, $data)) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Medicine updated successfully',
                'data'    => $this->medicineModel->find($id)
            ]);
        }

        return $this->response->setStatusCode(400)->setJSON([
            'status'  => 'error',
            'message' => 'Failed to update medicine',
            'errors'  => $this->medicineModel->errors()
        ]);
    }

    /**
     * Dispense medicine
     */
    public function dispense($id = null)
    {
        $data = $this->request->getJSON(true);
        $medicine = $this->medicineModel->find($id);

        if (!$medicine) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Medicine not found'
            ]);
        }

        $quantity = $data['quantity'] ?? 0;

        if ($medicine['stock_quantity'] < $quantity) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Insufficient stock'
            ]);
        }

        $newStock = $medicine['stock_quantity'] - $quantity;
        
        if ($this->medicineModel->update($id, ['stock_quantity' => $newStock])) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Medicine dispensed successfully',
                'data'    => [
                    'remaining_stock' => $newStock,
                    'dispensed_quantity' => $quantity
                ]
            ]);
        }

        return $this->response->setStatusCode(400)->setJSON([
            'status'  => 'error',
            'message' => 'Failed to dispense medicine'
        ]);
    }

    /**
     * Delete medicine
     */
    public function delete($id = null)
    {
        if (!$this->medicineModel->find($id)) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Medicine not found'
            ]);
        }

        if ($this->medicineModel->delete($id)) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Medicine deleted successfully'
            ]);
        }

        return $this->response->setStatusCode(400)->setJSON([
            'status'  => 'error',
            'message' => 'Failed to delete medicine'
        ]);
    }

    /**
     * Get inventory alerts
     */
    public function alerts()
    {
        $branchId = $this->request->getGet('branch_id');

        $lowStock = $this->medicineModel->getLowStockMedicines($branchId);
        $expiring = $this->medicineModel->getExpiringMedicines(30, $branchId);

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => [
                'low_stock_count' => count($lowStock),
                'expiring_count'  => count($expiring),
                'low_stock'       => $lowStock,
                'expiring'        => $expiring
            ]
        ]);
    }
}
