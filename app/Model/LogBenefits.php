<?php 
App::uses('AuthComponent', 'Controller/Component');
class LogBenefits extends AppModel {
  public $name = 'LogBenefits';

  public $belongsTo = array(
    'User' => array(
        'className' => 'User',
        'foreignKey' => 'user_id'
    )
  );

  public function beforeSave($options = array())
  {
    if (!empty($this->data[$this->alias]['old_value'])) {
      $this->data[$this->alias]['old_value'] = $this->priceFormatBeforeSave($this->data[$this->alias]['old_value']);
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
      if (isset($val[$this->alias]['old_value'])) {
        $results[$key][$this->alias]['old_value_not_formated'] = $results[$key][$this->alias]['old_value'];
        $results[$key][$this->alias]['old_value'] = number_format($results[$key][$this->alias]['old_value'], 2, ',', '.');
      }
    }

    foreach ($results as $key => $val) {
      if (isset($val[$this->alias]['log_date'])) {
        $results[$key][$this->alias]['log_date_nao_formatado'] = $results[$key][$this->alias]['log_date'];
        $results[$key][$this->alias]['log_date'] = date('d/m/Y H:i:s ', strtotime($results[$key][$this->alias]['log_date']));
      }
    }

    return $results;
  }
}