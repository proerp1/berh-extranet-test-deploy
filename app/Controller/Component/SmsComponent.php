<?php
App::uses('ZenviaApi', 'Lib');
class SmsComponent extends Component
{
    public $components = ['Session'];

    public function send($celular, $tipo, $msg = null)
    {
        $num = str_replace([' ', '-', '(', ')'], '', $celular);
        $ZenviaApi = new ZenviaApi();

        switch ($tipo) {
            case 'blocked_notification':
                $msg = 'SERASA: PREZADO CLIENTE INFORMAMOS QUE O SISTEMA SERA BLOQUEADO POR FALTA DE PAGAMENTO, ENCONTRA-SE DISPONIVEL NO SEU E-MAIL E NO NOSSO SITE. DUVIDAS 4020-7705.';
                break;

            case 'new_billet':
                $msg = 'SERASA: PREZADO CLIENTE INFORMAMOS QUE SUA FATURA COM VENCIMENTO DIA 10, ENCONTRA-SE DISPONIVEL NO SEU E-MAIL E NO NOSSO SITE. DUVIDAS 4020-7705 OU 77-34293800.';
                break;

            case 'negociado':
                $msg = 'SERASA: PREZADO CLIENTE SEGUE CODIGO DE BARRAS PARA PAGAMENTO DUVIDAS 4020-7705';
                break;

            case 'not_found_payment':
                $msg = 'SERASA: PREZADO CLIENTE AINDA NAO IDENTIFICAMOS O PAGAMENTO DA SUA FATURA, ENCONTRA-SE DISPONIVEL NO SEU E-MAIL E NO NOSSO SITE. DUVIDAS 4020-7705 OU 7734293800';
                break;

            case 'today_due_date':
                $msg = 'SERASA: PREZADO CLIENTE INFORMAMOS QUE E HOJE O VENCIMENTO DA SUA FATURA, ENCONTRA-SE DISPONIVEL NO SEU E-MAIL E NO NOSSO SITE. DUVIDAS 4020-7705 OU 77-34293800';
                break;

            case 'tomorrow_due_date':
                $msg = 'SERASA: PREZADO CLIENTE INFORMAMOS QUE E AMANHA O VENCIMENTO DA SUA FATURA, ENCONTRA-SE DISPONIVEL NO SEU E-MAIL E NO NOSSO SITE. DUVIDAS 4020-7705 OU 77-34293800';
                break;

            case 'extrajudicial':
                $msg = 'NOTIFICAÇÃO EXTRAJUDICIAL: NAO IDENTIFICAMOS O PAGAMENTO DA SUA FATURA, ENCONTRA-SE DISPONIVEL NO SEU E-MAIL E NO NOSSO SITE. DUVIDAS 4020-7705 OU 7734293800';
                break;

            case 'test':
                $msg = 'teste berh';
                break;

            default:
                break;
        }

        return $ZenviaApi->sendSms('55'.$num, utf8_encode($msg));
    }
}
