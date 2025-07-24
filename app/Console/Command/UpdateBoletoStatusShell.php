<?php

App::uses('ApiItau', 'Lib');
App::uses('AppShell', 'Console/Command');

class UpdateBoletoStatusShell extends AppShell
{
    public $uses = ['Income', 'CnabItem', 'Order'];

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
                                'data_pagamento' => $this->getNextWeekdayDate(),
                                'valor_pago' => $dadoBoleto[0]['valor_titulo'],
                            ]
                        ]);

                        if ($item['Income']['order_id'] != null) {
                            $order = $this->Order->find('first', [
                                'conditions' => ['Order.id' => $item['Income']['order_id']],
                                'fields' => ['Order.pedido_complementar'],
                                'recursive' => -1
                            ]);

                            if ($order) {
                                $statusId = ($order['Order']['pedido_complementar'] == 2) ? 104 : 85;

                                $this->Order->id = $item['Income']['order_id'];
                                $this->Order->save([
                                    'Order' => [
                                        'status_id' => $statusId,
                                        'payment_date' => $this->getNextWeekdayDate('Y-m-d'),
                                    ]
                                ]);
                            }
                        }

                        $this->out("Boleto {$item['CnabItem']['income_id']} {$dadoBoleto[0]['situacao_geral_boleto']}");
                    } else if (
                        $dadoBoleto[0]['situacao_geral_boleto'] == 'Em Aberto' 
                        && $dadoBoleto[0]['status_vencimento'] == 'Vencida'
                        && $this->isMoreThan5DaysFromToday($item['Income']['vencimento_nao_formatado'])
                    ) {
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

                        if ($item['Income']['order_id'] != null) {
                            $this->Order->id = $item['Income']['order_id'];
                            $this->Order->save([
                                'Order' => [
                                    'status_id' => 94,
                                ]
                            ]);
                        }

                        $this->out("Boleto {$item['CnabItem']['income_id']} baixado");
                    }
                }
            } else {
                $this->err($boleto['error']);
            }
        }

        $this->out('Fim.');
    }

    public function isMoreThan5DaysFromToday($targetDate) {
        $currentDate = new DateTime();

        $targetDateObj = DateTime::createFromFormat('Y-m-d', $targetDate);

        $dateInterval = $currentDate->diff($targetDateObj);

        return $dateInterval->days > 5;
    }

    private function getNextWeekdayDate($format = 'Y-m-d H:i:s') {
        $date = new DateTime();
        $dayOfWeek = $date->format('w'); // 0 = domingo, 6 = sábado

        if ($dayOfWeek == 6) {
            $date->modify('+2 days'); // Sábado → Segunda
        } elseif ($dayOfWeek == 0) {
            $date->modify('+1 day'); // Domingo → Segunda
        }

        return $date->format($format);
    }
}
