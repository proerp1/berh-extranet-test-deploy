<?php 
App::uses('AuthComponent', 'Controller/Component');

class LogSupplier extends AppModel {
    public $name = 'LogSupplier';
    public $useTable = 'log_suppliers';

    public $belongsTo = [
        'Supplier',
        'Modalidade' => [
          'className' => 'Modalidade',
          'foreignKey' => 'modalidade_id'
        ],
        'Tecnologia' => [
          'className' => 'Tecnologia',
          'foreignKey' => 'tecnologia_id'
        ],
        'VersaoCadastro' => [
          'className' => 'TecnologiaVersao',
          'foreignKey' => 'versao_cadastro_id',
          'conditions' => ['VersaoCadastro.tipo' => 'cadastro'],
        ],
        'VersaoCredito' => [
          'className' => 'TecnologiaVersao',
          'foreignKey' => 'versao_credito_id',
          'conditions' => ['VersaoCredito.tipo' => 'credito'],
        ],
        'BankAccountType' => [
          'className' => 'BankAccountType',
          'foreignKey' => 'account_type_id'
        ],
        'BankCode' => [
          'className' => 'BankCode',
          'foreignKey' => 'bank_code_id'
        ],
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['LogSupplier.data_cancel' => '1901-01-01 00:00:00'];
        
        return $queryData;
    }

    public function afterFind($results, $primary = false){
      foreach ($results as $key => $val) {
        if (isset($val[$this->alias]['created'])) {
          $results[$key][$this->alias]['created_nao_formatado'] = $val[$this->alias]['created'];
          $results[$key][$this->alias]['created'] = date("d/m/Y H:i:s", strtotime($val[$this->alias]['created']));
        }
      }

      return $results;
    }

    public function logSupplier($data_item)
    {
        $userId = CakeSession::read("Auth.User.id");

        $data = $data_item['Supplier'];

        $registro = [
            'LogSupplier' => [
                'supplier_id'               => $data['id'],
                'transfer_fee_type'         => $data['transfer_fee_type'],
                'realiza_gestao_eficiente'  => $data['realiza_gestao_eficiente'] ? 1 : 0,
                'modalidade_id'             => $data['modalidade_id'],
                'tecnologia_id'             => $data['tecnologia_id'],
                'versao_credito_id'         => $data['versao_credito_id'],
                'versao_cadastro_id'        => $data['versao_cadastro_id'],
                'account_type_id'           => $data['account_type_id'],
                'bank_code_id'              => $data['bank_code_id'],
                'payment_method'            => $data['payment_method'],
                'branch_number'             => $data['branch_number'],
                'branch_digit'              => $data['branch_digit'],
                'acc_number'                => $data['acc_number'],
                'acc_digit'                 => $data['acc_digit'],
                'pix_type'                  => $data['pix_type'],
                'pix_id'                    => $data['pix_id'],
                'user_creator_id'           => $userId,
            ]
        ];

        $this->create();

        return $this->save($registro);
    }
}