<?php
class Benefit extends AppModel {
    public $name = 'Benefit';
    public $useTable = 'benefits';
    public $primaryKey = 'id';

    public $belongsTo = array(
        'Supplier' => array(
            'className' => 'Supplier',
            'foreignKey' => 'supplier_id'
        ),
        'BenefitType' => array(
            'className' => 'BenefitType',
            'foreignKey' => 'benefit_type_id'
        )
    );

    var $virtualFields = array(
        'complete_name' => "CONCAT(CONCAT(Benefit.code, ' - '), Benefit.name)"
    );

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['Benefit.data_cancel' => '1901-01-01 00:00:00'];
        
        return $queryData;
    }

    public function beforeSave($options = array())
	{
		if (!empty($this->data[$this->alias]['last_fare_update'])) {
			$this->data[$this->alias]['last_fare_update'] = $this->dateFormatBeforeSave($this->data[$this->alias]['last_fare_update']);
		}

		if (!empty($this->data[$this->alias]['unit_price'])) {
			$this->data[$this->alias]['unit_price'] = $this->priceFormatBeforeSave($this->data[$this->alias]['unit_price']);
		}

		return true;
	}

	public function priceFormatBeforeSave($price)
	{
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

	public function afterFind($results, $primary = false)
	{
		foreach ($results as $key => $val) {
			if (isset($val[$this->alias]['unit_price'])) {
				$results[$key][$this->alias]['unit_price_not_formated'] = $results[$key][$this->alias]['unit_price'];
				$results[$key][$this->alias]['unit_price'] = number_format($results[$key][$this->alias]['unit_price'], 2, ',', '.');
			}

			if (isset($val[$this->alias]['last_fare_update'])) {
				$results[$key][$this->alias]['last_fare_update_nao_formatado'] = $val[$this->alias]['last_fare_update'];
				$results[$key][$this->alias]['last_fare_update'] = date("d/m/Y", strtotime($val[$this->alias]['last_fare_update']));
			}
		}

		return $results;
	}
}
