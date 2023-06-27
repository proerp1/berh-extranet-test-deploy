<script type="text/javascript">
	$(document).ready(function(){
		$('.money_exchange').maskMoney({
			decimal: '.',
			thousands: '',
			precision: 2
		});
	});
</script>

<?php echo $this->element("abas_customers", array('id' => $id)); ?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
    	<?php echo $this->Form->create('CustomerDiscount', array("id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false])); ?>

    		<div class="mb-7">
                <label for="cep" class="form-label">Desconto</label>
                <div class="input-group">
                    <span class="input-group-text">%</span>
                    <?php echo $this->Form->input('discount', array("type" => "text", "placeholder" => "Desconto", "class" => "form-control money_exchange mb-3 mb-lg-0"));  ?>
                </div>
            </div>

    		<div class="mb-7">
                <label for="cep" class="form-label">Data de validade</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                    <?php echo $this->Form->input('expire_date', array("type" => "text", "placeholder" => "Data de validade", "class" => "form-control datepicker mb-3 mb-lg-0"));  ?>
                </div>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Status</label>
                <?php echo $this->Form->input('status_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Observação</label>
                <?php echo $this->Form->input('description', ["placeholder" => "Observação", "class" => "form-control mb-3 mb-lg-0"]);?>
            </div>

            <?php if ($acao == "edit"){ ?>
	            <div class="mb-7 col">
	                <label class="fw-semibold fs-6 mb-2">Produto</label>
	                <select name="product" id="DiscountProducts" class="form-select mb-3 mb-lg-0" data-control="select2">
	                	<option value="">Selecione</option>
	                	<?php foreach ($produtos as $key => $produto) { ?>
	                		<option value="<?php echo $produto['Product']['id'] ?>"><?php echo $produto["Product"]['name'] ?></option>
	                	<?php } ?>
	                </select>
	            </div>
            <?php } ?>

            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                	<a href="<?php echo $this->base.'/customers/descontos/'.$id; ?>" class="btn btn-light-dark">Voltar</a>
                	<button type="submit" class="btn btn-success js-salvar" data-loading-text="Aguarde...">Salvar</button>
                </div>
            </div>

            <?php if (isset($desconto_id) and CakeSession::read("Auth.User.group_id") == 1): ?>
            	<i>Alterado por <?php echo $this->request->data['UsuarioAlteracao']['name'] ?> em <?php echo date('d/m/Y H:i:s', strtotime($this->request->data['CustomerDiscount']['updated'])) ?></i>
            <?php endif ?>

    	</form>
    </div>
</div>

<?php if ($acao == "edit") { ?>
	<div class="card mb-5 mb-xl-8">
	    <div class="card-body py-7">
	        <div class="table-responsive">
				<?php echo $this->element("table"); ?>
					<thead>
						<tr class="fw-bolder text-muted bg-light">
							<th class="ps-4 min-w-200px rounded-start">Produto</th>
							<th class="w-200px min-w-200px rounded-end">Ações</th>
						</tr>
					</thead>
					<tbody>
						<?php if (!empty($data[0]["CustomerDiscountsProduct"]['id'])) { ?>
							<?php for ($i=0; $i < count($data); $i++) { ?>
								<tr>
									<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Product"]["name"] ?></td>
									<td class="fw-bold fs-7 ps-4">
										<a href="javascript:" onclick="verConfirm('<?php echo $this->base."/customers/delete_produto_desconto/".$id."/".$desconto_id."/".$data[$i]["CustomerDiscountsProduct"]["id"] ?>');" rel="tooltip" title="Excluir" class="btn btn-danger btn-sm">
											Excluir
										</a>
									</td>
								</tr>
							<?php } ?>
						<?php } else { ?>
							<tr>
								<td colspan="6" class="fw-bold fs-7 ps-4">Nenhum registro encontrado</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
<?php } ?>