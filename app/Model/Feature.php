<?php 
App::uses('AuthComponent', 'Controller/Component');
class Feature extends AppModel {
  public $name = 'Feature';

  public $belongsTo = array(
    'Product' => array(
      'className' => 'Product',
      'foreignKey' => 'product_id',
      'conditions' => array('Product.data_cancel' => '1901-01-01 00:00:00', 'Product.status_id' => 1)
    ),
    'Status' => array(
      'className' => 'Status',
      'foreignKey' => 'status_id',
      'conditions' => array('Status.categoria' => 1)
    )
  );

  public function afterFind($results, $primary = false){
    foreach ($results as $key => $val) {
      if (isset($val['Feature']['data_ativacao'])) {
        $results[$key]['Feature']['data_ativacao'] = date("d/m/Y", strtotime($val['Feature']['data_ativacao']));
      }
      if (isset($val['Feature']['valor'])) {
        $results[$key]['Feature']['valor_nao_formatado'] = $results[$key]['Feature']['valor'];
        $results[$key]['Feature']['valor'] = number_format($results[$key]['Feature']['valor'],2,',','.');
      }
      if (isset($val['Feature']['valor_minimo'])) {
        $results[$key]['Feature']['valor_minimo_nao_formatado'] = $results[$key]['Feature']['valor_minimo'];
        $results[$key]['Feature']['valor_minimo'] = number_format($results[$key]['Feature']['valor_minimo'],2,',','.');
      }
    }

    return $results;
  }

  public function beforeSave($options = array()) {
    if (!empty($this->data['Feature']['data_ativacao'])) {
      $this->data['Feature']['data_ativacao'] = $this->dateFormatBeforeSave($this->data['Feature']['data_ativacao']);
    }
    if (!empty($this->data['Feature']['valor'])) {
      $this->data['Feature']['valor'] = $this->priceFormatBeforeSave($this->data['Feature']['valor']);
    }
    if (!empty($this->data['Feature']['valor_minimo'])) {
      $this->data['Feature']['valor_minimo'] = $this->priceFormatBeforeSave($this->data['Feature']['valor_minimo']);
    } else {
      $this->data['Feature']['valor_minimo'] = 0;
    }

    return true;
  }

  public function dateFormatBeforeSave($dateString) {
    return date('Y-m-d', strtotime($this->date_converter($dateString)));
  }

  public function date_converter($_date = null) {
    $format = '/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/';
    if ($_date != null && preg_match($format, $_date, $partes)) {
      return $partes[3].'-'.$partes[2].'-'.$partes[1];
    }
    
    return false;
  }

  public function priceFormatBeforeSave($price) {
    $valueFormatado = str_replace('.', '', $price);
    $valueFormatado = str_replace(',', '.', $valueFormatado);

    return $valueFormatado;
  }

  public function beforeFind($queryData) {

    $queryData['conditions'][] = array('Feature.data_cancel' => '1901-01-01 00:00:00');
    
    return $queryData;
  }

  public function find_features_package($package_id) {
    $result = $this->query("SELECT f.id, f.name, TRIM(f.descricao) AS descricao, p.price AS valor, f.tipo_consulta
                            FROM features f
                              INNER JOIN package_features p ON p.feature_id = f.id
                            WHERE f.data_cancel = '1901-01-01' AND p.data_cancel = '1901-01-01' AND p.package_id = ".$package_id);

    return $result;
  }
}