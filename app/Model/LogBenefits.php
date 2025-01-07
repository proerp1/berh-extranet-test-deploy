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

    if (!empty($this->data[$this->alias]['ate'])) {
      $this->data[$this->alias]['ate'] = $this->dateFormatBeforeSave($this->data[$this->alias]['ate']);
    }

    if (!empty($this->data[$this->alias]['de'])) {
      $this->data[$this->alias]['de'] = $this->dateFormatBeforeSave($this->data[$this->alias]['de']);
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
      return $partes[3].'-'.$partes[2].'-'.$partes[1];
    }

    return false;
  }

  public function afterFind($results, $primary = false)
  {
    foreach ($results as $key => $val) {
      if (isset($val[$this->alias]['old_value'])) {
        $results[$key][$this->alias]['old_value_not_formated'] = $results[$key][$this->alias]['old_value'];
        $results[$key][$this->alias]['old_value'] = number_format($results[$key][$this->alias]['old_value'], 2, ',', '.');
      }
      
      if (isset($val[$this->alias]['log_date'])) {
        $results[$key][$this->alias]['log_date_nao_formatado'] = $results[$key][$this->alias]['log_date'];
        $results[$key][$this->alias]['log_date'] = date('d/m/Y H:i:s ', strtotime($results[$key][$this->alias]['log_date']));
      }

      if (isset($val[$this->alias]['de'])) {
        $results[$key][$this->alias]['de_nao_formatado'] = $val[$this->alias]['de'];
        $results[$key][$this->alias]['de'] = date('d/m/Y', strtotime($val[$this->alias]['de']));
      }

      if (isset($val[$this->alias]['ate'])) {
        $results[$key][$this->alias]['ate_nao_formatado'] = $val[$this->alias]['ate'];
        $results[$key][$this->alias]['ate'] = date('d/m/Y', strtotime($val[$this->alias]['ate']));
      }
    }

    return $results;
  }
}