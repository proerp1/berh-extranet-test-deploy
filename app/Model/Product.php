<?php
class Product extends AppModel
{
    public $name = 'Product';

    public $hasMany = [
        'Answer' => [
            'className' => 'Answer',
            'foreignKey' => 'product_id',
            'conditions' => ['Answer.data_cancel' => '1901-01-01 00:00:00']
        ],
        'Feature' => [
            'className' => 'Feature',
            'foreignKey' => 'product_id',
            'conditions' => ['Feature.data_cancel' => '1901-01-01 00:00:00']
        ],
        'ProductPrice' => [
            'conditions' => ['ProductPrice.data_cancel' => '1901-01-01 00:00:00']
        ],
        'ProductFeature' => [
            'conditions' => ['ProductFeature.data_cancel' => '1901-01-01 00:00:00']
        ]
    ];

    public $belongsTo = [
        'Status' => [
            'className' => 'Status',
            'foreignKey' => 'status_id',
            'conditions' => ['Status.categoria' => 1]
        ]
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['Product.data_cancel' => '1901-01-01 00:00:00'];
        
        return $queryData;
    }

    public function beforeSave($options = [])
    {
        if (!empty($this->data['Product']['valor'])) {
            $this->data['Product']['valor'] = $this->priceFormatBeforeSave($this->data['Product']['valor']);
        }
        if (!empty($this->data['Product']['valor_minimo'])) {
            $this->data['Product']['valor_minimo'] = $this->priceFormatBeforeSave($this->data['Product']['valor_minimo']);
        }
        
        return true;
    }

    public function priceFormatBeforeSave($price)
    {
        $valueFormatado = str_replace('.', '', $price);
        $valueFormatado = str_replace(',', '.', $valueFormatado);

        return $valueFormatado;
    }

    public function afterFind($results, $primary = false)
    {
        foreach ($results as $key => $val) {
            if (isset($val['Product']['valor'])) {
                $results[$key]['Product']['valor'] = number_format($results[$key]['Product']['valor'], 2, ',', '.');
            }
            if (isset($val['Product']['valor_minimo'])) {
                $results[$key]['Product']['valor_minimo'] = number_format($results[$key]['Product']['valor_minimo'], 2, ',', '.');
            }
        }

        return $results;
    }
}
