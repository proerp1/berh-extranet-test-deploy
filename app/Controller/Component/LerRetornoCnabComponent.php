<?php
	class LerRetornoCnabComponent extends Component {
		
		public function ler($id, $arquivo){
			$RetornoCnab = ClassRegistry::init('RetornoCnab');
			$Income = ClassRegistry::init('Income');
			$TmpRetornoCnab = ClassRegistry::init('TmpRetornoCnab');
			$CaixaCodMovimento = ClassRegistry::init('CaixaCodMovimento');

			$RetornoCnab->id = $id;

			$fp = fopen($arquivo, "r");
			$tipo_arquivo = '';
			$lote = '';
			$data_arquivo = '';
			$data = array();

			while (!feof($fp)){
				$linha = fgets($fp, 4096);
				$valor_pago = '';
				$valor_liquido = '';
				$data_pagamento = '';

				//HEADER
				if (substr($linha,7,1 ) == 0){
					$banco = substr($linha,102,30);
					$data_arquivo = substr($linha,143,8);
					$data_arquivo = substr($data_arquivo,4,4).'-'.substr($data_arquivo,2,2).'-'.substr($data_arquivo,0,2);
				}

				if (substr($linha,7,1 ) == 1){
					// $lote = substr($linha,3,4);
					$lote = substr($linha,183,8);
					$tipo_arquivo = substr($linha,8,1);

					if(trim($banco) != "C ECON FEDERAL" && $tipo_arquivo != 'T'){
						$data['data_cancel'] = date('Y-m-d H:i:s');
						$data['usuario_id_cancel'] = CakeSession::read('Auth.User.id');
					
						$is_valid = false;
						$mensagem = "Arquivo invalido.";
					} else if($RetornoCnab->verifica_arquivo_enviado($id, $data_arquivo, $lote)){
						$data['data_cancel'] = date('Y-m-d H:i:s');
						$data['usuario_id_cancel'] = CakeSession::read('Auth.User.id');
					
						$is_valid = false;
						$data_arquivo = date('d/m/Y', strtotime($data_arquivo));
						$mensagem = "Este arquivo já foi enviado. Lote: ".$lote.", data: ".$data_arquivo;          	
					} else {
						$data['data_arquivo'] = $data_arquivo;
						$data['lote'] = $lote;

						$is_valid = true;
						$mensagem = "Upload efetuado com sucesso.";
					}
				}

				if (substr($linha,7,1 ) == 3){
					if (substr($linha,13,1 ) == 'T'){
						$nosso_numero = substr($linha,39,18);
						$codigo_movimento = substr($linha,15,2);
						$codigo = $CaixaCodMovimento->find('first', ['conditions' => ['CaixaCodMovimento.codigo' => $codigo_movimento], 'recursive' => -1]);
						$conta = $Income->find('first', ['conditions' => ["REPLACE(Income.nosso_numero,'-','')" => $nosso_numero], 'recursive' => -1, 'callbacks' => false]);

						$vencimento = substr($linha,73,8);
						$vencimento = substr($vencimento,4,4).'-'.substr($vencimento,2,2).'-'.substr($vencimento,0,2);
					}

					if (substr($linha,13,1 ) == 'U'){
						$valor_pago = substr($linha,77,15);
						$valor_pago = number_format(substr($valor_pago, 0, -2).'.'.substr($valor_pago, -2),2,'.','');

						$valor_liquido = substr($linha,92,15);
						$valor_liquido = substr($valor_liquido, 0, -2).'.'.substr($valor_liquido, -2);

						$data_pagamento = substr($linha,137,8);
						$data_pagamento = substr($data_pagamento,4,4).'-'.substr($data_pagamento,2,2).'-'.substr($data_pagamento,0,2);

						if (empty($conta)) {
							$encontrado = 2;
							$income_id = '';
						} else {
							$encontrado = 1;
							$income_id = $conta['Income']['id'];
						}

						if ($vencimento != '0000-00-00') {
							$data_tmp = ['TmpRetornoCnab' => ['retorno_cnab_id' => $id, 'data_pagamento' => $data_pagamento, 'income_id' => $income_id, 'nosso_numero' => $nosso_numero, 'caixa_cod_movimento_id' => $codigo['CaixaCodMovimento']['id'], 'encontrado' => $encontrado, 'vencimento' => $vencimento, 'valor_pago' => $valor_pago, 'valor_liquido' => $valor_liquido, 'user_created_id' => CakeSession::read('Auth.User.id')]];

							$TmpRetornoCnab->create();
							$TmpRetornoCnab->save($data_tmp);
						}
					}
				}
			}

			$RetornoCnab->save($data);

			if ($is_valid == false) {
				unlink($arquivo);
			}

			return array("is_valid" => $is_valid, "mensagem" => $mensagem);
		}
	}
?>