<?php

App::uses('ApiItau', 'Lib');
App::uses('AppShell', 'Console/Command');

class UpdateBoletoStatusShell extends AppShell
{
    public $uses = ['Income', 'CnabItem'];

    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->description('Atualiza o status do boleto de acordo com o banco.');

        return $parser;
    }

    public function main()
    {
        $this->out('Começou...');

        $itens = $this->CnabItem->find('all', [
            'conditions' => [
                'CnabItem.status_id not in(61,62,63)',
                'CnabItem.id_web is not null',
                'Income.cnab_gerado' => 1,
            ],
            'order' => ['Income.vencimento' => 'desc'],
        ]);

        foreach ($itens as $item) {
            $conta = $this->Income->getDadosBoleto($item['CnabItem']['income_id']);

            $ApiItau = new ApiItau();
            $boleto = $ApiItau->buscarBoleto($conta);

            if ($boleto['success']) {
                $dadoBoleto = Hash::extract($boleto['contents']['data'], '{n}.dado_boleto.dados_individuais_boleto.0');

                if (!empty($dadoBoleto)) {
                    if ($dadoBoleto[0]['situacao_geral_boleto'] == 'Paga') {
                        $this->CnabItem->id = $item['CnabItem']['id'];
                        $this->CnabItem->save([
                            'CnabItem' => [
                                'status_id' => 61
                            ]
                        ]);
                        
                        $this->Income->id = $item['CnabItem']['income_id'];
                        $this->Income->save([
                            'Income' => [
                                'status_id' => 17,
                                'data_pagamento' => date('Y-m-d H:i:s'),
                                'valor_pago' => $dadoBoleto[0]['valor_titulo'],
                            ]
                        ]);

                        $this->out("Boleto {$item['CnabItem']['income_id']} {$dadoBoleto[0]['situacao_geral_boleto']}");
                    } else if ($dadoBoleto[0]['situacao_geral_boleto'] == 'Em Aberto' && $dadoBoleto[0]['status_vencimento'] == 'Vencida') {
                        $this->CnabItem->id = $item['CnabItem']['id'];
                        $this->CnabItem->save([
                            'CnabItem' => [
                                'status_id' => 61
                            ]
                        ]);
                        
                        $this->Income->id = $item['CnabItem']['income_id'];
                        $this->Income->save([
                            'Income' => [
                                'status_id' => 55,
                                'data_baixa' => date('Y-m-d H:i:s'),
                            ]
                        ]);

                        $this->out("Boleto {$item['CnabItem']['income_id']} baixado");
                    }
                }
            } else {
                $this->err($boleto['error']);
            }
        }

        $this->out('Fim.');
    }
}
