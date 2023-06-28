<?php 
App::uses('AuthComponent', 'Controller/Component');
class LoginConsulta extends AppModel {
    public $name = 'LoginConsulta';
    public $useTable = 'login_consulta';

    public $belongsTo = array(
        'Status' => array(
            'className' => 'Status',
            'foreignKey' => 'status_id',
            'conditions' => array('Status.categoria' => 1)
        ),
        'Customer',
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_creator_id'
        ),
        'UsuarioAlteracao' => array(
            'className' => 'User',
            'foreignKey' => 'user_updated_id'
        ),
        'CustomerUser' => array(
            'className' => 'CustomerUser',
            'foreignKey' => 'customer_user_id'
        ),
    );

    public function beforeFind($queryData) {
        $queryData['conditions'][] = array('LoginConsulta.data_cancel' => '1901-01-01 00:00:00');
        
        return $queryData;
    }

    public $validate = array(
        'login' => array(
            'required' => array(
                'rule' => array('notBlank'),
                'message' => 'O login não pode ser um já existente',
                'last' => false
            ),
            'isUnique' => array(
                'rule' => 'isUnique',
                'message' => 'O login fornecido já foi cadastrado'
            )
        ),
        'senha' => array(
            'required' => array(
                'rule' => array('notBlank'),
                'message' => 'Campo senha é obrigatório'
            )
        ),
        'status_id' => array(
            'required' => array(
                'rule' => array('notBlank'),
                'message' => 'Campo status obrigatório'
            )
        )
    );

    public function blinda_logon($dados){
        $sql = "UPDATE login_consulta lc SET lc.login_blindado = 1 WHERE lc.id IN (".$dados.")";

        $exSql = $this->query($sql);

        return $exSql;
    }
}