<?php
App::uses('Controller', 'Controller');
use Carbon\Carbon;

class BoletoItau extends Controller
{
    public $uses = ['TmpRetornoCnab', 'Income', 'RetornoCnab'];

    private function pagador($dados)
    {
        return new \Eduardokum\LaravelBoleto\Pessoa([
            'documento' => $dados['documento'],
            'nome'      => $dados['nome_primario'],
            'cep'       => $dados['cep'],
            'endereco'  => $dados['endereco'],
            'bairro'    => $dados['bairro'],
            'uf'        => $dados['estado'],
            'cidade'    => $dados['cidade'],
        ]);
    }

    private function beneficiario($dados)
    {
        return new \Eduardokum\LaravelBoleto\Pessoa([
            'documento' => $dados['cnpj'],
            'nome'      => $dados['razao_social'],
            'cep'       => $dados['cep'],
            'endereco'  => $dados['endereco'].', '.$dados['numero'],
            'bairro'    => $dados['bairro'],
            'uf'        => $dados['estado'],
            'cidade'    => $dados['cidade'],
        ]);
    }

    private function boleto($boleto)
    {
        return new Eduardokum\LaravelBoleto\Boleto\Banco\Itau([
            'logo' => APP.'webroot/img/logo-berh-colorido.png',
            'dataVencimento' => Carbon::parse($boleto['Income']['vencimento_nao_formatado']),
            'valor' => $boleto['Income']['valor_total_nao_formatado'],
            'multa' => ($boleto['Income']['valor_total_nao_formatado']*0.05), // 1% do valor do boleto após o vencimento
            'juros' => 1, // 1% ao mês do valor do boleto
            'numero' => $boleto['Income']['nosso_numero'],
            'numeroDocumento' => $boleto['Income']['id'],
            'pagador' => $this->pagador($boleto['Customer']),
            'beneficiario' => $this->beneficiario($boleto['Resale']),
            'carteira' => $boleto['BankTicket']['carteira'],
            'agencia' => $boleto['BankAccount']['agency'],
            'conta' =>  $boleto['BankAccount']['account_number'],
            'convenio' => $boleto['BankAccount']['convenio'],
            'descricaoDemonstrativo' => [
                $boleto['BankTicket']['informativo_boleto'], 
                '-',
                '-'
            ],
            'instrucoes' => [
                $boleto['BankTicket']['instrucao_boleto_1'], 
                $boleto['BankTicket']['instrucao_boleto_2'], 
                $boleto['BankTicket']['instrucao_boleto_3'], 
                $boleto['BankTicket']['instrucao_boleto_4'], 
            ],
            'aceite' => 'S',
            'especieDoc' => 'DM',
            'diasBaixaAutomatica'    => 58,
        ]);
    }

    public function printBoleto($boleto, $pdf = false, $pdfPath = false)
    {
        $Itau = $this->boleto($boleto);

        if ($pdf) {
            $this->downloadPdf($Itau);
        } if ($pdfPath) {
            $this->savePdf($Itau, $pdfPath);
        } else {
            echo $Itau->renderHTML(true);
        }
    }

    public function gerarRemessa($contas, $nome, $remessa)
    {
        $remessa = new Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab400\Banco\Itau(
            [
                'idRemessa' => $remessa,
                'beneficiario' => $this->beneficiario($contas[0]['Resale']),
                'carteira' => $contas[0]['BankTicket']['carteira'],
                'agencia' => $contas[0]['BankAccount']['agency'],
                'conta' =>  $contas[0]['BankAccount']['account_number'],
                'convenio' => $contas[0]['BankAccount']['convenio'],
            ]
        );

        foreach ($contas as $conta) {
            $remessa->addBoleto($this->boleto($conta));
        }

        // Saves the string to a file on the disk whose path was passed in $path argument.
        $remessa->save(APP.'Private'.DS.'remessa_itau'.DS.$nome);
    }

    public function processarRetorno($id, $arquivo)
    {
        $return = new \Eduardokum\LaravelBoleto\Cnab\Retorno\Cnab240\Banco\Itau($arquivo);

        $return->processar();

        $header = $return->getHeaderLote();

        $this->RetornoCnab->id = $id;

        $data['data_arquivo'] = $header->getDataGravacao('Y-m-d');
        $this->RetornoCnab->save($data);

        foreach ($return->getDetalhes() as $object) {
            $detalhe = $object->toArray();

            $conta = $this->Income->find('first', ['conditions' => ["Income.id" => $detalhe['numeroDocumento']], 'recursive' => -1, 'callbacks' => false]);

            if (empty($conta)) {
                $encontrado = 2;
                $income_id = '';
            } else {
                $encontrado = 1;
                $income_id = $conta['Income']['id'];
            }

            $data_tmp = [
                'TmpRetornoCnab' => [
                    'retorno_cnab_id' => $id,
                    'data_pagamento' => $object->getDataOcorrencia('Y-m-d'),
                    'income_id' => $income_id,
                    'nosso_numero' => $detalhe['numeroDocumento'],
                    'encontrado' => $encontrado,
                    'vencimento' => $object->getDataVencimento('Y-m-d'),
                    'valor_pago' => $detalhe['valorRecebido'],
                    'valor_liquido' => $detalhe['valor'],
                    'cod_ocorrencia' => $detalhe['ocorrencia'],
                    'tipo' => $detalhe['ocorrenciaTipo'],
                    'ocorrencia' => $detalhe['ocorrenciaDescricao'],
                    'erro' => $detalhe['error'],
                    'user_created_id' => CakeSession::read('Auth.User.id')
                ]
            ];

            $this->TmpRetornoCnab->create();
            $this->TmpRetornoCnab->save($data_tmp);
        }
    }

    public function downloadHtml($boleto)
    {
        $pdf = new Eduardokum\LaravelBoleto\Boleto\Render\Pdf();

        // Add as many bills as you want.
        $pdf->addBoleto($boleto);

        $pdf->gerarBoleto('D');
    }

    public function downloadPdf($boleto)
    {
        $pdf = new Eduardokum\LaravelBoleto\Boleto\Render\Pdf();

        // Add as many bills as you want.
        $pdf->addBoleto($boleto);

        $pdf->gerarBoleto('D', null, 'boleto_'.uniqid());
    }

    public function savePdf($boleto, $pdfPath)
    {
        $pdf = new Eduardokum\LaravelBoleto\Boleto\Render\Pdf();

        // Add as many bills as you want.
        $pdf->addBoleto($boleto);

        $pdf->gerarBoleto('F', $pdfPath);
    }
}
