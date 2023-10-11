<?php

App::uses('AuthComponent', 'Controller/Component');
class BankTicket extends AppModel
{
	public $name = 'BankTicket';

	public $belongsTo = array(
		'Status' => array(
			'conditions' => ['Status.categoria' => 1]
		),
		'Bank' => [
			'className' => 'Bank',
			'foreignKey' => 'bank_account_id',
		]
	);

	public function beforeFind($queryData)
	{

		$queryData['conditions'][] = ['BankTicket.data_cancel' => '1901-01-01 00:00:00'];

		return $queryData;
	}

	public function afterFind($results, $primary = false)
	{
		foreach ($results as $key => $val) {
			if (isset($val[$this->alias]['valor_taxa_bancaria'])) {
				$results[$key][$this->alias]['valor_taxa_bancaria_nao_formatada'] = $val[$this->alias]['valor_taxa_bancaria'];
				$results[$key][$this->alias]['valor_taxa_bancaria'] = number_format($val[$this->alias]['valor_taxa_bancaria'], 2, ',', '.');
			}

			if (isset($val[$this->alias]['multa_boleto'])) {
				$results[$key][$this->alias]['multa_boleto_nao_formatada'] = $val[$this->alias]['multa_boleto'];
				$results[$key][$this->alias]['multa_boleto'] = number_format($val[$this->alias]['multa_boleto'], 2, ',', '.');
			}

			if (isset($val[$this->alias]['juros_boleto_dia'])) {
				$results[$key][$this->alias]['juros_boleto_dia_nao_formatada'] = $val[$this->alias]['juros_boleto_dia'];
				$results[$key][$this->alias]['juros_boleto_dia'] = number_format($val[$this->alias]['juros_boleto_dia'], 3, ',', '.');
			}
		}

		return $results;
	}

	public function beforeSave($options = array())
	{
		if (!empty($this->data[$this->alias]['valor_taxa_bancaria'])) {
			$this->data[$this->alias]['valor_taxa_bancaria'] = $this->priceFormatBeforeSave($this->data[$this->alias]['valor_taxa_bancaria']);
		}

		if (!empty($this->data[$this->alias]['multa_boleto'])) {
			$this->data[$this->alias]['multa_boleto'] = $this->priceFormatBeforeSave($this->data[$this->alias]['multa_boleto']);
		}

		return true;
	}

	public function priceFormatBeforeSave($price)
	{
		$valueFormatado = str_replace('.', '', $price);
		$valueFormatado = str_replace(',', '.', $valueFormatado);

		return $valueFormatado;
	}

	public $validate = array(
		'status_id' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'Campo obrigatório'
			)
		),
		'carteira' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'Campo obrigatório'
			)
		),
		'codigo_cedente' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'Campo obrigatório'
			)
		),
		'cobranca_taxa_bancaria' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'Campo obrigatório'
			)
		)
	);
}
