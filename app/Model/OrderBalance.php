<?php
class OrderBalance extends AppModel {
    public $name = 'OrderBalance';

    public $belongsTo = array(
        'Order' => array(
            'className' => 'Order',
            'foreignKey' => 'order_id'
        ),
        'CustomerUser' => array(
            'className' => 'CustomerUser',
            'foreignKey' => 'customer_user_id'
        ),
        'Benefit' => array(
            'className' => 'Benefit',
            'foreignKey' => 'benefit_id'
        ),
    );

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['OrderBalance.data_cancel' => '1901-01-01 00:00:00'];
        
        return $queryData;
    }

    public function afterFind($results, $primary = false)
    {
        foreach ($results as $key => $val) {
            if (isset($val[$this->alias]['total'])) {
                $results[$key][$this->alias]['total_not_formated'] = $results[$key][$this->alias]['total'];
                $results[$key][$this->alias]['total'] = number_format($results[$key][$this->alias]['total'], 2, ',', '.');
            } else {
                $results[$key][$this->alias]['total_not_formated'] = 0;
                $results[$key][$this->alias]['total'] = '0,00';
            }
        }

        return $results;
    }

    public function beforeSave($options = array()) 
    {
        if (!empty($this->data[$this->alias]['total'])) {
            $this->data[$this->alias]['total'] = $this->priceFormatBeforeSave($this->data[$this->alias]['total']);
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
}
