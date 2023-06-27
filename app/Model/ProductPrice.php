<?php 
App::uses('AuthComponent', 'Controller/Component');
class ProductPrice extends AppModel {
	public $name = 'ProductPrice';

	public $belongsTo = array(
		'Product',
		'PriceTable'
	);

	public function beforeFind($queryData) {

		$queryData['conditions'][] = array('ProductPrice.data_cancel' => '1901-01-01 00:00:00');
		
		return $queryData;
	}

	public function beforeSave($options = array()) {
		if (!empty($this->data['ProductPrice']['value'])) {
			$this->data['ProductPrice']['value'] = $this->priceFormatBeforeSave($this->data['ProductPrice']['value']);
		}
		
		return true;
	}

	public function priceFormatBeforeSave($price) {
		$valueFormatado = str_replace('.', '', $price);
		$valueFormatado = str_replace(',', '.', $valueFormatado);

		return $valueFormatado;
	}

	public function afterFind($results, $primary = false){
		foreach ($results as $key => $val) {
			if (isset($val['ProductPrice']['value'])) {
				$results[$key]['ProductPrice']['value_nao_formatado'] = $results[$key]['ProductPrice']['value'];
                $results[$key]['ProductPrice']['value'] = number_format($results[$key]['ProductPrice']['value'],2,',','.');
			}
		}

		return $results;
	}

	public $validate = array(
    'price_table_id' => array(
      'required' => array(
        'rule' => array('notEmpty'),
        'message' => 'Campo obrigatório'
      )
    ),
    'value' => array(
      'required' => array(
        'rule' => array('notEmpty'),
        'message' => 'Campo obrigatório'
      )
    )
  );
}