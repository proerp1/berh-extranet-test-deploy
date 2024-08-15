<?php
class CustomerUserItinerary extends AppModel
{
    public $name = 'CustomerUserItinerary';
    public $useTable = 'customer_user_itineraries';
    public $primaryKey = 'id';

    public $belongsTo = array(
        'CustomerUser' => [
            'className' => 'CustomerUser',
            'foreignKey' => 'customer_user_id'
        ],
        'Benefit' => [
            'className' => 'Benefit',
            'foreignKey' => 'benefit_id'
        ]
    );

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = array('CustomerUserItinerary.data_cancel' => '1901-01-01 00:00:00');

        return $queryData;
    }

    public function afterFind($results, $primary = false)
    {
        foreach ($results as $key => $val) {
            if (isset($val[$this->alias]['unit_price']) && !isset($val[$this->alias]['unit_price_not_formated'])) {
                $results[$key][$this->alias]['unit_price_not_formated'] = $results[$key][$this->alias]['unit_price'];
                $results[$key][$this->alias]['unit_price'] = number_format($results[$key][$this->alias]['unit_price'], 2, ',', '.');
            }

            if (isset($val[$this->alias]['price_per_day']) && !isset($val[$this->alias]['price_per_day_not_formated'])) {
                $results[$key][$this->alias]['price_per_day_not_formated'] = $results[$key][$this->alias]['price_per_day'];
                $results[$key][$this->alias]['price_per_day'] = number_format($results[$key][$this->alias]['price_per_day'], 2, ',', '.');

                $results[$key][$this->alias]['total_not_formated'] = $results[$key][$this->alias]['price_per_day_not_formated'] * $results[$key][$this->alias]['working_days'];
                $results[$key][$this->alias]['total'] = number_format($results[$key][$this->alias]['total_not_formated'], 2, ',', '.');
            }

            if (!isset($val['Benefit']) && isset($val['CustomerUserItinerary']['benefit_id'])) {
                $benefit = $this->query('select name, code from benefits where id = ' . $val['CustomerUserItinerary']['benefit_id']);
                $results[$key][$this->alias]['benefit_name'] = $benefit[0]['benefits']['name'];
                $results[$key][$this->alias]['benefit_code'] = $benefit[0]['benefits']['code'];
            }
        }

        return $results;
    }

    public function beforeSave($options = array())
    {
        if (!empty($this->data[$this->alias]['price_per_day'])) {
            $this->data[$this->alias]['price_per_day'] = $this->priceFormatBeforeSave($this->data[$this->alias]['price_per_day']);
        }

        if (!empty($this->data[$this->alias]['unit_price'])) {
            $this->data[$this->alias]['unit_price'] = $this->priceFormatBeforeSave($this->data[$this->alias]['unit_price']);

            if (!isset($this->data[$this->alias]['price_per_day'])) {
                $this->data[$this->alias]['price_per_day'] = $this->data[$this->alias]['unit_price'] * $this->data[$this->alias]['quantity'];
            }
        }

        return true;
    }

    public function priceFormatBeforeSave($price)
    {
        $valueFormatado = str_replace('.', '', $price);
        $valueFormatado = str_replace(',', '.', $valueFormatado);

        return $valueFormatado;
    }
}
