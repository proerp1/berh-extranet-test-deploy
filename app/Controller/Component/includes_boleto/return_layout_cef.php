<?php
// +----------------------------------------------------------------------+
// | BoletoPhp - Versão Beta                                              |
// +----------------------------------------------------------------------+
// | Este arquivo está disponível sob a Licença GPL disponível pela Web   |
// | em http://pt.wikipedia.org/wiki/GNU_General_Public_License           |
// | Você deve ter recebido uma cópia da GNU Public License junto com     |
// | esse pacote; se não, escreva para:                                   |
// |                                                                      |
// | Free Software Foundation, Inc.                                       |
// | 59 Temple Place - Suite 330                                          |
// | Boston, MA 02111-1307, USA.                                          |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Originado do Projeto BBBoletoFree que tiveram colaborações de Daniel |
// | William Schultz e Leandro Maniezo que por sua vez foi derivado do	  |
// | PHPBoleto de João Prado Maia e Pablo Martins F. Costa				        |
// | 														                                   			  |
// | Se vc quer colaborar, nos ajude a desenvolver p/ os demais bancos :-)|
// | Acesse o site do Projeto BoletoPhp: www.boletophp.com.br             |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Equipe Coordenação Projeto BoletoPhp: <boletophp@boletophp.com.br>   |
// | Desenvolvimento Boleto CEF: Elizeu Alcantara                         |
// +----------------------------------------------------------------------+

$bar_codes = fbarcode2($dadosboleto["codigo_barras"], APP.'webroot');

$html_boleto_pdf = '
<html>
	<head>
		<title>'.$dadosboleto["identificacao"].'</title>
		<meta http-equiv=Content-Type content=text/html charset=ISO-8859-1>
	</head>

	<body style="color: rgb(0, 0, 0);background-color: rgb(255, 255, 255);margin-right: 0px;margin-top: 0px;">
		<table style="width:666px;border:0px" cellspacing=0 cellpadding=0>
			<tr>
				<td style="vertical-align=top;font: bold 10px Arial; color: black">
					<div style="text-align:center">Instruções de Impressão</div>
				</td>
			</tr>
			<tr>
				<td style="vertical-align=top;font: bold 10px Arial; color: black">
					<div style="text-align:-webkit-left">
						<p>
							<li>Imprima em impressora jato de tinta (ink jet) ou laser em qualidade normal ou alta (Não use modo econômico).<br>
							<li>Utilize folha A4 (210 x 297 mm) ou Carta (216 x 279 mm) e margens mínimas à esquerda e à direita do formulário.<br>
							<li>Corte na linha indicada. Não rasure, risque, fure ou dobre a região onde se encontra o código de barras.<br>
							<li>Caso não apareça o código de barras no final, clique em F5 para atualizar esta tela.
							<li>Caso tenha problemas ao imprimir, copie a seqüencia numérica abaixo e pague no caixa eletrônico ou no internet banking:<br><br>
							<span style="font: bold 12px Arial; color: #000000">
								&nbsp;&nbsp;&nbsp;&nbsp;Linha Digitável: &nbsp;'.$dadosboleto["linha_digitavel"].'<br>
								&nbsp;&nbsp;&nbsp;&nbsp;Valor: &nbsp;&nbsp;R$ '.$dadosboleto["valor_boleto"].'<br>
							</span>
						</p>
					</div>
				</td>
			</tr>
		</table>
		<br>
		<table style="width:666px;border:0px" cellspacing=0 cellpadding=0>
			<tbody>
				<tr>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;width:666px;">
						<img style="height:1px;width:665px;border:0px" src='.APP.'webroot/img/boleto_imgs/6.png>
					</td>
				</tr>
				<tr>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;width:666px;">
						<div style="text-align:-webkit-right">
							<b style="font: bold 10px Arial; color: black">Recibo do Sacado</b>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
		<table style="width:666px;border:0px" cellspacing=5 cellpadding=0><tr><td style="width:41px"></td></tr></table>
		<table style="width:666px;border:0px;text-align:inherit" cellspacing=5 cellpadding=0>
			<tr>
				<td style="width:41px"><img src="'.APP.'webroot/img/logo.png" style="width:150px;margin-right:20px"></td>
				<td style="font: 9px Arial, Helvetica, sans-serif;width:455px">
					'.$dadosboleto["identificacao"].' '.(isset($dadosboleto["cpf_cnpj"]) ? "<br>".$dadosboleto["cpf_cnpj"] : '').'<br>
					'.utf8_decode($dadosboleto["endereco"]).'<br>
					'.$dadosboleto["cidade_uf"].'<br>
				</td>
				<td style="text-align:-webkit-right;width:150px;font: 9px Arial, Helvetica, sans-serif">&nbsp;</td>
			</tr>
		</table>
		<br>
		<table style="width:666px;border:0px" cellspacing=0 cellpadding=0>
			<tr>
				<td style="font: bold 10px Arial; color: black;width:150px">
					<span class="campo">
						<img src="'.APP.'webroot/img/boleto_imgs/logocaixa.jpg" style="width:150px;height:40px;border:0px">
					</span>
				</td>
				<td style="width:3px;vertical-align=bottom">
					<img style="height:22px;width:2px;border:0px" src='.APP.'webroot/img/boleto_imgs/3.png>
				</td>
				<td style="font: bold 10px Arial; color: black;width:58px;vertical-align:bottom">
					<div style="text-align:-webkit-center">
						<font style="font: bold 20px Arial; color: #000000">'.$dadosboleto["codigo_banco_com_dv"].'</font>
					</div>
				</td>
				<td style="width:3px;vertical-align=bottom">
					<img style="height:22px;width:2px;border:0px" src='.APP.'webroot/img/boleto_imgs/3.png>
				</td>
				<td style="font: bold 15px Arial; color: #000000;text-align:-webkit-right;width:453px;vertical-align:bottom">
					<span style="font: bold 15px Arial; color: #000000"> 
					<span class="campotitulo">'.$dadosboleto["linha_digitavel"].'</span>
				</td>
			</tr>
			<tbody>
				<tr>
					<td colspan=5>
						<img style="height:2px;width:666px;border:0px" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
				</tr>
			</tbody>
		</table>
		<table cellspacing=0 cellpadding=0 style="border:0px">
			<tbody>
				<tr>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:7px;height:13px">
						<img style="height:13px;width:1px;border:0px;" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:268px;height:13px;">Cedente</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:7px;height:13px">
						<img style="height:13px;width:1px;border:0px;" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:156px;height:13px;">Agência/Código do Cedente</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:7px;height:13px">
						<img style="height:13px;width:1px;border:0px;" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:34px;height:13px;">Espécie</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:7px;height:13px">
						<img style="height:13px;width:1px;border:0px;" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:53px;height:13px">Quantidade</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:7px;height:13px">
						<img style="height:13px;width:1px;border:0px;" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:120px;height:13px;">Nosso número</td>
				</tr>
				<tr>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
						<img style="width:1px;border:0px;height:12px" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:268px;height:12px;">
						<span class="campo">'.$dadosboleto["cedente"].'</span>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
						<img style="width:1px;border:0px;height:12px" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:156px;height:12px">
						<span class="campo">'.$dadosboleto["agencia_codigo"].'</span>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
						<img style="width:1px;border:0px;height:12px" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:34px;height:12px">
						<span class="campo">'.$dadosboleto["especie"].'</span>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
						<img style="width:1px;border:0px;height:12px" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:53px;height:12px">
						<span class="campo">'.$dadosboleto["quantidade"].'</span>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
						<img style="width:1px;border:0px;height:12px" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;text-align:-webkit-right;width:120px;height:12px">
						<span class="campo">'.$dadosboleto["nosso_numero"].'</span>
					</td>
				</tr>
				<tr>
					<td style="vertical-align:top;width:7px;height:1px;">
						<img style="height:1px;width:7px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:268px;height:1px;">
						<img style="height:1px;width:268px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:7px;height:1px;">
						<img style="height:1px;width:7px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:156px;height:1px;">
						<img style="height:1px;width:156px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:7px;height:1px;">
						<img style="height:1px;width:7px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td  style="vertical-align:top;width:34px;height:1px;">
						<img style="height:1px;width:34px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:7px;height:1px;">
						<img style="height:1px;width:7px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:53px;height:1px;">
						<img style="height:1px;width:53px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:7px;height:1px;">
						<img style="height:1px;width:7px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:120px;height:1px;">
						<img style="height:1px;width:120px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
				</tr>
			</tbody>
		</table>
		<table cellspacing=0 cellpadding=0 style="border:0px">
			<tbody>
				<tr>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:7px;height:13px">
						<img style="height:13px;width:1px;border:0px;" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;height:13px" colspan=3>Número do documento</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:7px;height:13px">
						<img style="height:13px;width:1px;border:0px;" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:132px;height:13px">CPF/CNPJ</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:7px;height:13px">
						<img style="height:13px;width:1px;border:0px;" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:134px;height:13px">Vencimento</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:7px;height:13px">
						<img style="height:13px;width:1px;border:0px;" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:180px;height:13px">Valor documento</td>
				</tr>
				<tr>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
						<img style="width:1px;border:0px;height:12px" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;height:12px" colspan=3>
						<span class="campo">'.trim($dadosboleto["numero_documento"]).'</span>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
						<img style="width:1px;border:0px;height:12px" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:132px;height:12px;">
						<span class="campo">'.trim($dadosboleto["cpf_cnpj"]).'</span>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
						<img style="width:1px;border:0px;height:12px" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:134px;height:12px;">
						<span class="campo">'.trim(($data_venc != "" ? $dadosboleto["data_vencimento"] : "Contra Apresentação")).'</span>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
						<img style="width:1px;border:0px;height:12px" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top:text-align:-webkit-right;width:180px;height:12px"> 
						<span class="campo">'.trim($dadosboleto["valor_boleto"]).'</span>
					</td>
				</tr>
				<tr>
					<td style="vertical-align:top;width:7px;height:1px;">
						<img style="height:1px;width:7px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:113px;height:1px">
						<img style="height:1px;width:113px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:7px;height:1px;">
						<img style="height:1px;width:7px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:72px;height:1px">
						<img style="height:1px;width:72px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:7px;height:1px;">
						<img style="height:1px;width:7px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:132px;height:1px">
						<img style="height:1px;width:132px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:7px;height:1px;">
						<img style="height:1px;width:7px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:134px;height:1px">
						<img style="height:1px;width:134px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:7px;height:1px;">
						<img style="height:1px;width:7px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:180px;height:1px">
						<img style="height:1px;width:180px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
				</tr>
			</tbody>
		</table>
		<table cellspacing=0 cellpadding=0 style="border:0px">
			<tbody>
				<tr>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:7px;height:13px">
						<img style="height:13px;width:1px;border:0px;" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;valign=top;width:113px;height:13px">(-) Desconto / Abatimentos</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:7px;height:13px">
						<img style="height:13px;width:1px;border:0px;" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;valign=top;width:112px;height:13px">(-) Outras deduções</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:7px;height:13px">
						<img style="height:13px;width:1px;border:0px;" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;valign=top;width:113px;height:13px">(+) Mora / Multa</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:7px;height:13px">
						<img style="height:13px;width:1px;border:0px;" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;valign=top;width:113px;height:13px">(+) Outros acréscimos</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:7px;height:13px">
						<img style="height:13px;width:1px;border:0px;" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;valign=top;width:180px;height:13px">(=) Valor cobrado</td>
				</tr>
				<tr>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
						<img style="width:1px;border:0px;height:12px" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;text-align:-webkit-right;width:113px;height:12px"></td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
						<img style="width:1px;border:0px;height:12px" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;text-align:-webkit-right;width:112px;height:12px"></td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
						<img style="width:1px;border:0px;height:12px" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;text-align:-webkit-right;width:113px;height:12px"></td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
						<img style="width:1px;border:0px;height:12px" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;text-align:-webkit-right;width:113px;height:12px"></td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
						<img style="width:1px;border:0px;height:12px" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top:text-align:-webkit-right;width:180px;height:12px"></td>
				</tr>
				<tr>
					<td style="vertical-align:top;width:7px;height:1px;">
						<img style="height:1px;width:7px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:113px;height:1px">
						<img style="height:1px;width:113px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:7px;height:1px;">
						<img style="height:1px;width:7px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:112px;height:1px">
						<img style="height:1px;width:112px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:7px;height:1px;">
						<img style="height:1px;width:7px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:113px;height:1px">
						<img style="height:1px;width:113px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:7px;height:1px;">
						<img style="height:1px;width:7px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:113px;height:1px">
						<img style="height:1px;width:113px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:7px;height:1px;">
						<img style="height:1px;width:7px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:180px;height:1px">
						<img style="height:1px;width:180px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
				</tr>
			</tbody>
		</table>
		<table cellspacing=0 cellpadding=0 style="border:0px">
			<tbody>
				<tr>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:7px;height:13px">
						<img style="height:13px;width:1px;border:0px;" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:659px;height:13px">Sacado</td>
				</tr>
				<tr>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
						<img style="width:1px;border:0px;height:12px" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:659px;height:12px;">
						<span class="campo">'.$dadosboleto["sacado"].'</span>
					</td>
				</tr>
				<tr>
					<td style="vertical-align:top;width:7px;height:1px;">
						<img style="height:1px;width:7px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:659px;height:1px">
						<img style="height:1px;width:659px;border:0px" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
				</tr>
			</tbody>
		</table>
		<table cellspacing=0 cellpadding=0 style="border:0px">
			<tbody>
				<tr>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;width:7px;height:12px"></td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;width:564px">Demonstrativo</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;width:7px;height:12px"></td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;width:88px">Autenticação mecânica</td>
				</tr>
				<tr>
					<td style="width:7px"></td>
					<td style="font: bold 10px Arial; color: black;width:564px">
						<span class="campo">
							'.$dadosboleto["demonstrativo1"].'<br>
							'.$dadosboleto["demonstrativo2"].'<br>
							'.$dadosboleto["demonstrativo3"].'<br>
						</span>
					</td>
					<td style="width:7px"></td>
					<td style="width:88px"></td>
				</tr>
			</tbody>
		</table>
		<table style="width:666px;border:0px" cellspacing=0 cellpadding=0>
			<tbody>
				<tr>
					<td style="width:7px"></td>
					<td style="width:500px;font: bold 10px Arial; color: black"><br><br><br></td>
					<td style="width:159px"></td>
				</tr>
			</tbody>
		</table>
		<table style="width:666px;border:0px" cellspacing=0 cellpadding=0>
			<tr>
				<td style="font: 9px \'Arial Narrow\'; color: #000033;width:666px;"></td>
			</tr>
			<tbody>
				<tr>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;width:666px;">
						<div style="text-align:-webkit-right">Corte na linha pontilhada</div>
					</td>
				</tr>
				<tr>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;width:666px;">
						<img style="height:1px;width:665px;border:0px" src='.APP.'webroot/img/boleto_imgs/6.png>
					</td>
				</tr>
			</tbody>
		</table>
		<br>
		<table style="width:666px;border:0px" cellspacing=0 cellpadding=0>
			<tr>
				<td style="font: bold 10px Arial; color: black;width:150px">
					<span class="campo">
						<img src="'.APP.'webroot/img/boleto_imgs/logocaixa.jpg" style="width:150px;height:40px;border:0px">
					</span>
				</td>
				<td style="width:3px;vertical-align=bottom">
					<img style="height:22px;width:2px;border:0px" src='.APP.'webroot/img/boleto_imgs/3.png>
				</td>
				<td style="font: bold 10px Arial; color: black;width:58px;vertical-align:bottom">
					<div style="text-align:-webkit-center"><font style="font: bold 20px Arial; color: #000000">'.$dadosboleto["codigo_banco_com_dv"].'</font></div>
				</td>
				<td style="width:3px;vertical-align=bottom"><img style="height:22px;width:2px;border:0px" src='.APP.'webroot/img/boleto_imgs/3.png></td>
				<td style="font: bold 15px Arial; color: #000000;text-align:-webkit-right;width:453px;vertical-align:bottom">
					<span style="font: bold 15px Arial; color: #000000"><span class="campotitulo">'.$dadosboleto["linha_digitavel"].'</span></span>
				</td>
			</tr>
			<tbody>
				<tr>
					<td colspan=5>
						<img style="height:2px;width:666px;border:0px" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
				</tr>
			</tbody>
		</table>
		<table cellspacing=0 cellpadding=0 style="border:0px">
			<tbody>
				<tr>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:7px;height:13px">
						<img style="height:13px;width:1px;border:0px;" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:472px;height:13px">Local de pagamento</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:7px;height:13px">
						<img style="height:13px;width:1px;border:0px;" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:180px;height:13px">Vencimento</td>
				</tr>
				<tr>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
						<img style="width:1px;border:0px;height:12px" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:472px;height:12px">Pagável em qualquer Banco até o vencimento</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
						<img style="width:1px;border:0px;height:12px" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top:text-align:-webkit-right;width:180px;height:12px"> 
						<span class="campo">'.($data_venc != "" ? $dadosboleto["data_vencimento"] : "Contra Apresentação").'</span>
					</td>
				</tr>
				<tr>
					<td style="vertical-align:top;width:7px;height:1px;">
						<img style="height:1px;width:7px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:472px;height:1px">
						<img style="height:1px;width:472px;border:0px" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:7px;height:1px;">
						<img style="height:1px;width:7px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:180px;height:1px">
						<img style="height:1px;width:180px;border:0px" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
				</tr>
			</tbody>
		</table>
		<table cellspacing=0 cellpadding=0 style="border:0px">
			<tbody>
				<tr>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:7px;height:13px">
						<img style="height:13px;width:1px;border:0px;" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:472px;height:13px">Cedente</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:7px;height:13px">
						<img style="height:13px;width:1px;border:0px;" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:180px;height:13px">Agência/Código cedente</td>
				</tr>
				<tr>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
						<img style="width:1px;border:0px;height:12px" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:472px;height:12px"> 
						<span class="campo">'.$dadosboleto["cedente"].'</span>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
						<img style="width:1px;border:0px;height:12px" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top:text-align:-webkit-right;width:180px;height:12px"> 
						<span class="campo">'.$dadosboleto["agencia_codigo"].'</span>
					</td>
				</tr>
				<tr>
					<td style="vertical-align:top;width:7px;height:1px;">
						<img style="height:1px;width:7px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:472px;height:1px">
						<img style="height:1px;width:472px;border:0px" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:7px;height:1px;">
						<img style="height:1px;width:7px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:180px;height:1px">
						<img style="height:1px;width:180px;border:0px" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
				</tr>
			</tbody>
		</table>
		<table cellspacing=0 cellpadding=0 style="border:0px">
			<tbody>
				<tr>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:7px;height:13px">
						<img style="height:13px;width:1px;border:0px;" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;valign=top;width:113px;height:13px">Data do documento</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:7px;height:13px">
						<img style="height:13px;width:1px;border:0px;" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:133px;height:13px">Nº documento</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:7px;height:13px">
						<img style="height:13px;width:1px;border:0px;" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:62px;height:13px">Espécie doc.</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:7px;height:13px">
						<img style="height:13px;width:1px;border:0px;" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:34px;height:13px;">Aceite</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:7px;height:13px">
						<img style="height:13px;width:1px;border:0px;" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:102px;height:13px">Data processamento</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:7px;height:13px">
						<img style="height:13px;width:1px;border:0px;" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:180px;height:13px">Nosso número</td>
				</tr>
				<tr>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
						<img style="width:1px;border:0px;height:12px" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:113px;height:12px">
						<div style="text-align:-webkit-left"><span class="campo">'.$dadosboleto["data_documento"].'</span></div>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
						<img style="width:1px;border:0px;height:12px" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:133px;height:12px">
						<span class="campo">'.$dadosboleto["numero_documento"].'</span>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
						<img style="width:1px;border:0px;height:12px" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:62px;height:12px">
						<div style="text-align:-webkit-left"><span class="campo">'.$dadosboleto["especie_doc"].'</span></div>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
						<img style="width:1px;border:0px;height:12px" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:34px;height:12px">
						<div style="text-align:-webkit-left"><span class="campo">'.$dadosboleto["aceite"].'</span></div>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
						<img style="width:1px;border:0px;height:12px" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:102px;height:12px">
						<div style="text-align:-webkit-left"><span class="campo">'.$dadosboleto["data_processamento"].'</span></div>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
						<img style="width:1px;border:0px;height:12px" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top:text-align:-webkit-right;width:180px;height:12px">
						<span class="campo">'.$dadosboleto["nosso_numero"].'</span>
					</td>
				</tr>
				<tr>
					<td style="vertical-align: top;width:7px;height:1px">
						<img style="border:0px;width:7px;height:1px" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:113px;height:1px">
						<img style="height:1px;width:113px;border:0px" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:7px;height:1px;">
						<img style="height:1px;width:7px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:133px;height:1px">
						<img style="height:1px;width:133px;border:0px" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:7px;height:1px;">
						<img style="height:1px;width:7px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:62px;height:1px">
						<img style="height:1px;width:62px;border:0px" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:7px;height:1px;">
						<img style="height:1px;width:7px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td  style="vertical-align:top;width:34px;height:1px;">
						<img style="height:1px;" src='.APP.'webroot/img/boleto_imgs/2.png width=34 border=0>
					</td>
					<td style="vertical-align:top;width:7px;height:1px;">
						<img style="height:1px;width:7px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:102px;height:1px">
						<img style="height:1px;width:102px;border:0px" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:7px;height:1px;">
						<img style="height:1px;width:7px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:180px;height:1px">
						<img style="height:1px;width:180px;border:0px" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
				</tr>
			</tbody>
		</table>
		<table cellspacing=0 cellpadding=0 style="border:0px">
			<table cellspacing="0" cellpadding="0" border="0">
				<tbody>
					<tr>
						<td style="font: 9px \'Arial Narrow\';vertical-align:top;width:7;height:13px">
							<img style="height:13px;width:1;border:0px" src="'.APP.'webroot/img/boleto_imgs/1.png">
						</td>
						<td style="font: 9px \'Arial Narrow\';verticalalign:top;height:13px" colspan="3">Uso do banco</td>
						<td style="font: 9px \'Arial Narrow\';verticalalign:top;height:13px;width:7px">
							<img style="height:13px;width:1;border:0px" src="'.APP.'webroot/img/boleto_imgs/1.png">
						</td>
						<td style="font: 9px \'Arial Narrow\';verticalalign:top;width:83px;height:13px">Carteira</td>
						<td style="font: 9px \'Arial Narrow\';verticalalign:top;height:13px;width:7px"> 
							<img style="height:13px;width:1;border:0px" src="'.APP.'webroot/img/boleto_imgs/1.png">
						</td>
						<td style="font: 9px \'Arial Narrow\';vertical-align:top;width:43px;height:13px">Espécie</td>
						<td style="font: 9px \'Arial Narrow\';vertical-align:top;height:13px;width:7px">
							<img style="height:13px;width:1;border:0px" src="'.APP.'webroot/img/boleto_imgs/1.png">
						</td>
						<td style="font: 9px \'Arial Narrow\';vertical-align:top;width:103px;height:13px">Quantidade</td>
						<td style="font: 9px \'Arial Narrow\';vertical-align:top;height:13px;width:7px">
							<img style="height:13px;width:1;border:0px" src="'.APP.'webroot/img/boleto_imgs/1.png">
						</td>
						<td style="font: 9px \'Arial Narrow\';vertical-align:top;width:102px;height:13px">Valor Documento</td>
						<td style="font: 9px \'Arial Narrow\';vertical-align:top;width:7px;height:13px">
							<img style="height:13px;width:1;border:0px" src="'.APP.'webroot/img/boleto_imgs/1.png">
						</td>
						<td style="font: 9px \'Arial Narrow\';vertical-align:top;width:180px;height:13px">(=) Valor documento</td>
					</tr>
					<tr>
						<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
							<img style="width:1px;border:0px;height:12px" src="'.APP.'webroot/img/boleto_imgs/1.png">
						</td>
						<td valign="top" style="font: bold 10px Arial; color: black;height:12px;" colspan="3">
							<div style="text-align:-webkit-left"></div>
						</td>
						<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
							<img style="width:1px;border:0px;height:12px" src="'.APP.'webroot/img/boleto_imgs/1.png">
						</td>
						<td style="font: bold 10px Arial; color: black;vertical-align:top;width:83px;">
							<div style="text-align:-webkit-left"> <span class="campo">'.$dadosboleto["carteira"].'</span></div>
						</td>
						<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
							<img style="width:1px;border:0px;height:12px" src="'.APP.'webroot/img/boleto_imgs/1.png">
						</td>
						<td style="font: bold 10px Arial; color: black;vertical-align:top;width:43px;">
							<div style="text-align:-webkit-left"><span class="campo">'.$dadosboleto["especie"].'</span> </div>
						</td>
						<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
							<img style="width:1px;border:0px;height:12px" src="'.APP.'webroot/img/boleto_imgs/1.png">
						</td>
						<td style="font: bold 10px Arial; color: black;vertical-align:top;width:103px;">
							<span class="campo">'.$dadosboleto["quantidade"].'</span>
						</td>
						<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
							<img style="width:1px;border:0px;height:12px" src="'.APP.'webroot/img/boleto_imgs/1.png">
						</td>
						<td style="font: bold 10px Arial; color: black;vertical-align:top;width:102px;">
							<span class="campo">'.$dadosboleto["valor_unitario"].'</span>
						</td>
						<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px"> 
							<img style="width:1px;border:0px;height:12px" src="'.APP.'webroot/img/boleto_imgs/1.png">
						</td>
						<td style="font: bold 10px Arial; color: black;vertical-align:top;text-align:-webkit-right;width:180px;height:12px"> 
							<span class="campo">'.$dadosboleto["valor_boleto"].'</span>
						</td>
					</tr>
					<tr>
						<td style="vertical-align:top;width:7px;height:1px">
							<img style="width:7px;border:0px;height:1px" src="'.APP.'webroot/img/boleto_imgs/2.png">
						</td>
						<td style="vertical-align:top;width:7px;height:1px">
							<img style="width:75px;border:0px;height:1px" src="'.APP.'webroot/img/boleto_imgs/2.png">
						</td>
						<td style="vertical-align:top;width:7px;height:1px">
							<img style="width:7px;border:0px;height:1px" src="'.APP.'webroot/img/boleto_imgs/2.png">
						</td>
						<td style="vertical-align:top;width:31px;height:1px">
							<img style="width:31px;border:0px;height:1px" src="'.APP.'webroot/img/boleto_imgs/2.png">
						</td>
						<td style="vertical-align:top;width:7px;height:1px"> 
							<img style="width:7px;border:0px;height:1px" src="'.APP.'webroot/img/boleto_imgs/2.png">
						</td>
						<td style="vertical-align:top;width:83px;height:1px">
							<img style="width:83px;border:0px;height:1px" src="'.APP.'webroot/img/boleto_imgs/2.png">
						</td>
						<td style="vertical-align:top;width:7px;height:1px"> 
							<img style="width:7px;border:0px;height:1px" src="'.APP.'webroot/img/boleto_imgs/2.png">
						</td>
						<td style="vertical-align:top;width:43px;height:1px">
							<img style="width:43px;border:0px;height:1px" src="'.APP.'webroot/img/boleto_imgs/2.png">
						</td>
						<td style="vertical-align:top;width:7px;height:1px">
							<img style="width:7px;border:0px;height:1px" src="'.APP.'webroot/img/boleto_imgs/2.png">
						</td>
						<td style="vertical-align:top;width:10px; height:1px">
							<img style="width:103px;border:0px;height:1px" src="'.APP.'webroot/img/boleto_imgs/2.png">
						</td>
						<td style="vertical-align:top;width:7px;height:1px">
							<img style="width:7px;border:0px;height:1px" src="'.APP.'webroot/img/boleto_imgs/2.png">
						</td>
						<td style="vertical-align:top;width:10px; height:1px">
							<img style="width:102px;border:0px;height:1px" src="'.APP.'webroot/img/boleto_imgs/2.png">
						</td>
						<td style="vertical-align:top;width:7px;height:1px">
							<img style="width:7px;border:0px;height:1px" src="'.APP.'webroot/img/boleto_imgs/2.png">
						</td>
						<td style="vertical-align:top;width:18px; height:1px">
							<img style="width:180px;border:0px;height:1px" src="'.APP.'webroot/img/boleto_imgs/2.png">
						</td>
					</tr>
				</tbody>
			</table> 
		</table>
		<table style="width:666px;border:0px" cellspacing=0 cellpadding=0>
			<tbody>
				<tr>
					<td style="text-align:-webkit-right:width:10px">
						<table cellspacing=0 cellpadding=0 style="border:0px;text-align:-webkit-left">
							<tbody>
								<tr>
									<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:7px;height:13px">
										<img style="height:13px;width:1px;border:0px;" src='.APP.'webroot/img/boleto_imgs/1.png>
									</td>
								</tr>
								<tr>
									<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
										<img style="width:1px;border:0px;height:12px" src='.APP.'webroot/img/boleto_imgs/1.png>
									</td>
								</tr>
								<tr>
									<td style="vertical-align:top;width:7px;height:1px;">
										<img style="height:1px;width:1px;border:0px" src='.APP.'webroot/img/boleto_imgs/2.png>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
					<td style="vertical-align:top;width:468px" rowspan=5>
						<font style="font: 9px \'Arial Narrow\'; color: #000033">Instruções (Texto de responsabilidade do cedente)</font>
						<br><br>
						<span style="font: bold 10px Arial; color: black">
							<font class=campo>
								'.utf8_decode($dadosboleto["instrucoes1"]).'<br>
								'.utf8_decode($dadosboleto["instrucoes2"]).'<br>
								'.utf8_decode($dadosboleto["instrucoes3"]).'<br>
								'.utf8_decode($dadosboleto["instrucoes4"]).'<br>
								'.utf8_decode($dadosboleto["instrucoes5"]).'<br>
								'.utf8_decode($dadosboleto["instrucoes6"]).'<br>
								'.utf8_decode($dadosboleto["instrucoes7"]).'
							</font>
							<br><br>
						</span>
					</td>
					<td style="text-align: -webkit-right;width:188px">
						<table cellspacing=0 cellpadding=0 style="border:0px">
							<tbody>
								<tr>
									<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:7px;height:13px">
										<img style="height:13px;width:1px;border:0px;" src='.APP.'webroot/img/boleto_imgs/1.png>
									</td>
									<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:180px;height:13px">(-) Desconto / Abatimentos</td>
								</tr>
								<tr>
									<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
										<img style="width:1px;border:0px;height:12px" src='.APP.'webroot/img/boleto_imgs/1.png>
									</td>
								<td style="font: bold 10px Arial; color: black;vertical-align:top:text-align:-webkit-right;width:180px;height:12px"></td>
							</tr>
							<tr>
								<td style="vertical-align:top;width:7px;height:1px;">
									<img style="height:1px;width:7px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
								</td>
								<td style="vertical-align:top;width:180px;height:1px">
									<img style="height:1px;width:180px;border:0px" src='.APP.'webroot/img/boleto_imgs/2.png>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td style="text-align:-webkit-right:width:10px"> 
					<table cellspacing=0 cellpadding=0 style="border:0px;text-align:-webkit-left">
						<tbody>
							<tr>
								<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:7px;height:13px">
									<img style="height:13px;width:1px;border:0px;" src='.APP.'webroot/img/boleto_imgs/1.png>
								</td>
							</tr>
							<tr>
								<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
									<img style="width:1px;border:0px;height:12px" src='.APP.'webroot/img/boleto_imgs/1.png>
								</td>
							</tr>
							<tr>
								<td style="vertical-align:top;width:7px;height:1px;">
									<img style="height:1px;width:1px;border:0px" src='.APP.'webroot/img/boleto_imgs/2.png>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
				<td style="text-align:-webkit-right;width:188px">
					<table cellspacing=0 cellpadding=0 style="border:0px">
						<tbody>
							<tr>
								<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:7px;height:13px">
									<img style="height:13px;width:1px;border:0px;" src='.APP.'webroot/img/boleto_imgs/1.png>
								</td>
								<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:180px;height:13px">(-) Outras deduções</td>
							</tr>
							<tr>
								<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
									<img style="width:1px;border:0px;height:12px" src='.APP.'webroot/img/boleto_imgs/1.png>
								</td>
								<td style="font: bold 10px Arial; color: black;vertical-align:top:text-align:-webkit-right;width:180px;height:12px"></td>
							</tr>
							<tr>
								<td style="vertical-align:top;width:7px;height:1px;">
									<img style="height:1px;width:7px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
								</td>
								<td style="vertical-align:top;width:180px;height:1px">
									<img style="height:1px;width:180px;border:0px" src='.APP.'webroot/img/boleto_imgs/2.png>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td style="text-align:-webkit-right:width:10px">
					<table cellspacing=0 cellpadding=0 style="border:0px;text-align:-webkit-left">
						<tbody>
							<tr>
								<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:7px;height:13px">
									<img style="height:13px;width:1px;border:0px;" src='.APP.'webroot/img/boleto_imgs/1.png>
								</td>
							</tr>
							<tr>
								<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
									<img style="width:1px;border:0px;height:12px" src='.APP.'webroot/img/boleto_imgs/1.png>
								</td>
								</tr>
								<tr>
									<td style="vertical-align:top;width:7px;height:1px;">
										<img style="height:1px;width:1px;border:0px" src='.APP.'webroot/img/boleto_imgs/2.png>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
					<td style="text-align:-webkit-right;width:188px">
						<table cellspacing=0 cellpadding=0 style="border:0px">
							<tbody>
								<tr>
									<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:7px;height:13px">
										<img style="height:13px;width:1px;border:0px;" src='.APP.'webroot/img/boleto_imgs/1.png>
									</td>
									<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:180px;height:13px">(+) Mora / Multa</td>
								</tr>
								<tr>
									<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
										<img style="width:1px;border:0px;height:12px" src='.APP.'webroot/img/boleto_imgs/1.png>
									</td>
									<td style="font: bold 10px Arial; color: black;vertical-align:top:text-align:-webkit-right;width:180px;height:12px"></td>
								</tr>
								<tr>
									<td style="vertical-align:top;width:7px;height:1px;">
										<img style="height:1px;width:7px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
									</td>
									<td style="vertical-align:top;width:180px;height:1px">
										<img style="height:1px;width:180px;border:0px" src='.APP.'webroot/img/boleto_imgs/2.png>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td style="text-align:-webkit-right:width:10px">
						<table cellspacing=0 cellpadding=0 style="border:0px;text-align:-webkit-left">
							<tbody>
								<tr>
									<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:7px;height:13px">
										<img style="height:13px;width:1px;border:0px;" src='.APP.'webroot/img/boleto_imgs/1.png>
									</td>
								</tr>
								<tr>
									<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
										<img style="width:1px;border:0px;height:12px" src='.APP.'webroot/img/boleto_imgs/1.png>
									</td>
								</tr>
								<tr>
									<td style="vertical-align:top;width:7px;height:1px;">
										<img style="height:1px;width:1px;border:0px" src='.APP.'webroot/img/boleto_imgs/2.png>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
					<td style="text-align:-webkit-right;width:188px">
						<table cellspacing=0 cellpadding=0 style="border:0px">
							<tbody>
								<tr>
									<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:7px;height:13px">
										<img style="height:13px;width:1px;border:0px;" src='.APP.'webroot/img/boleto_imgs/1.png>
									</td>
									<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:180px;height:13px">(+) Outros acréscimos</td>
								</tr>
								<tr>
									<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
										<img style="width:1px;border:0px;height:12px" src='.APP.'webroot/img/boleto_imgs/1.png>
									</td>
									<td style="font: bold 10px Arial; color: black;vertical-align:top:text-align:-webkit-right;width:180px;height:12px"></td>
								</tr>
								<tr>
									<td style="vertical-align:top;width:7px;height:1px;">
										<img style="height:1px;width:7px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
									</td>
									<td style="vertical-align:top;width:180px;height:1px">
										<img style="height:1px;width:180px;border:0px" src='.APP.'webroot/img/boleto_imgs/2.png>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td style="text-align:-webkit-right:width:10px">
						<table cellspacing=0 cellpadding=0 style="border:0px;text-align:-webkit-left">
							<tbody>
								<tr>
									<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:7px;height:13px">
										<img style="height:13px;width:1px;border:0px;" src='.APP.'webroot/img/boleto_imgs/1.png>
									</td>
								</tr>
								<tr>
									<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
										<img style="width:1px;border:0px;height:12px" src='.APP.'webroot/img/boleto_imgs/1.png>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
					<td style="text-align:-webkit-right;width:188px">
						<table cellspacing=0 cellpadding=0 style="border:0px">
							<tbody>
								<tr>
									<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:7px;height:13px">
										<img style="height:13px;width:1px;border:0px;" src='.APP.'webroot/img/boleto_imgs/1.png>
									</td>
									<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:180px;height:13px">(=) Valor cobrado</td>
								</tr>
								<tr>
									<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
										<img style="width:1px;border:0px;height:12px" src='.APP.'webroot/img/boleto_imgs/1.png>
									</td>
									<td style="font: bold 10px Arial; color: black;vertical-align:top:text-align:-webkit-right;width:180px;height:12px"></td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
		<table style="width:666px;border:0px" cellspacing=0 cellpadding=0>
			<tbody>
				<tr>
					<td style="vertical-align:top;width:666px;height:1px">
						<img style="height:1px;width:666px;border:0px" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
				</tr>
			</tbody>
		</table>
		<table cellspacing=0 cellpadding=0 style="border:0px">
			<tbody>
				<tr>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:7px;height:13px">
						<img style="height:13px;width:1px;border:0px;" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:659px;height:13px">Sacado</td>
				</tr>
				<tr>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
						<img style="width:1px;border:0px;height:12px" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:659px;height:12px;">
						<span class="campo">'.$dadosboleto["sacado"].'</span>
					</td>
				</tr>
			</tbody>
		</table>
		<table cellspacing=0 cellpadding=0 style="border:0px">
			<tbody>
				<tr>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:7px;height:12px">
						<img style="width:1px;border:0px;height:12px" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:659px;height:12px;">
						<span class="campo">'.$dadosboleto["endereco1"].'</span>
					</td>
				</tr>
			</tbody>
		</table>
		<table cellspacing=0 cellpadding=0 style="border:0px">
			<tbody>
				<tr>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:7px;height:13px">
						<img style="height:13px;width:1px;border:0px;" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: bold 10px Arial; color: black;vertical-align:top;width:472px;height:13px">
						<span class="campo">'.$dadosboleto["endereco2"].'</span>
					</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:7px;height:13px">
						<img style="height:13px;width:1px;border:0px;" src='.APP.'webroot/img/boleto_imgs/1.png>
					</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;vertical-align:top;width:180px;height:13px">Cód. baixa</td>
				</tr>
				<tr>
					<td style="vertical-align:top;width:7px;height:1px;">
						<img style="height:1px;width:7px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:472px;height:1px">
						<img style="height:1px;width:472px;border:0px" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:7px;height:1px;">
						<img style="height:1px;width:7px;border:0px;" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
					<td style="vertical-align:top;width:180px;height:1px">
						<img style="height:1px;width:180px;border:0px" src='.APP.'webroot/img/boleto_imgs/2.png>
					</td>
				</tr>
			</tbody>
		</table>
		<table style="width:666px;border:0px" cellspacing=0 cellpadding=0>
			<tbody>
				<tr>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;width:7px;height:12px"></td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;width:409px;">Sacador/Avalista</td>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;width:250px;">
						<div style="text-align:-webkit-right">Autenticação mecânica - <b style="font: bold 10px Arial; color: black">Ficha de Compensação</b></div>
					</td>
				</tr>
				<tr>
					<td style="font: 9px \'Arial Narrow\'; color: #000033" colspan=3></td>
				</tr>
			</tbody>
		</table>
		<table style="width:666px;border:0px" cellspacing=0 cellpadding=0>
			<tbody>
				<tr>
					<td style="vertical-align:bottom;text-align:-webkit-left;height:50px">'.$bar_codes.'</td>
				</tr>
			</tbody>
		</table>
		<table style="width:666px;border:0px" cellspacing=0 cellpadding=0>
			<tr>
				<td style="font: 9px \'Arial Narrow\'; color: #000033;width:666px;"></td>
			</tr>
			<tbody>
				<tr>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;width:666px;">
						<div style="text-align:-webkit-right">Corte na linha pontilhada</div>
					</td>
				</tr>
				<tr>
					<td style="font: 9px \'Arial Narrow\'; color: #000033;width:666px;">
						<img style="height:1px;width:665px;border:0px" src='.APP.'webroot/img/boleto_imgs/6.png>
					</td>
				</tr>
			</tbody>
		</table>
	</body>
</html>';

?>