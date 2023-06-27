<?php
    echo $this->element("abas_billings", ['id' => $id]);
?>

<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url([ "controller" => "billings", "action" => "linhas_nao_importadas", $id]); ?>/" role="form" id="busca" autocomplete="off">
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
            </div>
        </div>
    </form>

    <div class="card-body pt-0 py-3">
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
            	<thead>
            		<tr class="fw-bolder text-muted bg-light">
                        <th class="ps-4 w-150px min-w-150px rounded-start">Produto</th>
            			<th>Logon</th>
            			<th>Documento</th>
            			<th>Quantidade consumo</th>
            			<th>Valor unitário</th>
            			<th>Valor total</th>
            		</tr>
            	</thead>
            	<tbody>
            		<?php if ($data) { ?>
            			<?php for ($i=0; $i < count($data); $i++) { ?>
            				<tr>
            					<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Product']['name'] ?></td>
            					<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['LinhasNaoImportadas']['logon'] ?></td>
            					<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['LinhasNaoImportadas']['documento'] ?></td>
            					<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['LinhasNaoImportadas']['qtd_consumo'] ?></td>
            					<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['LinhasNaoImportadas']['valor_unitario'] ?></td>
            					<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['LinhasNaoImportadas']['valor_total'] ?></td>
            				</tr>
            			<?php } ?>
            		<?php } else { ?>
            			<tr>
            				<td class="fw-bold fs-7 ps-4" colspan="7">Nenhum registro encontrado</td>
            			</tr>
            		<?php } ?>
            	</tbody>
            </table>
        </div>
        <?php echo $this->element("pagination"); ?>
    </div>
</div>

<script>
    $( document ).ready(function() {
        $('[data-kt-customer-table-filter="reset"]').on('click', function () {
            $("#q").val(null);

            $("#busca").submit();
        });

        $('#q').on('change', function () {
            $("#busca").submit();
        });
    })
</script>