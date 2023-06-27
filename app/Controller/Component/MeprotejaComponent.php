<?php

App::uses('SoapClientMeProteja', 'Lib/Meproteja');
include_once APP.'Lib/Meproteja/api.meproteja.php';

/**
 * Essa classe interliga os controllers à classe do cliente soap do me proteja.
 */
class MeprotejaComponent extends Component
{
    public $components = ['Session'];
    public $controller;

    /**
     * Esse método adiciona um cliente ao me proteja.
     */
    public function include_company_old($cnpj, $dias)
    {
        $client = new SoapClientMeProteja();

        return $client->incluir_empresa($cnpj, $dias);
    }

    public function include_company($cnpj, $dias, $partners)
    {
        $client = new ApiMeproteja();

        return $client->incluir_empresa($cnpj, $dias, $partners);
    }

    public function exclude_company_old($cnpj)
    {
        $client = new SoapClientMeProteja();

        $cnpj = str_replace([".", "-", "/"], "", $cnpj);
        $cnpj = substr($cnpj, 0, 8);

        return $client->excluir_empresa($cnpj);
    }

    public function exclude_company($cnpj)
    {
        $client = new ApiMeproteja();

        $cnpj = str_replace([".", "-", "/"], "", $cnpj);
        $cnpj = substr($cnpj, 0, 8);

        return $client->exclude_company($cnpj);
    }

    public function search_company($cnpj)
    {
        $client = new SoapClientMeProteja();

        return $client->consulta_empresa($cnpj);
    }

    public function include_partner($cnpj, $tipo_doc, $doc)
    {
        $client = new SoapClientMeProteja();

        return $client->incluir_socio($cnpj, $tipo_doc, $doc);
    }

    public function exclude_partner($cnpj, $tipo_doc, $doc)
    {
        $client = new SoapClientMeProteja();

        $ret = $this->get_info_partner($cnpj, $tipo_doc, $doc);

        $cnpj = $ret['cnpj_emp'];
        $tipo_doc = $ret['tipo_doc'];
        $doc = $ret['doc'];

        return $client->excluir_socio($cnpj, $tipo_doc, $doc);
    }

    public function search_partner($cnpj, $tipo_doc, $doc)
    {
        $client = new SoapClientMeProteja();

        return $client->consulta_socio($cnpj, $tipo_doc, $doc);
    }

    public function get_qtde_apontamentos($result, $tipo_doc = null)
    {
        // $em = $this->Bootstrap->getORMEm();

        $qtde = 0;
        if (isset($result->dadosRelato->apontamentos)) {
            $apontamentos = $result->dadosRelato->apontamentos;

            if ('CPF' == $tipo_doc) {
                //Estrutura PF
                if (isset($apontamentos->PendenciasFinanceiras->Pefins)) {
                    $qtde += $apontamentos->PendenciasFinanceiras->Pefins->Quantidade;
                }
                if (isset($apontamentos->PendenciasFinanceiras->Refins)) {
                    $qtde += $apontamentos->PendenciasFinanceiras->Refins->Quantidade;
                }
                if (isset($apontamentos->AcoesJudiciais)) {
                    $qtde += $apontamentos->AcoesJudiciais->Quantidade;
                }
                if (isset($apontamentos->DividasVencidas->DividaVencida)) {
                    $dividasVencidas = $apontamentos->DividasVencidas->DividaVencida;

                    $qtde += count($dividasVencidas);
                }
                if (isset($apontamentos->Protestos)) {
                    $qtde += $apontamentos->Protestos->Quantidade;
                }
                if (isset($apontamentos->ParticipacoesFalencias)) {
                    $qtde += $apontamentos->ParticipacoesFalencias->Quantidade;
                }
                if (isset($apontamentos->ChequesSemFundoAchei->ChequeSemFundoAchei)) {
                    $chequeSemFundo = $apontamentos->ChequesSemFundoAchei->ChequeSemFundoAchei;

                    $qtde += count($chequeSemFundo);
                }
                if (isset($apontamentos->ChequesExtraviadoSustadoRecheque)) {
                    $qtde += $apontamentos->ChequesExtraviadoSustadoRecheque->Quantidade;
                }
            } else {
                //Estrutura PJ
                if (isset($apontamentos->PendenciasFinanceiras->Pefins)) {
                    $qtde += $apontamentos->PendenciasFinanceiras->Pefins->Quantidade;
                }
                if (isset($apontamentos->PendenciasFinanceiras->Refins)) {
                    $qtde += $apontamentos->PendenciasFinanceiras->Refins->Quantidade;
                }
                if (isset($apontamentos->AcoesJudiciais)) {
                    $qtde += $apontamentos->AcoesJudiciais->Quantidade;
                }
                if (isset($apontamentos->DividasVencidas->DividaVencida)) {
                    $dividasVencidas = $apontamentos->DividasVencidas->DividaVencida;

                    $qtde += count($dividasVencidas);
                }
                if (isset($apontamentos->Protestos)) {
                    $qtde += $apontamentos->Protestos->Quantidade;
                }
                if (isset($apontamentos->FalenciasConcordatas)) {
                    $qtde += $apontamentos->FalenciasConcordatas->Quantidade;
                }
                if (isset($apontamentos->ChequesSemFundoAchei->ChequeSemFundoAchei)) {
                    $chequeSemFundo = $apontamentos->ChequesSemFundoAchei->ChequeSemFundoAchei;

                    $qtde += count($chequeSemFundo);
                }
                if (isset($apontamentos->ChequesExtraviadoSustadoRecheque)) {
                    $qtde += $apontamentos->ChequesExtraviadoSustadoRecheque->Quantidade;
                }
            }
        }

        return $qtde;
    }

    public function get_info_partner($cnpj_empresa, $tipo_documento, $documento)
    {
        $cnpj_emp = str_replace(['.', '-', '/'], '', $cnpj_empresa);
        $cnpj_emp = substr($cnpj_emp, 0, 8);

        if (1 == $tipo_documento) {
            $tipo_doc = 'CPF';

            $doc = str_replace(['-', '.'], '', $documento);
            $doc = substr($doc, 0, 9);
        } else {
            $tipo_doc = 'CNPJ';

            $doc = str_replace(['.', '-', '/'], '', $documento);
            $doc = substr($doc, 0, 8);
        }

        $dados = ['cnpj_emp' => $cnpj_emp,
            'tipo_doc' => $tipo_doc,
            'doc' => $doc, ];
        //debug($dados);die;
        return $dados;
    }
}
