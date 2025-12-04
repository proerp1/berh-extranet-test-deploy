<?php

class SupplierParamsGe extends AppModel
{
    public $name = 'SupplierParamsGe';
    public $useTable = 'supplier_params_ge';

    public $belongsTo = [
        'Status' => [
            'className' => 'Status',
            'foreignKey' => 'status_id',
            'conditions' => ['Status.categoria' => 1],
        ],
        'Customer',
        'Supplier',
    ];

    public $actsAs = ['Containable'];

    public $validate = [
        'status_id' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
        ],
        'supplier_id' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
        ],
        'tickets' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
        ],
        'minimum_purchase' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
        ],
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['SupplierParamsGe.data_cancel' => '1901-01-01 00:00:00'];

        return $queryData;
    }

    public function beforeSave($options = [])
    {
        if (!empty($this->data[$this->alias]['minimum_purchase'])) {
            $this->data[$this->alias]['minimum_purchase'] = $this->priceFormatBeforeSave($this->data[$this->alias]['minimum_purchase']);
        }

        return true;
    }

    public function afterFind($results, $primary = false)
    {
        foreach ($results as $key => $val) {
            if (isset($val[$this->alias]['minimum_purchase'])) {
                $results[$key][$this->alias]['minimum_purchase_nao_formatado'] = $results[$key][$this->alias]['minimum_purchase'];
                $results[$key][$this->alias]['minimum_purchase'] = number_format($results[$key][$this->alias]['minimum_purchase'], 2, ',', '.');
            }
        }

        return $results;
    }
}
