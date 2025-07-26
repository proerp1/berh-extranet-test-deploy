<?php
class WebhookBtgController extends AppController
{
    public $uses = ['CnabItem', 'Income', 'Order'];

    public function beforeFilter()
    {
        $this->Auth->allow();
        $this->autoRender = false;
        $this->layout = false;
        $this->response->type('json');
        $this->response->header('Access-Control-Allow-Origin','*');
    }

    public function index()
    {
        $input = json_decode($this->request->input(), true);

        if ($input['event'] == 'bank-slips.paid') {
            $data = $input['data'];
            $bankSlipId = $data['bankSlipId'];

            $boleto = $this->CnabItem->find('first', [
                'contain' => ['Income'],
                'conditions' => [
                    'CnabItem.id_web' => $bankSlipId,
                ],
            ]);

            if (!empty($boleto)) {
                $itens = $this->CnabItem->find('all', [
                    'conditions' => [
                        'Income.id' => $boleto['Income']['id'],
                    ],
                ]);

                foreach ($itens as $item) {
                    $this->CnabItem->id = $item['CnabItem']['id'];
                    $this->CnabItem->save([
                        'CnabItem' => [
                            'status_id' => 61
                        ]
                    ]);

                    if ($item['Income']['order_id'] != null) {
                        $this->Order->atualizarStatusPagamento($item['Income']['order_id']);
                    }
                }

                $this->Income->save([
                    'id' => $boleto['Income']['id'],
                    'status_id' => 17,
                    'valor_pago' => $data['amountPaid'],
                    'data_pagamento' => $data['paidAt'],
                ]);
            }
        }

        return json_encode(true);
    }
}
