<?php

namespace App\Models;

use CodeIgniter\Model;

class MedicineModel extends Model
{
    protected $table            = 'medicines';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'sku',
        'batch_number',
        'expiry_date',
        'supplier',
        'purchase_price',
        'sale_price',
        'stock_quantity',
        'min_stock_threshold',
        'branch_id'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules      = [
        'name'                => 'required|min_length[3]|max_length[255]',
        'sku'                 => 'required|is_unique[medicines.sku]',
        'batch_number'        => 'permit_empty|max_length[100]',
        'expiry_date'         => 'permit_empty|valid_date',
        'stock_quantity'      => 'required|integer',
        'min_stock_threshold' => 'permit_empty|integer',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;

    /**
     * Get medicines with low stock
     */
    public function getLowStockMedicines($branchId = null)
    {
        $builder = $this->where('stock_quantity <=', 'min_stock_threshold', false);

        if ($branchId) {
            $builder = $builder->where('branch_id', $branchId);
        }

        return $builder->findAll();
    }

    /**
     * Get expired or expiring soon medicines
     */
    public function getExpiringMedicines($days = 30, $branchId = null)
    {
        $date = date('Y-m-d', strtotime("+{$days} days"));

        $builder = $this->where('expiry_date <=', $date);

        if ($branchId) {
            $builder = $builder->where('branch_id', $branchId);
        }

        return $builder->findAll();
    }
}
