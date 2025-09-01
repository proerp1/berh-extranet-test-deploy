<?php
class SupplierVolumeTier extends AppModel
{
    public $name = 'SupplierVolumeTier';
    public $displayField = 'id';
    
    public $belongsTo = [
        'Supplier' => [
            'className' => 'Supplier',
            'foreignKey' => 'supplier_id'
        ]
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['SupplierVolumeTier.data_cancel' => '1901-01-01 00:00:00'];
        
        return $queryData;
    }

    public function afterFind($results, $primary = false)
    {
        foreach ($results as $key => $val) {
            if (isset($val[$this->alias]['percentual_repasse'])) {
                $results[$key][$this->alias]['percentual_repasse_nao_formatado'] = $results[$key][$this->alias]['percentual_repasse'];
                $results[$key][$this->alias]['percentual_repasse'] = number_format($results[$key][$this->alias]['percentual_repasse'], 2, ',', '.');
            }
            
            if (isset($val[$this->alias]['valor_fixo'])) {
                $results[$key][$this->alias]['valor_fixo_nao_formatado'] = $results[$key][$this->alias]['valor_fixo'];
                $results[$key][$this->alias]['valor_fixo'] = number_format($results[$key][$this->alias]['valor_fixo'], 2, ',', '.');
            }
        }

        return $results;
    }


    public function beforeSave($options = [])
    {
        if (!empty($this->data[$this->alias]['percentual_repasse'])) {
            $this->data[$this->alias]['percentual_repasse'] = $this->priceFormatBeforeSave($this->data[$this->alias]['percentual_repasse']);
        }
        
        if (!empty($this->data[$this->alias]['valor_fixo'])) {
            $this->data[$this->alias]['valor_fixo'] = $this->priceFormatBeforeSave($this->data[$this->alias]['valor_fixo']);
        }
        
        if (isset($this->data[$this->alias]['de_qtd']) && $this->data[$this->alias]['de_qtd'] !== '') {
            $this->data[$this->alias]['de_qtd'] = intval($this->data[$this->alias]['de_qtd']);
        }
        if (isset($this->data[$this->alias]['ate_qtd']) && $this->data[$this->alias]['ate_qtd'] !== '') {
            $this->data[$this->alias]['ate_qtd'] = intval($this->data[$this->alias]['ate_qtd']);
        }
        
        return true;
    }

    public function priceFormatBeforeSave($price)
    {
        // If already numeric, return as is
        if(is_numeric($price)){
            return floatval($price);
        }
        
        // Convert to string and trim whitespace
        $price = trim(strval($price));
        
        // Handle Brazilian format: 1.234,56 -> 1234.56
        if (strpos($price, ',') !== false) {
            // If there's both dot and comma, remove dots (thousands separator) and replace comma with dot
            if (strpos($price, '.') !== false && strpos($price, ',') !== false) {
                $valueFormatado = str_replace('.', '', $price);
                $valueFormatado = str_replace(',', '.', $valueFormatado);
            } else {
                // If there's only comma, replace it with dot
                $valueFormatado = str_replace(',', '.', $price);
            }
        } else {
            // If no comma, assume it's already in correct format
            $valueFormatado = $price;
        }

        return floatval($valueFormatado);
    }

    public $validate = [
        'supplier_id' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Fornecedor é obrigatório'
            ]
        ],
        'de_qtd' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Quantidade inicial é obrigatória'
            ],
            'numeric' => [
                'rule' => ['numeric'],
                'message' => 'Quantidade inicial deve ser um número'
            ],
            'range' => [
                'rule' => ['comparison', '>=', 1],
                'message' => 'Quantidade inicial deve ser maior ou igual a 1'
            ],
            'maxValue' => [
                'rule' => ['comparison', '<=', 999999],
                'message' => 'Quantidade inicial deve ser menor ou igual a 999.999'
            ]
        ],
        'ate_qtd' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Quantidade final é obrigatória'
            ],
            'numeric' => [
                'rule' => ['numeric'],
                'message' => 'Quantidade final deve ser um número'
            ],
            'range' => [
                'rule' => ['comparison', '>=', 1],
                'message' => 'Quantidade final deve ser maior ou igual a 1'
            ],
            'maxValue' => [
                'rule' => ['comparison', '<=', 999999],
                'message' => 'Quantidade final deve ser menor ou igual a 999.999'
            ],
            'greaterThanDeQtd' => [
                'rule' => ['greaterThanDeQtd'],
                'message' => 'Quantidade final deve ser maior que a quantidade inicial'
            ]
        ],
        'percentual_repasse' => [
            'decimal' => [
                'rule' => ['decimal'],
                'message' => 'Percentual de repasse deve ser um número decimal',
                'allowEmpty' => true
            ],
            'range' => [
                'rule' => ['comparison', '>=', 0.01],
                'message' => 'Percentual de repasse deve ser maior ou igual a 0,01%',
                'allowEmpty' => true
            ],
            'maxRange' => [
                'rule' => ['comparison', '<=', 100.00],
                'message' => 'Percentual de repasse deve ser menor ou igual a 100,00%',
                'allowEmpty' => true
            ]
        ],
        'valor_fixo' => [
            'decimal' => [
                'rule' => ['decimal'],
                'message' => 'Valor fixo deve ser um número decimal',
                'allowEmpty' => true
            ],
            'range' => [
                'rule' => ['comparison', '>=', 0.01],
                'message' => 'Valor fixo deve ser maior ou igual a 0,01',
                'allowEmpty' => true
            ]
        ]
    ];

    public function greaterThanDeQtd($check)
    {
        $ate_qtd = array_values($check)[0];
        $de_qtd = $this->data[$this->alias]['de_qtd'];
        
        return $ate_qtd > $de_qtd;
    }


    /**
     * Valida se não há sobreposição de faixas para o mesmo fornecedor
     */
    public function validateNoOverlap($supplierId, $deQtd, $ateQtd, $excludeId = null)
    {
        $conditions = [
            'SupplierVolumeTier.supplier_id' => $supplierId,
            'SupplierVolumeTier.data_cancel' => '1901-01-01 00:00:00',
            'OR' => [
                // Nova faixa começa dentro de uma faixa existente
                [
                    'SupplierVolumeTier.de_qtd <=' => $deQtd,
                    'SupplierVolumeTier.ate_qtd >=' => $deQtd
                ],
                // Nova faixa termina dentro de uma faixa existente
                [
                    'SupplierVolumeTier.de_qtd <=' => $ateQtd,
                    'SupplierVolumeTier.ate_qtd >=' => $ateQtd
                ],
                // Nova faixa engloba uma faixa existente
                [
                    'SupplierVolumeTier.de_qtd >=' => $deQtd,
                    'SupplierVolumeTier.ate_qtd <=' => $ateQtd
                ]
            ]
        ];

        if ($excludeId) {
            $conditions['SupplierVolumeTier.id !='] = $excludeId;
        }

        $overlapping = $this->find('count', [
            'conditions' => $conditions
        ]);

        return $overlapping == 0;
    }

    /**
     * Busca a faixa de volume aplicável para uma quantidade específica
     */
    public function findTierForQuantity($supplierId, $quantity)
    {
        return $this->find('first', [
            'conditions' => [
                'SupplierVolumeTier.supplier_id' => $supplierId,
                'SupplierVolumeTier.de_qtd <=' => $quantity,
                'SupplierVolumeTier.ate_qtd >=' => $quantity,
                'SupplierVolumeTier.data_cancel' => '1901-01-01 00:00:00'
            ],
            'order' => ['SupplierVolumeTier.de_qtd' => 'ASC']
        ]);
    }
}