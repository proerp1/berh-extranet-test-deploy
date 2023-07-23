<?php
class Order extends AppModel {
    public $name = 'Order';
    public $useTable = 'orders';
    public $primaryKey = 'id';

    public $belongsTo = array(
        'Customer' => array(
            'className' => 'Customer',
            'foreignKey' => 'customer_id'
        ),
		'Creator' => array(
			'className' => 'User',
			'foreignKey' => 'user_creator_id'
		),
    );
    public $hasMany = array(
        'OrderItem' => array(
            'className' => 'OrderItem',
            'foreignKey' => 'order_id'
        )
    );

    public function afterFind($results, $primary = false)
    {
        foreach ($results as $key => $val) {
            if (isset($val[$this->alias]['transfer_fee'])) {
                $results[$key][$this->alias]['transfer_fee_not_formated'] = $results[$key][$this->alias]['transfer_fee'];
                $results[$key][$this->alias]['transfer_fee'] = number_format($results[$key][$this->alias]['transfer_fee'], 2, ',', '.');
            }

            if (isset($val[$this->alias]['commission_fee'])) {
                $results[$key][$this->alias]['commission_fee_not_formated'] = $results[$key][$this->alias]['commission_fee'];
                $results[$key][$this->alias]['commission_fee'] = number_format($results[$key][$this->alias]['commission_fee'], 2, ',', '.');
            }

            if (isset($val[$this->alias]['subtotal'])) {
                $results[$key][$this->alias]['subtotal_not_formated'] = $results[$key][$this->alias]['subtotal'];
                $results[$key][$this->alias]['subtotal'] = number_format($results[$key][$this->alias]['subtotal'], 2, ',', '.');
            }

            if (isset($val[$this->alias]['total'])) {
                $results[$key][$this->alias]['total_not_formated'] = $results[$key][$this->alias]['total'];
                $results[$key][$this->alias]['total'] = number_format($results[$key][$this->alias]['total'], 2, ',', '.');
            }

            if (isset($val[$this->alias]['order_period'])) {
				$results[$key][$this->alias]['order_period_nao_formatado'] = $val[$this->alias]['order_period'];
				$results[$key][$this->alias]['order_period'] = date("m/Y", strtotime($val[$this->alias]['order_period']));
			}
        }

        return $results;
    }

    public function beforeSave($options = array()) {
		if (!empty($this->data[$this->alias]['transfer_fee'])) {
			$this->data[$this->alias]['transfer_fee'] = $this->priceFormatBeforeSave($this->data[$this->alias]['transfer_fee']);
		}

		if (!empty($this->data[$this->alias]['commission_fee'])) {
			$this->data[$this->alias]['commission_fee'] = $this->priceFormatBeforeSave($this->data[$this->alias]['commission_fee']);
		}

        if (!empty($this->data[$this->alias]['subtotal'])) {
			$this->data[$this->alias]['subtotal'] = $this->priceFormatBeforeSave($this->data[$this->alias]['subtotal']);
		}

        if (!empty($this->data[$this->alias]['total'])) {
			$this->data[$this->alias]['total'] = $this->priceFormatBeforeSave($this->data[$this->alias]['total']);
		}

        if (!empty($this->data[$this->alias]['order_period'])) {
			$this->data[$this->alias]['order_period'] = $this->dateFormatBeforeSave($this->data[$this->alias]['order_period']);
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
		return date('Y-m-d', strtotime($this->date_converter($dateString)));
	}

	public function date_converter($_date = null)
	{
		$format = '/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/';
		if ($_date != null && preg_match($format, $_date, $partes)) {
			return $partes[3] . '-' . $partes[2] . '-' . $partes[1];
		}

		return false;
	}
}