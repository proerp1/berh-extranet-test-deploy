<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array( "controller" => "negativacao", "action" => "inclusao")); ?>" role="form" id="busca" autocomplete="off">
        <div class="card-header border-0 pt-6 pb-6">
            <div class="card-title">
                <div class="row">
                    <div class="col d-flex align-items-center">
                        <span class="position-absolute ms-6">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control form-control-solid ps-15" id="q" name="q" value="<?php echo isset($_GET["q"]) ? $_GET["q"] : ""; ?>" placeholder="Buscar" />
                    </div>
                </div>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                	<a onclick="ver_confirm('Tem certeza que deseja gerar registros para esse(s) cliente(s)?')" href="javascript:;" class="btn btn-light-primary me-3 js_link_gerar_registros js_div_gerar_registros" style="display: none">
                        <i class="fas fa-file"></i>
						Gerar registros
                    </a>
                </div>
            </div>
        </div>
    </form>

    <div class="card-body pt-0 py-3">
        <div class="table-responsive">
        	<?php echo $this->element("table"); ?>
        		<thead>
        			<tr class="fw-bolder text-muted bg-light">
        				<th class="ps-4 w-150px min-w-150px rounded-start"><input type="checkbox" class="check_all" id="check_all"> <label for="check_all">Selecionar todos</label></th>
        				<th>Cliente</th>
        				<th>Nome</th>
        				<th>Tipo</th>
        				<th>Documento</th>
        				<th>Valor</th>
        				<th>Data de cadastro</th>
        				<th>Solic. Baixa</th>
        				<th class="w-200px min-w-200px rounded-end">Ações</th>
        			</tr>
        		</thead>
        		<tbody>
        			<?php $total = 0; ?>
        			<?php if ($data) { ?>
        				<?php for ($i=0; $i < count($data); $i++) { ?>
        					<tr>
        						<td class="fw-bold fs-7 ps-4">
        							<input type="checkbox" class="check_conta check_individual <?php echo $data[$i]['CadastroPefin']['principal_id'] != '' ? 'tem_coobrigado' : '' ?>" data-id="<?php echo $data[$i]["CadastroPefin"]["id"] ?>" data-coobrigadoid="<?php echo $data[$i]["CadastroPefin"]["principal_id"] ?>" id="<?php echo $data[$i]["CadastroPefin"]["id"] ?>">
        							<?php if ($data[$i]['CadastroPefin']['principal_id'] != ''): ?>
        								<span class="label label-warning" data-toggle="tooltip" data-placement="top" title="Coobrigado!"><i class="fa fa-exclamation-triangle"></i> </span>
        							<?php endif ?>
        						</td>
        						<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Customer']['nome_secundario'] ?></td>
        						<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['CadastroPefin']['nome'] ?></td>
        						<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['NaturezaOperacao']['nome'] ?></td>
        						<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['CadastroPefin']['documento'] ?></td>
        						<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['CadastroPefin']['valor'] ?></td>
        						<td class="fw-bold fs-7 ps-4"><?php echo date('d/m/Y H:i:s', strtotime($data[$i]['CadastroPefin']['created'])) ?></td>
        						<td class="fw-bold fs-7 ps-4"><?php echo ($data[$i]['CadastroPefin']['data_solic_baixa'] ? date('d/m/Y H:i:s', strtotime($data[$i]['CadastroPefin']['data_solic_baixa'])) : ''); ?></td>
        						<td class="fw-bold fs-7 ps-4">
        							<a href="<?php echo $this->base.'/negativacao/view/'.$data[$i]["CadastroPefin"]["id"]; ?>" class="btn btn-info btn-sm">Detalhes</a>
        							<a href="<?php echo $this->base.'/negativacao/delete/'.$data[$i]["CadastroPefin"]["id"]; ?>" class="btn btn-danger btn-sm">Excluir</a>
        						</td>
        					</tr>
        				<?php } ?>
        			<?php } else { ?>
        				<tr>
        					<td class="fw-bold fs-7 ps-4" colspan="8">Nenhum registro encontrado</td>
        				</tr>
        			<?php } ?>
        		</tbody>
        	</table>
        </div>
        <?php echo $this->element("pagination"); ?>
    </div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$(".check_all").on("change", function(){
			if ($(this).is(':checked')) {
				$(".check_individual").prop('checked', true);
			} else {
				$(".check_individual").prop('checked', false);
			}

			get_ids();
		})

		$(".check_conta").on("click", function(){	
			get_ids();
		})

		$(".tem_coobrigado").on("click", function(event){
			var coobrigadoid = $(this).data('coobrigadoid');

			if (!$(this).is(':checked')) {
				$("#"+coobrigadoid).prop('checked', false);
				$("body").find("[data-coobrigadoid='"+coobrigadoid+"']").prop('checked', false);
			} else {
				$("#"+coobrigadoid).prop('checked', true);
				$("body").find("[data-coobrigadoid='"+coobrigadoid+"']").prop('checked', true);
			}

			get_ids();

			event.preventDefault();
		});
	})

	function get_ids() {
		if ($(".check_individual:checked").length > 0) {
			$(".js_div_gerar_registros").show();
		} else {
			$(".js_div_gerar_registros").hide();
		}

		var pefinid = '';
		$(".check_individual:checked").each(function(index, el) {
			pefinid += $(this).data('id')+',';
		});

		if ($("#type").val() == 'Inclusão') {
			$("#url").val('<?php echo $this->base ?>/negativacao/gerar_txt/?tipo=inclusao&id='+pefinid);
		} else {
			$("#url").val('<?php echo $this->base ?>/negativacao/gerar_txt/?tipo=exclusao&id='+pefinid);
		}
	}

	function ver_confirm(message) {
		bootbox.confirm({
		    title: 'Atenção',
		    message: message,
		    buttons: {
		        confirm: {
		            label: 'Sim',
		            className: 'btn-success'
		        },
		        cancel: {
		            label: 'Não',
		            className: 'btn-danger'
		        }
		    },
		    callback: function (result) {
		        console.log('This was logged in the callback: ' + result);

		        if (result) {
					window.location.href = $("#url").val();
		        }
		    }
		});
	}
</script>

<input type="hidden" id="type" value="<?php echo $action ?>">
<input type="hidden" id="url" value="">

<script>
    $( document ).ready(function() {
        $('#q').on('change', function () {
            $("#busca").submit();
        });
    });
</script>