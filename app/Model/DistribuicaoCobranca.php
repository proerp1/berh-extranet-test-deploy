<?php
class DistribuicaoCobranca extends AppModel
{
    public $name = 'DistribuicaoCobranca';
    public $useTable = 'distribuicao_cobranca';

    public $hasMany = [
        'DistribuicaoCobrancaUsuario' => [
            'className' => 'DistribuicaoCobrancaUsuario',
            'foreignKey' => 'distribuicao_cobranca_id'
        ],
        'QtdeUsuarios' => [
            'className' => 'DistribuicaoCobrancaUsuario',
            'foreignKey' => 'distribuicao_cobranca_id',
            'finderQuery' => 'SELECT *, count(QtdeUsuarios.id) as total_clientes
												FROM distribuicao_cobranca_usuarios AS QtdeUsuarios
												WHERE QtdeUsuarios.distribuicao_cobranca_id in ({$__cakeID__$}) and QtdeUsuarios.data_cancel = "1901-01-01 00:00:00"
												GROUP BY QtdeUsuarios.distribuicao_cobranca_id, QtdeUsuarios.user_id'
        ]
    ];
    
    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['DistribuicaoCobranca.data_cancel' => '1901-01-01 00:00:00'];

        return $queryData;
    }
}
