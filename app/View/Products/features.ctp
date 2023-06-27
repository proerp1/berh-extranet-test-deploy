<div class="page page-profile">
	<?php
		echo $this->element("abas_products", array('tipo' => $produtos['Product']['tipo']));
	?>

	<section class="panel panel-default">
		<div class="panel-body">
			<?php echo $this->Form->create('ProductFeature', array("id" => "js-form-submit", "class" => "form-horizontal col-md-12", "action" => "/".$form_action."/", "method" => "post")); ?>
				<input type="hidden" name="data[ProductFeature][product_id]" value="<?php echo $id ?>">
				<div class="form-group">
					<label for="inputNome" class="col-sm-2 control-label">Feature</label>
					<div class="col-sm-9">
						<?php echo $this->Form->input('nova_vida_feature_id', array("div" => false, "label" => false, "empty" => "Selecione", "class" => "form-control", "empty" => "Selecione"));  ?>
					</div>
				</div>

				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-9">
						<button type="submit" class="btn btn-success js-salvar" data-loading-text="Aguarde...">Adicionar</button>
					</div>
				</div>
			</form> 
			
			<?php echo $this->element("table"); ?>
				<thead>
					<tr>
						<th class="default">Nome</th>
						<th>Tipo consulta</th>
						<th>Ações</th>
					</tr>
				</thead>
				<tbody>
					<?php if ($features_cadastradas) { ?>
						<?php for ($i=0; $i < count($features_cadastradas); $i++) { ?>
							<tr>
								<td><?php echo $features_cadastradas[$i]["NovaVidaFeature"]["name"]; ?></td>
								<td>
									<?php
										switch ($features_cadastradas[$i]["NovaVidaFeature"]["campo_pesquisa"]) {
											case '1':
												echo "PF";
												break;
											case '2':
												echo "PJ";
												break;
											case '3':
												echo "Ambos";
												break;
											case '4':
												echo "Núm benefício";
												break;
											case '5':
												echo "Atributos";
												break;
											default:
												break;
										}
									?>
								</td>
								<td>
									<a href="javascript:" onclick="verConfirm('<?php echo $this->base.'/products/delete_feature/'.$id.'/'.$features_cadastradas[$i]["ProductFeature"]["id"]; ?>');" rel="tooltip" title="Excluir" class="btn btn-danger btn-xs">
										Excluir
									</a>
								</td>
							</tr>
						<?php } ?>
					<?php } else { ?>
						<tr>
							<td colspan="3">Nenhum registro encontrado</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>

		</div> <!-- /painel-body -->
	</section> <!-- /panel-default -->
</div> <!-- /page-profile -->