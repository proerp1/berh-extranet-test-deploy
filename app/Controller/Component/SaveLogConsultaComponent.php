<?php
class SaveLogConsultaComponent extends Component
{
    public $components = ['Session', 'Auth'];

    public function save($id, $url, $features = [], $customer_id = false, $user_id = false)
    {
        $PlanCustomer = ClassRegistry::init('PlanCustomer');
        $Product = ClassRegistry::init('Product');
        $NovaVidaLogConsulta = ClassRegistry::init('NovaVidaLogConsulta');
        $NovaVidaLogConsultaFeature = ClassRegistry::init('NovaVidaLogConsultaFeature');
        $Feature = ClassRegistry::init('Feature');

        $plano = $PlanCustomer->find('first', ['conditions' => ['PlanCustomer.customer_id' => ($customer_id ? $customer_id : CakeSession::read('Auth.CustomerUser.customer_id')), 'PlanCustomer.status_id' => 1], 'recursive' => -1]);
        $cliente_tabela_preco = $plano['PlanCustomer']['price_table_id'];
        $tabela_precos_produto = $Product->find('first', ['conditions' => ['Product.id' => $id]]);

        $valorConsulta = 0;
        foreach ($tabela_precos_produto['ProductPrice'] as $key => $tabela_preco) {
            if ($cliente_tabela_preco == $tabela_preco['price_table_id']) {
                $valorConsulta = $tabela_preco['value_nao_formatado'];
            }
        }

        $dadosLog = [
            'NovaVidaLogConsulta' => [
                'product_id' => $id,
                'customer_id' => ($customer_id ? $customer_id : CakeSession::read("Auth.CustomerUser.customer_id")),
                'customer_user_id' => ($user_id ? $user_id : CakeSession::read('Auth.CustomerUser.id')),
                'plan_customer_id' => $plano['PlanCustomer']['id'],
                'valor' => $valorConsulta
            ]
        ];

        $NovaVidaLogConsulta->create();
        $NovaVidaLogConsulta->save($dadosLog);

        // se tiver feature salva na log_consulta_features
        if ($features) {
            $dadosLogFeatures = [];
            foreach ($features as $feature_id) {
                $Feature->id = $feature_id;
                $feature = $Feature->read();

                $dadosLogFeatures[] = [
                    'NovaVidaLogConsultaFeature' => [
                        'nova_vida_log_consulta_id' => $NovaVidaLogConsulta->id,
                        'feature_id' => $feature_id,
                        'valor' => $feature['Feature']['valor_nao_formatado'],
                        'user_creator_id' => ($user_id ? $user_id : CakeSession::read('Auth.CustomerUser.id'))
                    ]
                ];
            }

            $NovaVidaLogConsultaFeature->saveMany($dadosLogFeatures);
        }

        return $NovaVidaLogConsulta->id;
    }
}
