<?php
App::uses('CakeEmail', 'Network/Email');
App::uses('ApiBoleto', 'Lib/Credsis');
App::uses('Bancoob', 'Lib');

class EmailComponent extends Component
{
    public function send($dados)
    {
        $email = new CakeEmail();

        $email->config($dados['config']);

        $email->to($dados['viewVars']['email']);
        $email->subject($dados['subject']);
        $email->viewVars($dados['viewVars']);
        if (isset($dados['layout'])) {
            $email->template($dados['template'], $dados['layout']);
        } else {
            $email->template($dados['template']);
        }
        $email->emailFormat('html');

        if ($email->send()) {
            return true;
        } else {
            return false;
        }
    }

    public function send_many($dados)
    {
        $CnabItem = ClassRegistry::init('CnabItem');
        $CnabItemSicoob = ClassRegistry::init('CnabItemSicoob');
        $Income = ClassRegistry::init('Income');
        //init CakeEmail
        $error = [];

        $email = new CakeEmail();
        $ApiBoleto = new ApiBoleto();
    
        $email->config($dados['config']);
        $email->subject($dados['subject']);
        $email->emailFormat('html');

        $customers_array = $dados['customers'];
    
        foreach ($customers_array as $ca) {
            $conta = $Income->find('first', [
                'fields' => ['BankAccount.bank_id'],
                'conditions' => ['Income.id' => $ca['MailList']['income_id']]
            ]);
            $isSicoob = $conta['BankAccount']['bank_id'] == 8;

            if ($isSicoob) {
                $dadosBoleto = $Income->getDadosBoleto($ca['MailList']['income_id']);

                $Bancoob = new Bancoob();
                $name = 'boleto_'.uniqid();
                $pathSicoob = APP.'webroot/files/boleto_sicoob/'.$name.'.pdf';
                $Bancoob->printBoleto($dadosBoleto, false, $pathSicoob);

                $email->attachments($pathSicoob);
            } else {
                $item = $CnabItem->find('first', [
                    'fields' => ['CnabItem.id_web'],
                    'conditions' => [
                        'CnabItem.income_id' => $ca['MailList']['income_id']
                    ],
                    'recursive' => -1
                ]);

                if (!empty($item)) {
                    $boleto = $ApiBoleto->buscarBoleto($item['CnabItem']['id_web']);
                    $bin = base64_decode($boleto['obj']['boleto'], true);


                    $name = uniqid();
                    file_put_contents($name.'.pdf', $bin);

                    $email->attachments(APP.'webroot/'.$name.'.pdf');
                }
            }
            
            if (!isset($dados['avulso'])) {
                //registra envio
                self::control_send_email($dados['id'], $ca['Customer']['id']);
            }
      
            // usado para fazer login no site com o bypass, NAO ALTERAR!!!
            $hash = base64_encode($ca['Customer']['codigo_associado']);
            $hash = rawurlencode($hash);

            //$link = Configure::read('Areadoassociado.link').'users/bypass_login/'.$hash.'/?em_aberto';
            $link = Configure::read('Areadoassociado.link').'billings?em_aberto';

            //substituir valores no corpo do email
            $data     = [$ca['Customer']['nome_secundario'], $ca['Customer']['documento'], $ca['Customer']['codigo_associado'], $link];
            $replace  = ['#NomeFantasia#', '#NumeroCNPJ#', '#CodigoAssociado#', '#LinkGerarBoleto#'];
            $content  = str_replace($replace, $data, $dados['content']);

            $email->to(trim($ca['Customer']['email']));
            /*
            if(($ca['Customer']['email1'])){                
                $email->cc(trim($ca['Customer']['email1']));
            }*/

            $send = $email->send($content);

            if ($isSicoob) {
                unlink($pathSicoob);
            } else {
                if (!empty($item)) {
                    unlink(APP.'webroot/'.$name.'.pdf');
                }
            }

            if (!$send) {
                $error[] = $ca['Customer']['email'];
            }
        }
    
        return $error;
    }

    public function control_send_email($id_campanha, $customer_id)
    {
        //carregar model EmailsCampanha
        $EmailsCampanha = ClassRegistry::init('EmailsCampanha');
        $MailList = ClassRegistry::init('MailList');

        //sinalizar como enviado na campanha
        $EmailsCampanha->updateAll(
            ['send' => true, 'send_data'=> 'current_timestamp'], //set
            ['id' => $id_campanha] // where
        );
    
        //verificar pendentes e atualizar
        $MailList->updateAll(
            ['sent' => true, 'updated'=> 'current_timestamp'], //set
            ['customer_id' => $customer_id] // where
        );

        return true;
    }
}
