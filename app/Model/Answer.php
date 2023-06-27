<?php 
App::uses('AuthComponent', 'Controller/Component');
class Answer extends AppModel {
  public $name = 'Answer';

  public $hasMany = array(
    'AnswerItem' => array(
      'className' => 'AnswerItem',
      'foreignKey' => 'answer_id',
      'conditions' => array('AnswerItem.data_cancel' => '1901-01-01 00:00:00')
    )
  );

  public $belongsTo = array(
    'Product' => array(
      'className' => 'Product',
      'foreignKey' => 'product_id',
      'conditions' => array('Product.data_cancel' => '1901-01-01 00:00:00', 'Product.status_id' => 1)
    )
  );

  public function beforeFind($queryData) {

    $queryData['conditions'][] = array('Answer.data_cancel' => '1901-01-01 00:00:00');
    
    return $queryData;
  }

  public function commom_task_string($produto_id, $tipo){

    $result = $this->query("SELECT r.id,
                                    r.name,
                                    r.pai_id,
                                    r.qtde_colunas,
                                    r.registro,
                                    r.flag_feature,
                                    r.subtipo,
                                    r.numero_ordem,
                                    r.flag_restricao,
                                    r.visivel_cliente,
                                    i.id,
                                    i.name,
                                    i.formatacao,
                                    i.multivalorado,
                                    i.visivel_cliente,
                                    i.msg_personalizada,
                                    i.inicio,
                                    i.byte,
                                    i.data_cancel,
                                    io.codigo,
                                    io.name
                              FROM answers r
                                INNER JOIN answer_items i on i.answer_id = r.id
                                LEFT JOIN item_options io on i.id = io.answer_item_id
                              WHERE r.product_id = ".$produto_id." AND r.data_cancel = '1901-01-01'");

    foreach ($result as $res) {
      $resposta[] = array("resp_id"             => $res['r']['id'],
                            "titulo"            => $res['r']['name'],
                            "grupo_resposta"    => $res['r']['pai_id'],
                            "qtde_agrupar"      => $res['r']['qtde_colunas'],
                            "registro"          => $res['r']['registro'],
                            "flag_restricao"    => $res['r']['flag_restricao'],
                            "flag_feature"      => $res['r']['flag_feature'],
                            "ordem"             => $res['r']['numero_ordem'],
                            "subregistro"       => $res['r']['subtipo'],
                            "tam"               => 0,
                            "visivel_cliente"   => $res['r']['visivel_cliente'],
                            "itens" => array("cord"                       => array($res['i']['inicio'], $res['i']['byte']),
                                              "item_id"                   => $res['i']['id'],
                                              "nome"                      => $res['i']['name'],
                                              "formato"                   => $res['i']['formatacao'],
                                              "multivalorado"             => $res['i']['multivalorado'],
                                              "itemRespostaDataCancel"    => $res['i']['data_cancel'],
                                              "visivel_cliente"           => $res['i']['visivel_cliente'],
                                              "msg_person"                => $res['i']['msg_personalizada'],
                                              "opts"                      => array($res['io']['codigo']      => $res['io']['name'])
                                            )
                          );
    }

    $opts = array();

    // seraparndo por resposta
    $orgazinado_por_resp = array();
    foreach ($resposta as $key => $value) {
      $orgazinado_por_resp[$value["resp_id"]][] = $value;
    }

    return $orgazinado_por_resp;
  }

  public function get_respostas_por_produto($produto_id, $tipo = 1, $tipo_chave = 1){
    $orgazinado_por_resp = $this->commom_task_string($produto_id, $tipo);

    // seraparndo por item
    $itens = $this->get_main_logic($orgazinado_por_resp, $tipo, $tipo_chave);
    $orgazinado_por_item = $itens["orgazinado_por_item"];
    $opcoes = $itens["opcoes"];

    // seraparndo por opcoes
    $atual = 0;
    $tamanhos = array();
    foreach ($orgazinado_por_item as $key => $respostas) {
      $tam = 0;
      for ($i=0; $i < count($respostas["itens"]); $i++) {
        if($respostas["itens"][$i]["item_id"] == $atual){
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

  public function get_main_logic($orgazinado_por_resp, $tipo, $tipo_chave = 1){

    // para todos menos o relato
    if($tipo == 1){
      $opcoes = array();
      $orgazinado_por_item = array();
      foreach ($orgazinado_por_resp as $key => $value) {
        if($tipo_chave == 1){
          $key = $value[0]["registro"];
        } else {
          $key = $value[0]["registro"].$value[0]["subregistro"];
        }
        if($key == "F900" && in_array($value[0]["subregistro"], array("CRP30", "CRP31", "AL05"))){
          $key = $value[0]["registro"].$value[0]["subregistro"];
        }

        if($value[0]["visivel_cliente"] == 1){
          $orgazinado_por_item[$key] = array("resp_id"  => $value[0]["resp_id"],
                                                              "titulo"            => $value[0]["titulo"],
                                                              "grupo_resposta"    => $value[0]["grupo_resposta"],
                                                              "flag_feature"    => $value[0]["flag_feature"],
                                                              "qtde_agrupar"      => $value[0]["qtde_agrupar"],
                                                              "registro"          => $value[0]["registro"],
                                                              "flag_restricao"    => $value[0]["flag_restricao"],
                                                              "ordem"             => $value[0]["ordem"],
                                                              "subregistro"       => $value[0]["subregistro"],
                                                              "tam"               => $value[0]["tam"],
                                                              "visivel_cliente"   => $value[0]["visivel_cliente"]);


        $itens = array();
        for ($i=0; $i < count($value); $i++) {
          if(trim($value[$i]["itens"]["itemRespostaDataCancel"]) == "1901-01-01 00:00:00" && $value[$i]["itens"]["visivel_cliente"] == 1){
            $itens[] = $value[$i]["itens"];
            if($value[$i]["itens"]["item_id"] != ""){
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
      $opcoes = array();
      $orgazinado_por_item = array();
      foreach ($orgazinado_por_resp as $key => $value) {
        if(strlen($value[0]["subregistro"]) == 2){
          $str = "0".substr($value[0]["subregistro"], 0, 1)."0".substr($value[0]["subregistro"], 1, 1);
        } elseif(strlen($value[0]["subregistro"]) == 3){
          $str = "0".substr($value[0]["subregistro"], 0, 1).substr($value[0]["subregistro"], 1, 2);
        } elseif(strlen($value[0]["subregistro"]) == 3){
          $str = substr($value[0]["subregistro"], 0, 2).substr($value[0]["subregistro"], 1, 2);
        } else {
          $str = $value[0]["subregistro"];
        }
        $key = str_pad($value[0]["registro"], 2, "0", STR_PAD_LEFT).$str;

        if($value[0]["visivel_cliente"] == 1){
          $orgazinado_por_item[$key] = array("resp_id"  => $value[0]["resp_id"],
                                                              "titulo"            => $value[0]["titulo"],
                                                              "grupo_resposta"    => $value[0]["grupo_resposta"],
                                                              "qtde_agrupar"      => $value[0]["qtde_agrupar"],
                                                              "flag_feature"      => $value[0]["flag_feature"],
                                                              "registro"          => $value[0]["registro"],
                                                              "flag_restricao"    => $value[0]["flag_restricao"],
                                                              "ordem"             => $value[0]["ordem"],
                                                              "subregistro"       => $value[0]["subregistro"],
                                                              "tam"               => $value[0]["tam"],
                                                              "visivel_cliente"   => $value[0]["visivel_cliente"]);


        $itens = array();
        for ($i=0; $i < count($value); $i++) {
          if(trim($value[$i]["itens"]["itemRespostaDataCancel"]) == "1901-01-01 00:00:00" && $value[$i]["itens"]["visivel_cliente"] == 1){
            $itens[] = $value[$i]["itens"];
            if($value[$i]["itens"]["item_id"] != ""){
              $chave_atual = key($value[$i]["itens"]["opts"]);
              $opcoes[$value[$i]["itens"]["item_id"]][$chave_atual] = $value[$i]["itens"]["opts"][$chave_atual];
            }
          }
        }

        $orgazinado_por_item[$key]["itens"] = $itens;

        }



      }
    }

    return array("orgazinado_por_item" => $orgazinado_por_item, "opcoes" => $opcoes);
  }
}