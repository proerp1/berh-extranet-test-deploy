<?php
class Resposta extends AppModel
{
    public $primaryKey = 'respostaID';
    public $virtualFields = [
        'full_name' => 'CONCAT(Resposta.respostaRegistro, " - ", Resposta.respostaNome)'
    ];
    public $displayField = 'full_name';

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['Resposta.respostaDataCancel' => '1901-01-01 00:00:00'];
        
        return $queryData;
    }
    public $belongsTo = [
        'Produto' => [
            'className' => 'Product',
            'foreignKey' => 'produtoID',
            'conditions' => ['Produto.data_cancel' => '1901-01-01 00:00:00']
        ]
    ];

    public function commom_task_string($produto_id, $tipo)
    {
        $sql = "SELECT r.respostaID,
                                        r.respostaNome,
                                        r.respostaPaiID,
                                        r.respostaQtdeColunas,
                                        r.respostaRegistro,
                                        r.respostaSubtipo,
                                        r.respostaNumeroOrdem,
                                        r.respostaFlagRestricao,
                                        r.respostaVisivelCliente,
                                        r.respostaInformativo,
                                        i.itemRespostaID,
                                        i.itemRespostaNome,
                                        i.itemRespostaFormatacao,
                                        i.itemRespostaMultivalorado,
                                        i.itemRespostaVisivelCliente,
                                        i.itemRespostaMsgPersonalizada,
                                        i.itemRespostaInicio,
                                        i.itemRespostaByte,
                                        i.itemRespostaDataCancel,
                                        io.itemOpcaoCodigo,
                                        io.itemOpcaoNome
                            FROM respostas r
                            INNER JOIN itensResposta i on i.respostaID = r.respostaID
                            LEFT JOIN itensOpcoes io on i.itemRespostaID = io.itemRespostaID
                            WHERE r.produtoID = ".$produto_id." AND r.respostaDataCancel = '1901-01-01' order by i.itemRespostaID asc";

        $exSql = $this->query($sql);
        $features = [];

        for ($i=0; $i < count($exSql); $i++) {
            // debug($res);
            $resposta[] = ["resp_id"           => $exSql[$i]['r']["respostaID"],
                "titulo"                        => $exSql[$i]['r']["respostaNome"],
                "grupo_resposta"        => $exSql[$i]['r']["respostaPaiID"],
                "qtde_agrupar"          => $exSql[$i]['r']["respostaQtdeColunas"],
                "registro"                  => $exSql[$i]['r']["respostaRegistro"],
                "flag_restricao"        => $exSql[$i]['r']["respostaFlagRestricao"],
                "ordem"                         => $exSql[$i]['r']["respostaNumeroOrdem"],
                "subregistro"           => $exSql[$i]['r']["respostaSubtipo"],
                "tam"                           => 0,
                "visivel_cliente"   => $exSql[$i]['r']["respostaVisivelCliente"],
                "informativo"           => $exSql[$i]['r']["respostaInformativo"],
                "itens" => [
                    "cord"                                      => [$exSql[$i]['i']["itemRespostaInicio"], $exSql[$i]['i']["itemRespostaByte"]],
                    "item_id"                               => $exSql[$i]['i']["itemRespostaID"],
                    "nome"                                      => $exSql[$i]['i']["itemRespostaNome"],
                    "formato"                               => $exSql[$i]['i']["itemRespostaFormatacao"],
                    "multivalorado"                     => $exSql[$i]['i']["itemRespostaMultivalorado"],
                    "itemRespostaDataCancel"    => $exSql[$i]['i']["itemRespostaDataCancel"],
                    "visivel_cliente"               => $exSql[$i]['i']["itemRespostaVisivelCliente"],
                    "msg_person"                            => $exSql[$i]['i']["itemRespostaMsgPersonalizada"],
                    "opts"                                      => [
                        $exSql[$i]['io']["itemOpcaoCodigo"] => $exSql[$i]['io']["itemOpcaoNome"]
                    ]
                ]
            ];
        }
        
        $opts = [];

        // seraparndo por resposta
        $orgazinado_por_resp = [];
        foreach ($resposta as $key => $value) {
            $orgazinado_por_resp[$value["resp_id"]][] = $value;
        }

        return $orgazinado_por_resp;
    }

    public function get_respostas_por_produto($produto_id, $tipo = 1, $tipo_chave = 1)
    {
        $orgazinado_por_resp = $this->commom_task_string($produto_id, $tipo);
        // debug($orgazinado_por_resp);die;
        // seraparndo por item
        $itens = $this->get_main_logic($orgazinado_por_resp, $tipo, $tipo_chave);
        $orgazinado_por_item = $itens["orgazinado_por_item"];
        $opcoes = $itens["opcoes"];



        // seraparndo por opcoes
        $atual = 0;
        $tamanhos = [];
        
        foreach ($orgazinado_por_item as $key => $respostas) {
            $tam = 0;
            for ($i=0; $i < count($respostas["itens"]); $i++) {
                if ($respostas["itens"][$i]["item_id"] == $atual) {
                    unset($orgazinado_por_item[$key]["itens"][$i]);
                } else {
                    $atual = $respostas["itens"][$i]["item_id"];
                    $tam = $tam + $respostas["itens"][$i]["cord"][1];
                    $orgazinado_por_item[$key]["itens"][$i]["opts"] = array_filter($opcoes[$respostas["itens"][$i]["item_id"]]);
                }
            }
            $orgazinado_por_item[$key]["tam"] = $tam;
            $orgazinado_por_item[$key]["itens"] = array_values($orgazinado_por_item[$key]["itens"]);
        }
        
        return $orgazinado_por_item;
    }

    public function get_main_logic($orgazinado_por_resp, $tipo, $tipo_chave = 1)
    {

        // para todos menos o relato
        if ($tipo == 1) {
            $opcoes = [];
            $orgazinado_por_item = [];
            foreach ($orgazinado_por_resp as $key => $value) {
                if ($tipo_chave == 1) {
                    $key = $value[0]["registro"];
                } else {
                    $key = $value[0]["registro"].$value[0]["subregistro"];
                }
                if ($key == "F900" && in_array($value[0]["subregistro"], ["CRP30", "CRP31", "AL05"])) {
                    $key = $value[0]["registro"].$value[0]["subregistro"];
                }

                if ($value[0]["visivel_cliente"] == 1) {
                    $orgazinado_por_item[$key] = ["resp_id"    => $value[0]["resp_id"],
                        "titulo"                        => $value[0]["titulo"],
                        "grupo_resposta"        => $value[0]["grupo_resposta"],
                        "qtde_agrupar"          => $value[0]["qtde_agrupar"],
                        "registro"                  => $value[0]["registro"],
                        "flag_restricao"        => $value[0]["flag_restricao"],
                        "ordem"                         => $value[0]["ordem"],
                        "subregistro"           => $value[0]["subregistro"],
                        "tam"                           => $value[0]["tam"],
                        "visivel_cliente"   => $value[0]["visivel_cliente"],
                        "informativo"           => $value[0]["informativo"]];


                    $itens = [];
                    for ($i=0; $i < count($value); $i++) {
                        if (trim($value[$i]["itens"]["itemRespostaDataCancel"]) == "1901-01-01 00:00:00" && $value[$i]["itens"]["visivel_cliente"] == 1) {
                            $itens[] = $value[$i]["itens"];
                            if ($value[$i]["itens"]["item_id"] != "") {
                                $chave_atual = key($value[$i]["itens"]["opts"]);
                                $opcoes[$value[$i]["itens"]["item_id"]][$chave_atual] = $value[$i]["itens"]["opts"][$chave_atual];
                            }
                        }
                    }

                    $orgazinado_por_item[$key]["itens"] = $itens;
                }
            }
        } else {

            //Relato
            $opcoes = [];
            $orgazinado_por_item = [];
            foreach ($orgazinado_por_resp as $key => $value) {
                if (strlen($value[0]["subregistro"]) == 2) {
                    $str = "0".substr($value[0]["subregistro"], 0, 1)."0".substr($value[0]["subregistro"], 1, 1);
                } elseif (strlen($value[0]["subregistro"]) == 3) {
                    $str = "0".substr($value[0]["subregistro"], 0, 1).substr($value[0]["subregistro"], 1, 2);
                } elseif (strlen($value[0]["subregistro"]) == 3) {
                    $str = substr($value[0]["subregistro"], 0, 2).substr($value[0]["subregistro"], 1, 2);
                } else {
                    $str = $value[0]["subregistro"];
                }
                $key = str_pad($value[0]["registro"], 2, "0", STR_PAD_LEFT).$str;

                if ($value[0]["visivel_cliente"] == 1) {
                    $orgazinado_por_item[$key] = ["resp_id"    => $value[0]["resp_id"],
                        "titulo"                        => $value[0]["titulo"],
                        "grupo_resposta"        => $value[0]["grupo_resposta"],
                        "qtde_agrupar"          => $value[0]["qtde_agrupar"],
                        "registro"                  => $value[0]["registro"],
                        "flag_restricao"        => $value[0]["flag_restricao"],
                        "ordem"                         => $value[0]["ordem"],
                        "subregistro"           => $value[0]["subregistro"],
                        "tam"                           => $value[0]["tam"],
                        "visivel_cliente"   => $value[0]["visivel_cliente"],
                        "informativo"           => $value[0]["informativo"]];


                    $itens = [];
                    for ($i=0; $i < count($value); $i++) {
                        if (trim($value[$i]["itens"]["itemRespostaDataCancel"]) == "1901-01-01 00:00:00" && $value[$i]["itens"]["visivel_cliente"] == 1) {
                            $itens[] = $value[$i]["itens"];
                            if ($value[$i]["itens"]["item_id"] != "") {
                                $chave_atual = key($value[$i]["itens"]["opts"]);
                                $opcoes[$value[$i]["itens"]["item_id"]][$chave_atual] = $value[$i]["itens"]["opts"][$chave_atual];
                            }
                        }
                    }

                    $orgazinado_por_item[$key]["itens"] = $itens;
                }
            }
        }

        return ["orgazinado_por_item" => $orgazinado_por_item, "opcoes" => $opcoes];
    }
}
