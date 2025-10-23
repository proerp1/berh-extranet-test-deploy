<?php
App::uses('AppModel', 'Model');

class OrderDiscountBatch extends AppModel {
    public $name = 'OrderDiscountBatch';
    public $displayField = 'id';

    public $belongsTo = [
        'Order' => [
            'className' => 'Order',
            'foreignKey' => 'order_id'
        ],
        'UserCreator' => [
            'className' => 'User',
            'foreignKey' => 'user_creator_id'
        ],
        'UserUpdated' => [
            'className' => 'User',
            'foreignKey' => 'user_updated_id'
        ],
        'UserCancel' => [
            'className' => 'User',
            'foreignKey' => 'usuario_id_cancel'
        ]
    ];

    public $hasMany = [
        'OrderDiscountBatchItem' => [
            'className' => 'OrderDiscountBatchItem',
            'foreignKey' => 'batch_id',
            'dependent' => true
        ]
    ];

    public function beforeFind($queryData) 
    {
        if (!isset($queryData['conditions']['OrderDiscountBatch.data_cancel'])) {
            $queryData['conditions']['OrderDiscountBatch.data_cancel'] = '1901-01-01 00:00:00';
        }
        
        return $queryData;
    }

    public function afterFind($results, $primary = false)
    {
        foreach ($results as $key => $val) {
            if (isset($val[$this->alias]['valor_total'])) {
                $results[$key][$this->alias]['valor_total_not_formated'] = $results[$key][$this->alias]['valor_total'];
                $results[$key][$this->alias]['valor_total'] = number_format($results[$key][$this->alias]['valor_total'], 2, ',', '.');
            }

            if (isset($val[$this->alias]['created'])) {
                $results[$key][$this->alias]['created_nao_formatado'] = $val[$this->alias]['created'];
                $results[$key][$this->alias]['created'] = date("d/m/Y", strtotime($val[$this->alias]['created']));
            }
        }

        return $results;
    }

    public function beforeSave($options = array()) 
    {
		if (!empty($this->data[$this->alias]['valor_total'])) {
			$this->data[$this->alias]['valor_total'] = $this->priceFormatBeforeSave($this->data[$this->alias]['valor_total']);
		}
		
		return true;
	}

    public function priceFormatBeforeSave($price)
	{
        if(is_numeric($price)){
            return $price;
        }

		$valueFormatado = str_replace('.', '', $price);
		$valueFormatado = str_replace(',', '.', $valueFormatado);

		return $valueFormatado;
	}

    public function dateFormatBeforeSave($dateString)
    {
        $date = DateTime::createFromFormat('d/m/Y', $dateString);

        if ($date === false) {
            $date = new DateTime($dateString);
        }

        # Check if it contains time
        if (strpos($dateString, ':') !== false) {
            return $date->format('Y-m-d H:i:s');
        }

        return $date->format('Y-m-d');
    }
}