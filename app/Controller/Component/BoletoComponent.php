<?php

class BoletoComponent extends Component{
	public function gerar($dados, $link, $id, $return_linha = false){
		$Income = ClassRegistry::init('Income');

		// ------------------------- DADOS DINÂMICOS DO SEU CLIENTE PARA A GERAÇÃO DO BOLETO (FIXO OU VIA GET) -------------------- //
		// Os valores abaixo podem ser colocados manualmente ou ajustados p/ formulário c/ POST, GET ou de BD (MySql,Postgre,etc)	//

		// DADOS DO BOLETO PARA O SEU CLIENTE
		$dias_de_prazo_para_pagamento = 5;
		$taxa_boleto = 0;
		$data_venc = $dados['vencimento'];  // Prazo de X dias OU informe data: "13/04/2006";
		$valor_cobrado = ($dados['valor'] + $dados['valor_multa'] + $dados['valor_juros']); // Valor - REGRA: Sem pontos na milhar e tanto faz com "." ou "," ou com 1 ou 2 ou sem casa decimal
		$valor_cobrado = str_replace(",", ".",$valor_cobrado);
		$valor_boleto=number_format($valor_cobrado+$taxa_boleto, 2, ',', '');

		// Composição Nosso Numero - CEF SIGCB
		$dadosboleto["nosso_numero1"] = "000"; // tamanho 3
		$dadosboleto["nosso_numero_const1"] = "1"; //constanto 1 , 1=registrada , 2=sem registro
		$dadosboleto["nosso_numero2"] = "000"; // tamanho 3
		$dadosboleto["nosso_numero_const2"] = "4"; //constanto 2 , 4=emitido pelo proprio cliente
		$dadosboleto["nosso_numero3"] = str_pad($dados['income_id'], 9, '0', STR_PAD_LEFT); // tamanho 9


		$dadosboleto["numero_documento"] = $dados['income_id'];	// Num do pedido ou do documento
		$dadosboleto["data_vencimento"] = strtotime(str_replace('/', '-', $data_venc)) > strtotime(date('d-m-Y')) ? $data_venc : date('d/m/Y'); // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
		$dadosboleto["data_documento"] = date("d/m/Y"); // Data de emissão do Boleto
		$dadosboleto["data_processamento"] = date("d/m/Y"); // Data de processamento do boleto (opcional)
		$dadosboleto["valor_boleto"] = $valor_boleto; 	// Valor do Boleto - REGRA: Com vírgula e sempre com duas casas depois da virgula

		// DADOS DO SEU CLIENTE
		$dadosboleto["sacado"]    = utf8_decode($dados['nome_fantasia'])." - CPF/CNPJ: ".$dados['cnpj'];//"WALMICK GONCALVES SANTIAGO - CPF/CNPJ: 016.654.225-30";
		$dadosboleto["endereco1"] = utf8_decode($dados['endereco'].", ".$dados['bairro']);//RUA AUGUSTO SEIXAS, 109";
		$dadosboleto["endereco2"] = utf8_decode($dados['cidade'])." - ".$dados['estado']." - CEP : ".$dados['cep'];//"	 VITORIA DA CONQUISTA - BA - CEP: 45020-705";

		// INFORMACOES PARA O CLIENTE
		$dadosboleto["demonstrativo1"] = "Pagamento de Fatura";
		$dadosboleto["demonstrativo2"] = "credcheck.com.br";
		$dadosboleto["demonstrativo3"] = "SAC CAIXA: 0800 726 0101 (Informações, reclamações, sugestões e elogios). <br>Para pessoas com deficiência auditiva ou de fala: 0800 726 2492. Ouvidoria: 0800 725 7474. caixa.gov.br";

		// INSTRUÇÕES PARA O CAIXA
		//$dadosboleto["instrucoes1"] = ($dados['cobrar_juros'] == 'S') ? utf8_decode("- Sr. Caixa, cobrar multa de 2% e juros de 0.333% ao dia após o vencimento"):"";
		$dadosboleto["instrucoes1"] = "";
		$dadosboleto["instrucoes2"] = "";//"- Receber até 5 dias após o vencimento";
		$dadosboleto["instrucoes3"] = utf8_decode("- Em caso de dúvidas entre em contato conosco: financeiro@credcheck.com.br");
		$dadosboleto["instrucoes4"] = utf8_decode("Credcheck Informações Cadastrais LTDA EPP");
		$dadosboleto["instrucoes5"] = utf8_decode("4020-7705 Para capitais e regiões metropolitanas");
		$dadosboleto["instrucoes6"] = utf8_decode("0800 606 8117 Demais localidades");
		$dadosboleto["instrucoes7"] = utf8_decode("credcheck.com.br");

		// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
		$dadosboleto["quantidade"] = "";
		$dadosboleto["valor_unitario"] = "";
		$dadosboleto["aceite"] = "N";
		$dadosboleto["especie"] = "R$";
		$dadosboleto["especie_doc"] = "DS";

		// ---------------------- DADOS FIXOS DE CONFIGURAÇÃO DO SEU BOLETO --------------- //

		// DADOS DA SUA CONTA - CEF
		$dadosboleto["agencia"] = "4160"; // Num da agencia, sem digito
		$dadosboleto["conta"] = "2086"; 	// Num da conta, sem digito
		$dadosboleto["conta_dv"] = "0"; 	// Digito do Num da conta

		// DADOS PERSONALIZADOS - CEF
		$dadosboleto["conta_cedente"] = "382196"; // C?digo Cedente do Cliente, com 6 digitos (Somente N?meros)
		$dadosboleto["carteira"] = "RG";  // C?digo da Carteira: pode ser SR (Sem Registro) ou RG (Com Registro) - (Confirmar com gerente qual usar)

		// SEUS DADOS
		$dadosboleto["identificacao"] = utf8_decode("Credcheck Informações Cadastrais LTDA EPP");
		$dadosboleto["cpf_cnpj"] = utf8_decode("14.438.607/0001-62");
		$dadosboleto["endereco"] = utf8_decode("RUA MONSENHOR OLIMPIO - 61 - AND3 SL301 - CENTRO");
		$dadosboleto["cidade_uf"] = utf8_decode("VITORIA DA CONQUISTA / BA - CEP 45.000.360");
		$dadosboleto["cedente"] = utf8_decode("Credcheck Informações Cadastrais LTDA EPP");

		// NÃO ALTERAR!
		include("includes_boleto/funcoes_cef_sigcb.php"); 

		// $Income->save(['Income' => ['id' => $id, 'nosso_numero' => $dadosboleto['nosso_numero'], 'doc_num' => $dadosboleto['nosso_numero']]]);

		if ($return_linha == 'retorna_linha_digitavel') {
			return $dadosboleto["linha_digitavel"];
		} else if ($return_linha == 'retorna_html_boleto') {
			$link = APP.'webroot';
			include("includes_boleto/layout_cef.php");
			return $html_boleto_pdf;
		} else {
			include("includes_boleto/layout_cef.php");
			echo $html_boleto_pdf;
		}
	}
}