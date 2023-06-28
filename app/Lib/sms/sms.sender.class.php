<?php

require_once APP.'Lib/sms/sms.commom.class.php';

class Sender
{
    private $login = 'hcheck';
    private $senha = 'hc5956';

    public function send_test($celular)
    {
        $function = 'EnviaSMS';
        $num = str_replace([' ', '-', '(', ')'], '', $celular);

        $arguments = ['EnviaSMS' => [
            'NumUsu' => $this->login,
            'Senha' => $this->senha,
            'SeuNum' => '123123123',
            'Celular' => '55'.$num,
            'Mensagem' => utf8_encode('teste berh'),
        ]];

        $soap = new Commom();
        $soap->post($function, $arguments);
    }

    public function send_blocked_notification($celular)
    {
        $function = 'EnviaSMS';
        $num = str_replace([' ', '-', '(', ')'], '', $celular);

        $arguments = ['EnviaSMS' => [
            'NumUsu' => $this->login,
            'Senha' => $this->senha,
            'SeuNum' => '123123123',
            'Celular' => '55'.$num,
            'Mensagem' => utf8_encode('SERASA: PREZADO CLIENTE INFORMAMOS QUE O SISTEMA SERA BLOQUEADO POR FALTA DE PAGAMENTO, ENCONTRA-SE DISPONIVEL NO SEU E-MAIL E NO NOSSO SITE. DUVIDAS 4020-7705.'),
        ]];

        $soap = new Commom();
        $soap->post($function, $arguments);
    }

    public function send_billet_available_notification($celular)
    {
        $function = 'EnviaSMS';
        $num = str_replace([' ', '-', '(', ')'], '', $celular);

        $arguments = ['EnviaSMS' => [
            'NumUsu' => $this->login,
            'Senha' => $this->senha,
            'SeuNum' => '123123123',
            'Celular' => '55'.$num,
            'Mensagem' => utf8_encode('SERASA: PREZADO CLIENTE INFORMAMOS QUE SUA FATURA COM VENCIMENTO DIA 10, ENCONTRA-SE DISPONIVEL NO SEU E-MAIL E NO NOSSO SITE. DUVIDAS 4020-7705 OU 77-34293800.'),
        ]];

        $soap = new Commom();
        $soap->post($function, $arguments);
    }

    public function send_billet_today_due_date($celular)
    {
        $function = 'EnviaSMS';
        $num = str_replace([' ', '-', '(', ')'], '', $celular);

        $arguments = ['EnviaSMS' => [
            'NumUsu' => $this->login,
            'Senha' => $this->senha,
            'SeuNum' => '123123123',
            'Celular' => '55'.$num,
            'Mensagem' => utf8_encode('SERASA: PREZADO CLIENTE INFORMAMOS QUE E HOJE O VENCIMENTO DA SUA FATURA, ENCONTRA-SE DISPONIVEL NO SEU E-MAIL E NO NOSSO SITE. DUVIDAS 4020-7705 OU 77-34293800'),
        ]];

        $soap = new Commom();
        $soap->post($function, $arguments);
    }

    public function send_billet_tomorrow_due_date($celular)
    {
        $function = 'EnviaSMS';
        $num = str_replace([' ', '-', '(', ')'], '', $celular);

        $arguments = ['EnviaSMS' => [
            'NumUsu' => $this->login,
            'Senha' => $this->senha,
            'SeuNum' => '123123123',
            'Celular' => '55'.$num,
            'Mensagem' => utf8_encode('SERASA: PREZADO CLIENTE INFORMAMOS QUE E AMANHA O VENCIMENTO DA SUA FATURA, ENCONTRA-SE DISPONIVEL NO SEU E-MAIL E NO NOSSO SITE. DUVIDAS 4020-7705 OU 77-34293800'),
        ]];

        $soap = new Commom();
        $soap->post($function, $arguments);
    }

    public function send_billet_negociado_notification($dados)
    {
        $function = 'EnviaSMS';
        $num = str_replace([' ', '-', '(', ')'], '', $dados['celular']);

        $arguments = ['EnviaSMS' => [
            'NumUsu' => $this->login,
            'Senha' => $this->senha,
            'SeuNum' => '123123123',
            'Celular' => '55'.$num,
            'Mensagem' => utf8_encode('SERASA: PREZADO CLIENTE SEGUE CODIGO DE BARRAS PARA PAGAMENTO '.$dados['linha'].' DUVIDAS 4020-7705'),
        ]];

        $soap = new Commom();
        $soap->post($function, $arguments);
    }

    public function send_billet_not_found_payment_notification($celular)
    {
        $function = 'EnviaSMS';
        $num = str_replace([' ', '-', '(', ')'], '', $celular);

        $arguments = ['EnviaSMS' => [
            'NumUsu' => $this->login,
            'Senha' => $this->senha,
            'SeuNum' => '123123123',
            'Celular' => '55'.$num,
            'Mensagem' => utf8_encode('SERASA: PREZADO CLIENTE AINDA NAO IDENTIFICAMOS O PAGAMENTO DA SUA FATURA, ENCONTRA-SE DISPONIVEL NO SEU E-MAIL E NO NOSSO SITE. DUVIDAS 4020-7705 OU 7734293800'),
        ]];

        $soap = new Commom();
        $soap->post($function, $arguments);
    }

    public function send_extrajudicial_notification($celular)
    {
        $function = 'EnviaSMS';
        $num = str_replace([' ', '-', '(', ')'], '', $celular);

        $arguments = ['EnviaSMS' => [
            'NumUsu' => $this->login,
            'Senha' => $this->senha,
            'SeuNum' => '123123123',
            'Celular' => '55'.$num,
            'Mensagem' => utf8_encode('NOTIFICAÇÃO EXTRAJUDICIAL: NAO IDENTIFICAMOS O PAGAMENTO DA SUA FATURA, ENCONTRA-SE DISPONIVEL NO SEU E-MAIL E NO NOSSO SITE. DUVIDAS 4020-7705 OU 7734293800'),
        ]];

        $soap = new Commom();
        $soap->post($function, $arguments);
    }

    public function send_meproteja($celular, $mensagem)
    {
        $function = 'EnviaSMS';
        $num = str_replace([' ', '-', '(', ')'], '', $celular);

        $arguments = ['EnviaSMS' => [
            'NumUsu' => $this->login,
            'Senha' => $this->senha,
            'SeuNum' => '123123123',
            'Celular' => '55'.$num,
            'Mensagem' => utf8_encode($mensagem),
        ]];

        $soap = new Commom();
        $soap->post($function, $arguments);
    }
}
