<?php

class EconomicGroupProposal extends AppModel
{
    public $name = 'EconomicGroupProposal';

    public $belongsTo = [
        'Customer',
        'EconomicGroup',
        'Status'
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
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['EconomicGroupProposal.data_cancel' => '1901-01-01 00:00:00'];

        return $queryData;
    }

    public function beforeSave($options = [])
    {
        $this->formatDateFieldBeforeSave('date');
        $this->formatDateFieldBeforeSave('expected_closing_date');
        $this->formatDateFieldBeforeSave('closing_date');

        $this->formatPriceFieldBeforeSave('transport_workers_price');
        $this->formatPriceFieldBeforeSave('transport_workers_price_total');
        $this->formatPriceFieldBeforeSave('transport_deli_fee');
        $this->formatPriceFieldBeforeSave('meal_workers_price');
        $this->formatPriceFieldBeforeSave('meal_workers_price_total');
        $this->formatPriceFieldBeforeSave('meal_deli_fee');
        $this->formatPriceFieldBeforeSave('fuel_workers_price');
        $this->formatPriceFieldBeforeSave('fuel_workers_price_total');
        $this->formatPriceFieldBeforeSave('fuel_deli_fee');
        $this->formatPriceFieldBeforeSave('multi_card_workers_price');
        $this->formatPriceFieldBeforeSave('multi_card_workers_price_total');
        $this->formatPriceFieldBeforeSave('multi_card_deli_fee');
        $this->formatPriceFieldBeforeSave('total_price');
        $this->formatPriceFieldBeforeSave('saude_card_deli_fee');
        $this->formatPriceFieldBeforeSave('saude_card_workers_price');
        $this->formatPriceFieldBeforeSave('saude_card_workers_price_total');
        $this->formatPriceFieldBeforeSave('prev_card_deli_fee');
        $this->formatPriceFieldBeforeSave('prev_card_workers_price');
        $this->formatPriceFieldBeforeSave('prev_card_workers_price_total');
        $this->formatPriceFieldBeforeSave('tpp');

        return true;
    }

    public function afterFind($results, $primary = false)
    {
        foreach ($results as $key => $val) {
            $this->formatDateFieldAfterFind($results[$key], $val, 'date');

            $this->formatDateFieldAfterFind($results[$key], $val, 'expected_closing_date');
            $this->formatDateFieldAfterFind($results[$key], $val, 'closing_date');

            $this->formatPriceFieldAfterFind($results[$key], $val, 'transport_workers_price');
            $this->formatPriceFieldAfterFind($results[$key], $val, 'transport_workers_price_total');
            $this->formatPriceFieldAfterFind($results[$key], $val, 'management_feel', '.', '');
            $this->formatPriceFieldAfterFind($results[$key], $val, 'transport_adm_fee', '.', '');
            $this->formatPriceFieldAfterFind($results[$key], $val, 'transport_deli_fee');
            $this->formatPriceFieldAfterFind($results[$key], $val, 'meal_workers_price');
            $this->formatPriceFieldAfterFind($results[$key], $val, 'meal_workers_price_total');
            $this->formatPriceFieldAfterFind($results[$key], $val, 'meal_adm_fee', '.', '');
            $this->formatPriceFieldAfterFind($results[$key], $val, 'meal_deli_fee');
            $this->formatPriceFieldAfterFind($results[$key], $val, 'fuel_workers_price');
            $this->formatPriceFieldAfterFind($results[$key], $val, 'fuel_workers_price_total');
            $this->formatPriceFieldAfterFind($results[$key], $val, 'fuel_adm_fee', '.', '');
            $this->formatPriceFieldAfterFind($results[$key], $val, 'fuel_deli_fee');
            $this->formatPriceFieldAfterFind($results[$key], $val, 'multi_card_workers_price');
            $this->formatPriceFieldAfterFind($results[$key], $val, 'multi_card_workers_price_total');
            $this->formatPriceFieldAfterFind($results[$key], $val, 'multi_card_adm_fee', '.', '');
            $this->formatPriceFieldAfterFind($results[$key], $val, 'multi_card_deli_fee');
            $this->formatPriceFieldAfterFind($results[$key], $val, 'total_price');
            $this->formatPriceFieldAfterFind($results[$key], $val, 'workers_price_total');
            $this->formatPriceFieldAfterFind($results[$key], $val, 'saude_card_deli_fee');
            $this->formatPriceFieldAfterFind($results[$key], $val, 'saude_card_workers_price');
            $this->formatPriceFieldAfterFind($results[$key], $val, 'saude_card_workers_price_total');
            $this->formatPriceFieldAfterFind($results[$key], $val, 'prev_card_deli_fee');
            $this->formatPriceFieldAfterFind($results[$key], $val, 'prev_card_workers_price');
            $this->formatPriceFieldAfterFind($results[$key], $val, 'prev_card_workers_price_total');
            $this->formatPriceFieldAfterFind($results[$key], $val, 'tpp');
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
        $notFormattedFieldName = $fieldName.'_not_formatted';
        $price = 0;
        if (!empty($val[$this->alias][$fieldName])) {
            $price = $val[$this->alias][$fieldName];
        }

        $results[$this->alias][$notFormattedFieldName] = $price;
        $results[$this->alias][$fieldName] = number_format($price, 2, $decimalpoint, $separator);
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
