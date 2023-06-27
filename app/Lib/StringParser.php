<?php
App::uses('Controller', 'Controller');
class StringParser extends Controller
{
    public $uses = ['ProductMapping', 'Resposta', 'ItensResposta', 'ItensOpcoes'];

    public $type = 'fisica';

    public function parseCrednet($string, $productID)
    {
        $allowed_answers = $this->getAllowedAnswers($productID);

        $ret = [];
        foreach ($allowed_answers as $key => $answers) {
            $k = $answers['key'];
            if (!isset($ret[$k])) {
                $ret[$k] = [];
            }

            foreach ($answers['items'] as $key => $answer) {

                $cod = $answer[0]['cod'];
                $cod = substr($cod, -2) == 'na' ? substr($cod, 0, 4) : $cod;
                $p = explode($cod, $string);

                if(count($p) > 1){
                    unset($p[0]);
                    $cont_ped = count($p);

                    $itens = $this->ItensResposta->find('all', [
                        'conditions' => [
                            'ItensResposta.respostaID' => $answer['Resposta']['respostaID'],
                            'ItensResposta.itemRespostaVisivelCliente' => 1
                        ],
                        'recursive' => -1
                    ]);

                    if(!isset($ret[$k]['grupo'])){
                        $ret[$k] = ['grupo' => ucfirst($answer['Resposta']['title'])];
                    }

                    $g_key = 0;
                    foreach ($p as $key => $apearances) {
                        $str = $cod.$apearances;

                        foreach ($itens as $key => $item) {

                            $ini = $item['ItensResposta']['itemRespostaInicio'] - 1;
                            $tam = (int)$item['ItensResposta']['itemRespostaByte'];
                            $val = trim(substr($str, $ini, $tam));
                            
                            if (strpos($item['ItensResposta']['itemRespostaNome'], '§') !== false) {
                                $pcs = explode('§', $item['ItensResposta']['itemRespostaNome']);
                                $k_num = $this->type == 'fisica' ? 0 : 1;
                                $key_description = $pcs[$k_num];
                            } else {
                                $key_description = $item['ItensResposta']['itemRespostaNome'];
                            }

                            $sub_key = $this->clear_string($key_description);

                            if($item['ItensResposta']['itemRespostaMultivalorado'] == 1){
                                $opts = $this->ItensOpcoes->find('all', [
                                    'conditions' => ['ItensOpcoes.itemRespostaID' => $item['ItensResposta']['itemRespostaID']]
                                ]);

                                foreach ($opts as $opt) {
                                    if($opt['ItensOpcoes']['itemOpcaoCodigo'] == $val){
                                        $val = $opt['ItensOpcoes']['itemOpcaoNome'];
                                        break;
                                    }
                                }
                            }

                            if($cont_ped > 1){
                                if(!isset($ret[$k]['dados'][$g_key])){
                                    $ret[$k]['dados'][$g_key] = [];
                                }

                                $content = ['titulo' => trim($key_description), 'valor' => $val];
                                
                                $ret[$k]['dados'][$g_key][$sub_key] = $content;
                            } else {
                                $ret[$k]['dados'][$sub_key] = ['titulo' => trim($key_description), 'valor' => $val];
                            }

                        }

                        $g_key = $g_key + 1;
                    }
                }
            }

            if(empty($ret[$k])){
                unset($ret[$k]);
            }
        }

        return $ret;
    }

    private function getAllowedAnswers($productID)
    {
        $allowed_answers = $this->ProductMapping->find('all', [
            'conditions' => [
                'ProductMapping.product_id' => $productID
            ],
            'order' => ['ProductMapping.parent_map_id' => 'asc']
        ]);

        $ret = [];
        $groups = [];
        foreach ($allowed_answers as $key => $answer) {
            if($answer['ProductMapping']['parent_map_id'] == 0){
                $groups[$answer['ProductMapping']['id']]['cods'][] = $answer['ProductMapping']['cod'];
                $groups[$answer['ProductMapping']['id']]['key'] = $answer['ProductMapping']['key_name'];
            } else {
                $groups[$answer['ProductMapping']['parent_map_id']]['cods'][] = $answer['ProductMapping']['cod'];
            }
        }

        foreach ($groups as $key => $group) {

            $group['cods'] = array_reverse($group['cods']);

            $answers = $this->Resposta->find('all', [
                'fields' => [
                    "concat(LPAD(Resposta.respostaRegistro, 4, '0'), Resposta.respostaSubtipo) as cod",
                    "Resposta.respostaNome as title",
                    "Resposta.respostaID"
                ],
                'conditions' => [
                    'Resposta.produtoID' => $productID,
                    "concat(LPAD(respostaRegistro, 4, '0'), respostaSubtipo)" => $group['cods'],
                    'Resposta.respostaVisivelCliente' => 1
                ],
                'order' => ["concat(LPAD(Resposta.respostaRegistro, 4, '0'), Resposta.respostaSubtipo)" => 'asc']
            ]);

            $groups[$key]['items'] = $answers;
            unset($groups[$key]['cods']);
        }

        return $groups;
    }

    private function clear_string($str){
        $cleared_str = Inflector::slug($str, '_');
        return str_replace(['_de_', '_da_', '_do_'], '_', $cleared_str);
    }

}