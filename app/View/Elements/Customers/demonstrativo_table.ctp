<?php
    $faturamento_inicio = $faturamento['Billing']['date_billing'];
    $faturamento_fim = date("t/m/Y", strtotime(str_replace('/', '-', $faturamento['Billing']['date_billing'])));
?>
<?php echo $this->Html->script('formata_dinheiro');echo"\n\t"; ?>


<table class="table table-hover align-middle gs-0 gy-4">
	<thead class="border-1">
		<tr>
			<td colspan="11" class="ps-4">Razão Social : <a target="_blank" href="<?php echo $this->base.'/customers/edit/'.$faturamento_cliente['Customer']['id'] ?>"><?php echo $faturamento_cliente['Customer']['nome_primario'].' - '.$faturamento_cliente['Customer']['nome_secundario']; ?></a></td>
		</tr>
		<tr>
			<td colspan="11" class="ps-4">Período : <?php echo $faturamento_inicio." até ".$faturamento_fim; ?></td>
		</tr>
		<tr>
			<td colspan="10" class="ps-4">Minimo Consultas : <?php echo $faturamento_cliente['BillingMonthlyPayment']['quantity']; ?></td>
		</tr>
		<tr>
			<td colspan="10" class="ps-4"><h4>Produtos</h4></td>
		</tr>
		<tr>
			<td class="ps-4">Nome</td>
			<td>Tipo da consulta</td>
			<td colspan="<?php echo $tipo == 1 ? '1' : '2' ?>">Consultas Realizadas</td>
			<?php if ($tipo == 1): ?>
				<td>Consultas Faturadas</td>
			<?php endif ?>
			<td>Valor Unitário</td>
			<td>Valor total</td>
			<td>Total</td>
		</tr>
		<tr>
			<td colspan="6" class="ps-4">Mensalidade</td>
			<td colspan="10">R$ <span class="js_valor_total"><?php echo $faturamento_cliente['BillingMonthlyPayment']['monthly_value_formatado']; ?></span></td>
		</tr>
		<?php $total = 0; ?>
		<?php if ($negativacao) { ?>
			<?php for ($i=0; $i < count($negativacao); $i++) { ?>
			<?php $total += $negativacao[$i]['n']['valor_total']; ?>
				<form action="<?php echo $this->Html->url(["controller" => "billings", "action" => "update_negativacao", $negativacao[$i]['n']['id']]) ?>" method="post">
					<tr class="align-middle">
						<td colspan="1" class="ps-4"><?php echo $negativacao[$i]['p']['name']; ?></td>
						<td colspan="1">
							<?php
                                if ($negativacao[$i]['n']['type'] == 1) {
                                    echo 'Quantidade';
                                } elseif ($negativacao[$i]['n']['type'] == 2) {
                                    echo 'Consumo';
                                } elseif ($negativacao[$i]['n']['type'] == 3) {
                                    echo 'Fora da composição do plano';
                                }
                            ?>
						</td>
						<td colspan="<?php echo $tipo == 1 ? '1' : '2' ?>">
							<input required type="number" name="quantidade" class="form-control js_quantidade" value="<?php echo $negativacao[$i]['n']['qtde_consumo']; ?>">
						</td>
						<?php if ($tipo == 1): ?>
							<td colspan="1"><?php echo $negativacao[$i]['n']['qtde_excedente']; ?></td>
						<?php endif ?>
						<td colspan="1">
							<input required type="text" name="valor_unitario" class="form-control money_exchange js_valor_unitario" value="<?php echo number_format($negativacao[$i]['n']['valor_unitario'], 2, ',', '.'); ?>">
						</td>
						<td colspan="1">R$ <?php echo number_format($negativacao[$i]['n']['qtde_consumo']*$negativacao[$i]['n']['valor_unitario'], 2, ',', '.'); ?></td>
						<td colspan="1">
							R$ <span class="js_valor_total"><?php echo number_format($negativacao[$i]['n']['valor_total'], 2, ',', '.'); ?></span>
							<button class="btn btn-success js_salva_linha" style="display:none">Salvar</button>
						</td>
					</tr>
				</form>
			<?php } ?>
		<?php } else {?>
			<tr>
				<td colspan="7" class="ps-4">Nenhum registro encontrado</td>
			</tr>
		<?php } ?>

		<?php if ($pefin) { ?>
			<?php for ($i=0; $i < count($pefin); $i++) { ?>
			<?php $total += $pefin[$i]['n']['valor_total']; ?>
				<form action="<?php echo $this->Html->url(["controller" => "billings", "action" => "update_pefin", $pefin[$i]['n']['id']]) ?>" method="post">
					<tr>
						<td colspan="1" class="align-middle ps-4"><?php echo $pefin[$i]['p']['name']; ?></td>
						<td></td>
						<td colspan="<?php echo $tipo == 1 ? '1' : '2' ?>">
							<input required type="number" name="quantidade" class="form-control js_quantidade" value="<?php echo $pefin[$i]['n']['qtde_realizado']; ?>">
						</td>
						<?php if ($tipo == 1): ?>
							<td colspan="1"><?php echo $pefin[$i]['n']['qtde_excedente']; ?></td>
						<?php endif ?>
						<td colspan="1">
							<input required type="text" name="valor_unitario" class="form-control money_exchange js_valor_unitario" value="<?php echo number_format($pefin[$i]['n']['valor_unitario'], 2, ',', '.'); ?>">
						</td>
						<td colspan="1">R$ <?php echo number_format($pefin[$i]['n']['qtde_realizado']*$pefin[$i]['n']['valor_unitario'], 2, ',', '.'); ?></td>
						<td colspan="1">
							R$ <?php echo number_format($pefin[$i]['n']['valor_total'], 2, ',', '.'); ?>
							<button class="btn btn-success js_salva_linha" style="display:none">Salvar</button>
						</td>
					</tr>
				</form>
			<?php } ?>
		<?php } ?>

		<?php if ($berh) { ?>
			<?php for ($i=0; $i < count($berh); $i++) { ?>
			<?php $total += $berh[$i]['BillingNovaVida']['valor_total']; ?>
				<form action="<?php echo $this->Html->url(["controller" => "billings", "action" => "update_hiper", $berh[$i]['BillingNovaVida']['id']]) ?>" method="post">
					<tr class="align-middle">
						<td colspan="1" class="ps-4"><?php echo $berh[$i]['Product']['name']; ?></td>
						<td></td>
						<td colspan="<?php echo $tipo == 1 ? '1' : '2' ?>">
							<input required type="number" name="quantidade" class="form-control js_quantidade" value="<?php echo $berh[$i]['BillingNovaVida']['quantidade']; ?>">
						</td>
						<?php if ($tipo == 1): ?>
							<td colspan="1"><?php echo $berh[$i]['BillingNovaVida']['quantidade_cobrada']; ?></td>
						<?php endif ?>
						<td colspan="1">
							<input required type="text" name="valor_unitario" class="form-control money_exchange js_valor_unitario" value="<?php echo number_format($berh[$i]['BillingNovaVida']['valor_unitario'], 2, ',', '.'); ?>">
						</td>
						<td colspan="1">R$ <?php echo number_format($berh[$i]['BillingNovaVida']['quantidade_cobrada']*$berh[$i]['BillingNovaVida']['valor_unitario'], 2, ',', '.'); ?></td>
						<td colspan="1">
							R$ <?php echo number_format($berh[$i]['BillingNovaVida']['valor_total'], 2, ',', '.'); ?>
							<button class="btn btn-success js_salva_linha" style="display:none">Salvar</button>
						</td>
					</tr>
				</form>
			<?php } ?>
		<?php } ?>

		<?php if (!empty($meproteja)) { ?>
			<?php for ($i=0; $i < count($meproteja); $i++) { ?>
			<?php $total += $meproteja[$i]['ClienteMeProteja']['clienteMeProtejaValor']; ?>
				<tr class="align-middle">
					<td colspan="1" class="ps-4"><?php echo $meproteja[$i]['Product']['name']; ?></td>
					<td>Me proteja</td>
					<td colspan="<?php echo $tipo == 1 ? '1' : '2' ?>">1</td>
					<?php if ($tipo == 1): ?>
						<td colspan="1">1</td>
					<?php endif ?>
					<td colspan="1">R$ <?php echo number_format($meproteja[$i]['ClienteMeProteja']['clienteMeProtejaValor'], 2, ',', '.'); ?></td>
					<td colspan="1">R$ <?php echo number_format($meproteja[$i]['ClienteMeProteja']['clienteMeProtejaValor'], 2, ',', '.'); ?></td>
					<td colspan="1">R$ <?php echo number_format($meproteja[$i]['ClienteMeProteja']['clienteMeProtejaValor'], 2, ',', '.'); ?></td>
				</tr>
			<?php } ?>
		<?php } ?>

		<?php $manutencao = 0; ?>
		<?php if ($faturamento_cliente['PefinMaintenance']['id'] != null): ?>
			<?php $manutencao = $faturamento_cliente['PefinMaintenance']['value_nao_formatado']; ?>
			<tr>
				<td colspan="6" class="ps-4">Manutenção PEFIN:</td>
				<td colspan="10">R$ <span class="js_valor_total"><?php echo $faturamento_cliente['PefinMaintenance']['value']; ?></span></td>
			</tr>
		<?php endif ?>
		<tr>
			<td colspan="6" style="text-align:right"><h4>Total Excedente:</h4></td>
			<td colspan="10">R$ <?php echo number_format($total, 2, ',', '.'); ?></td>
		</tr>
		<?php
            $total_sem_desconto = $faturamento_cliente['BillingMonthlyPayment']['monthly_value']+$total+$manutencao;
            $valor_descontar = ($faturamento_cliente['BillingMonthlyPayment']['desconto']/100)*$total_sem_desconto;
        ?>
		<?php if ($faturamento_cliente['BillingMonthlyPayment']['desconto'] > 0) { ?>
			<tr>
				<td colspan="6" style="text-align:right"><h4>Total sem desconto:</h4></td>
				<td colspan="10">R$ <?php echo number_format($total_sem_desconto, 2, ',', '.'); ?></td>
			</tr>
			<tr>
				<td colspan="6" style="text-align:right"><h4>Desconto:</h4></td>
				<td colspan="10">
					<p>- <?php echo number_format($faturamento_cliente['BillingMonthlyPayment']['desconto'], 2, '.', ''); ?>%</p>
					<p>- R$<?php echo number_format($valor_descontar, 2, ',', '.'); ?></p>
				</td>
			</tr>
		<?php } ?>
		<tr>
			<td colspan="6" style="text-align:right"><h4>Total Fatura:</h4></td>
			<?php
                $total_com_desconto = $total_sem_desconto - $valor_descontar;
            ?>
			<td colspan="10" class="js_total_geral">R$ <?php echo number_format($total_com_desconto, 2, ',', '.') //echo $tipo == 1 ? number_format($faturamento_cliente['BillingMonthlyPayment']['monthly_value']+$total+$manutencao, 2, ',', '.') : number_format($faturamento_cliente['BillingMonthlyPayment']['monthly_value_total']+$manutencao,2,',','.');?></td>
		</tr>
	</head>
</table>

<script type="text/javascript">
	$(document).ready(function(){
		$('.money_exchange').maskMoney({
			decimal: ',',
			thousands: '.',
			precision: 2
		});

		$(".js_quantidade").on("change", function() {
			calc_total_linha($(this).parent().parent());

			calc_total_geral();

			$(this).parent().parent().find(".js_salva_linha").show();
		});

		$(".js_valor_unitario").on("change", function() {
			calc_total_linha($(this).parent().parent());

			calc_total_geral();

			$(this).parent().parent().find(".js_salva_linha").show();
		});
	})

	function calc_total_linha(linha) {
		var qtde = linha.find(".js_quantidade").val();
		if (isNaN(qtde)) {
			qtde = 0;
		};
		var val_unit = retorna_dinheiro_us(linha.find('.js_valor_unitario').val());

		if (isNaN(val_unit)) {
			val_unit = 0;
		};
		var total = parseInt(qtde) * parseFloat(val_unit);

		if (isNaN(total)) {
			total = 0;
		};

		linha.find(".js_valor_total").text(total.toLocaleString('pt-br', {minimumFractionDigits: 2}));
	}

	function calc_total_geral() {
		var valor_total_geral = 0;

		$(".js_valor_total").each(function(index, value) {
			var val = retorna_dinheiro_us($(value).text());
			if (val == '') {
				val = 0;
			};
			valor_total_geral += parseFloat(val);
		});

		$(".js_total_geral").text(valor_total_geral.toLocaleString('pt-br',{style: 'currency', currency: 'BRL'}));
	}
</script>