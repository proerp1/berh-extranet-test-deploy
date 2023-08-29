<?php

class Proposal extends AppModel
{
    public $name = 'Proposal';

    public $belongsTo = [
        'Customer',
    ];

    public $validate = [
        'date' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
            'date_format' => [
                'rule' => ['date', 'dmy'],
                'message' => 'Digite uma data no formato DD/MM/YYYY.',
            ],
        ],
        'expected_closing_date' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
                'last' => false,
            ],
            'date_format' => [
                'rule' => ['date', 'dmy'],
                'message' => 'Digite uma data no formato DD/MM/YYYY.',
            ],
        ],
        'closing_date' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
            'date_format' => [
                'rule' => ['date', 'dmy'],
                'message' => 'Digite uma data no formato DD/MM/YYYY.',
            ],
        ],
        'workers_qty' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
        ],
        'workers_price' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
        ],
        'workers_price_total' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
        ],
        'transport_adm_fee' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
        ],
        'transport_deli_fee' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
        ],
        'management_feel' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
        ],
        'meal_adm_fee' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
        ],
        'meal_deli_fee' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
        ],
        'fuel_adm_fee' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
        ],
        'fuel_deli_fee' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
        ],
        'multi_card_adm_fee' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
        ],
        'multi_card_deli_fee' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
        ],
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['Proposal.data_cancel' => '1901-01-01 00:00:00'];

        return $queryData;
    }

    public function beforeSave($options = [])
    {
        $this->formatDateFieldBeforeSave('date');
        $this->formatDateFieldBeforeSave('expected_closing_date');
        $this->formatDateFieldBeforeSave('closing_date');

        $this->formatPriceFieldBeforeSave('workers_price');
        $this->formatPriceFieldBeforeSave('workers_price_total');
        $this->formatPriceFieldBeforeSave('transport_deli_fee');
        $this->formatPriceFieldBeforeSave('meal_deli_fee');
        $this->formatPriceFieldBeforeSave('fuel_deli_fee');
        $this->formatPriceFieldBeforeSave('multi_card_deli_fee');

        return true;
    }

    public function afterFind($results, $primary = false)
    {
        foreach ($results as $key => $val) {
            $this->formatDateFieldAfterFind($results[$key], $val, 'date');

            $this->formatDateFieldAfterFind($results[$key], $val, 'expected_closing_date');
            $this->formatDateFieldAfterFind($results[$key], $val, 'closing_date');

            $this->formatPriceFieldAfterFind($results[$key], $val, 'workers_price');
            $this->formatPriceFieldAfterFind($results[$key], $val, 'workers_price_total');
            $this->formatPriceFieldAfterFind($results[$key], $val, 'management_feel', '.', '');
            $this->formatPriceFieldAfterFind($results[$key], $val, 'transport_adm_fee', '.', '');
            $this->formatPriceFieldAfterFind($results[$key], $val, 'transport_deli_fee');
            $this->formatPriceFieldAfterFind($results[$key], $val, 'meal_adm_fee', '.', '');
            $this->formatPriceFieldAfterFind($results[$key], $val, 'meal_deli_fee');
            $this->formatPriceFieldAfterFind($results[$key], $val, 'fuel_adm_fee', '.', '');
            $this->formatPriceFieldAfterFind($results[$key], $val, 'fuel_deli_fee');
            $this->formatPriceFieldAfterFind($results[$key], $val, 'multi_card_adm_fee', '.', '');
            $this->formatPriceFieldAfterFind($results[$key], $val, 'multi_card_deli_fee');
        }

        return $results;
    }

    private function formatDateFieldBeforeSave($fieldName)
    {
        if (!empty($this->data[$this->alias][$fieldName])) {
            $this->data[$this->alias][$fieldName] = $this->dateFormatBeforeSave($this->data[$this->alias][$fieldName]);
        }
    }

    private function formatPriceFieldBeforeSave($fieldName)
    {
        if (!empty($this->data[$this->alias][$fieldName])) {
            $this->data[$this->alias][$fieldName] = $this->priceFormatBeforeSave($this->data[$this->alias][$fieldName]);
        }
    }

    private function formatDateFieldAfterFind(&$results, $val, $fieldName)
    {

        if (!empty($val[$this->alias][$fieldName])) {
            $notFormattedFieldName = $fieldName.'_not_formatted';

            $results[$this->alias][$notFormattedFieldName] = $val[$this->alias][$fieldName];
            $results[$this->alias][$fieldName] = date('d/m/Y', strtotime($val[$this->alias][$fieldName]));
        }
    }

    private function formatPriceFieldAfterFind(&$results,  $val, $fieldName, $decimalpoint = ',', $separator = '.')
    {
        if (!empty($val[$this->alias][$fieldName])) {
            $notFormattedFieldName = $fieldName.'_not_formatted';

            $results[$this->alias][$notFormattedFieldName] = $val[$this->alias][$fieldName];
            $results[$this->alias][$fieldName] = number_format($val[$this->alias][$fieldName], 2, $decimalpoint, $separator);
        }
    }

    public function getNextNumber()
    {
        $last = $this->field('number', null, 'id desc');

        $nextNumber = 1;
        if ($last) {
            $nextNumber = $last + 1;
        }
        
        return str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
