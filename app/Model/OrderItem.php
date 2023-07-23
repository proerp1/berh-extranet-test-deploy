<?php
class OrderItem extends AppModel {
    public $name = 'OrderItem';
    public $useTable = 'order_items';
    public $primaryKey = 'id';

    public $belongsTo = array(
        'Order' => array(
            'className' => 'Order',
            'foreignKey' => 'order_id'
        ),
        'CustomerUserItinerary' => array(
            'className' => 'CustomerUserItinerary',
            'foreignKey' => 'customer_user_itinerary_id'
        ),
        'CustomerUser' => array(
            'className' => 'CustomerUser',
            'foreignKey' => 'customer_user_id'
        )
    );

    public function afterFind($results, $primary = false)
    {
        foreach ($results as $key => $val) {
            if (isset($val[$this->alias]['transfer_fee'])) {
                $results[$key][$this->alias]['transfer_fee_not_formated'] = $results[$key][$this->alias]['transfer_fee'];
                $results[$key][$this->alias]['transfer_fee'] = number_format($results[$key][$this->alias]['transfer_fee'], 2, ',', '.');
            }

            if (isset($val[$this->alias]['subtotal'])) {
                $results[$key][$this->alias]['subtotal_not_formated'] = $results[$key][$this->alias]['subtotal'];
                $results[$key][$this->alias]['subtotal'] = number_format($results[$key][$this->alias]['subtotal'], 2, ',', '.');
            }

            if (isset($val[$this->alias]['total'])) {
                $results[$key][$this->alias]['total_not_formated'] = $results[$key][$this->alias]['total'];
                $results[$key][$this->alias]['total'] = number_format($results[$key][$this->alias]['total'], 2, ',', '.');
            }
        }

        return $results;
    }

    public function beforeSave($options = array()) {
		if (!empty($this->data[$this->alias]['transfer_fee'])) {
			$this->data[$this->alias]['transfer_fee'] = $this->priceFormatBeforeSave($this->data[$this->alias]['transfer_fee']);
		}

        if (!empty($this->data[$this->alias]['subtotal'])) {
			$this->data[$this->alias]['subtotal'] = $this->priceFormatBeforeSave($this->data[$this->alias]['subtotal']);
		}

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