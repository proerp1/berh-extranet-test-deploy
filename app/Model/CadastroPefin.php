<?php 
App::uses('AuthComponent', 'Controller/Component');
class CadastroPefin extends AppModel {
	public $name = 'CadastroPefin';
	public $useTable = 'cadastro_pefin';

	public $belongsTo = array(
		'Customer',
		'CustomerUser',
		'MotivoBaixa',
		'CadastroPefinLote',
		'NaturezaOperacao',
		'Status' => array(
			'className' => 'Status',
			'foreignKey' => 'status_id',
			'conditions' => array('Status.categoria' => 7)
		)
	);

	public $hasMany = array(
		'CadastroPefinErros' => array(
			'className' => 'CadastroPefinErros',
			'foreignKey' => 'cadastro_pefin_id'
		)
	);

	/*public $hasOne = array(
		'TemCoobrigado' => array(
			'className' => 'CadastroPefin',
			'foreignKey' => 'principal_id',
			'conditions' => array('TemCoobrigado.data_cancel' => '1901-01-01 00:00:00')
		)
	);*/

	public function beforeFind($queryData) {

		$queryData['conditions'][] = array('CadastroPefin.data_cancel' => '1901-01-01 00:00:00');
		
		return $queryData;
	}

	public function beforeSave($options = array()) {
		if (!empty($this->data['CadastroPefin']['data_compra'])) {
			$this->data['CadastroPefin']['data_compra_nao_formatada'] = $this->data['CadastroPefin']['data_compra'];
			$this->data['CadastroPefin']['data_compra'] = $this->dateFormatBeforeSave($this->data['CadastroPefin']['data_compra']);
		}
		
		if (!empty($this->data['CadastroPefin']['venc_divida'])) {
			$this->data['CadastroPefin']['venc_divida_nao_formatada'] = $this->data['CadastroPefin']['venc_divida'];
			$this->data['CadastroPefin']['venc_divida'] = $this->dateFormatBeforeSave($this->data['CadastroPefin']['venc_divida']);
		}
		
		if (!empty($this->data['CadastroPefin']['valor'])) {
			$this->data['CadastroPefin']['valor_nao_formatada'] = $this->data['CadastroPefin']['valor'];
			$this->data['CadastroPefin']['valor'] = $this->priceFormatBeforeSave($this->data['CadastroPefin']['valor']);
		}

		return true;
	}

	public function priceFormatBeforeSave($price) {
		$valueFormatado = str_replace('.', '', $price);
		$valueFormatado = str_replace(',', '.', $valueFormatado);

		return $valueFormatado;
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

	public function afterFind($results, $primary = false){
		foreach ($results as $key => $val) {
			if (isset($val['CadastroPefin']['data_compra'])) {
				$results[$key]['CadastroPefin']['data_compra_nao_formatado'] = $val['CadastroPefin']['data_compra'];
				$results[$key]['CadastroPefin']['data_compra'] = date("d/m/Y", strtotime($val['CadastroPefin']['data_compra']));
			}
			
			if (isset($val['CadastroPefin']['venc_divida'])) {
				$results[$key]['CadastroPefin']['venc_divida_nao_formatado'] = $val['CadastroPefin']['venc_divida'];
				$results[$key]['CadastroPefin']['venc_divida'] = date("d/m/Y", strtotime($val['CadastroPefin']['venc_divida']));
			}

			if (isset($val['CadastroPefin']['created'])) {
				$results[$key]['CadastroPefin']['created_nao_formatado'] = $val['CadastroPefin']['created'];
				$results[$key]['CadastroPefin']['created'] = date("d/m/Y h:i:s", strtotime($val['CadastroPefin']['created']));
			}
			
			if (isset($val['CadastroPefin']['valor'])) {
				$results[$key]['CadastroPefin']['valor_nao_formatado'] = $val['CadastroPefin']['valor'];
				$results[$key]['CadastroPefin']['valor'] = number_format($val['CadastroPefin']['valor'],2,",",".");
			}
		}

		return $results;
	}

	public $validate = array(
		'natureza_operacao_id' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'Campo obrigatório'
			)
		),
		'tipo_pessoa' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'Campo obrigatório'
			)
		),
		'documento' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'Campo obrigatório'
			)
		),
		'nome' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'Campo obrigatório'
			)
		),
		'cep' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'Campo obrigatório'
			)
		),
		'endereco' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'Campo obrigatório'
			)
		),
		'bairro' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'Campo obrigatório'
			)
		),
		'cidade' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'Campo obrigatório'
			)
		),
		'estado' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'Campo obrigatório'
			)
		),
		'num_banco' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'Campo obrigatório'
			)
		),
		'num_agencia' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'Campo obrigatório'
			)
		),
		'num_conta_corrente' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'Campo obrigatório'
			)
		),
		'num_cheque' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'Campo obrigatório'
			)
		),
		'alinea' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'Campo obrigatório'
			)
		),
		'data_compra' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'Campo obrigatório',
				'last' => false
			),
			'date_format' => array(
				'rule' => array('date', 'dmy'),
				'message' => 'Digite uma data no formato DD/MM/YYYY.'
			)
		),
		'nosso_numero' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'Campo obrigatório'
			)
		),
		'numero_titulo' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'Campo obrigatório'
			)
		),
		'venc_divida' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'Campo obrigatório'
			),
			'date_format' => array(
				'rule' => array('date', 'dmy'),
				'message' => 'Digite uma data no formato DD/MM/YYYY.'
			)
		),
		'valor' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'Campo obrigatório',
				'last' => false
			),
			'minValue' => array(
				'rule' => array('minValue', 15),
				'message' => 'O valor não pode ser menor que R$ 15,00'
			)
		),
		'email' => array(
			'email' => array(
				'rule' => 'email',
				'message' => 'O e-mail deve ser válido',
				'allowEmpty' => true
			)
		),
	);

	public function minValue($check, $limit) {
		$valueFormatado = str_replace('.', '', $check);
		$valueFormatado = str_replace(',', '.', $valueFormatado);

		if ($valueFormatado['valor'] < $limit) {
			return false;
		}	else {
			return true;
		}
	}

}