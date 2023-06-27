<?php
class PefinMaintenance extends AppModel
{
    public $name = 'PefinMaintenance';

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['PefinMaintenance.data_cancel' => '1901-01-01 00:00:00'];
        
        return $queryData;
    }

    public function afterFind($results, $primary = false)
    {
        foreach ($results as $key => $val) {
            if (isset($val['PefinMaintenance']['value'])) {
                $results[$key]['PefinMaintenance']['value_nao_formatado'] = $results[$key]['PefinMaintenance']['value'];
                $results[$key]['PefinMaintenance']['value'] = number_format($results[$key]['PefinMaintenance']['value'], 2, ',', '.');
            }
        }

        return $results;
    }

  public function beforeSave($options = [])
  {
      if (!empty($this->data['PefinMaintenance']['value'])) {
          $this->data['PefinMaintenance']['value'] = $this->priceFormatBeforeSave($this->data['PefinMaintenance']['value']);
      }
        
      return true;
  }

    public function priceFormatBeforeSave($price)
    {
        $valueFormatado = str_replace('.', '', $price);
        $valueFormatado = str_replace(',', '.', $valueFormatado);

        return $valueFormatado;
    }

    public function formatValue($value)
    {
        $value = str_replace(".", "", $value);
        $value = str_replace(",", ".", $value);

        return $value;
    }
}
