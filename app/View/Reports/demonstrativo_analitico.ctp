<div class="card mb-5 mb-xl-8">
	<div class="card-header border-0 pt-6 pb-6">
		<div class="card-title">
			<?php echo $this->Form->create('Bot', array("id" => "js-form-submit", "action" => "/".$form_action."/", "method" => "post", "autocomplete" => "off")); ?>
				<div class="row">
					<div class="col">
						<label class="form-label fs-5 fw-bold mb-3">Consulta por Logon</label>
						<div class="input-group" id="datepicker">
							<input type="date" class="form-control" autocomplete="off" id="de" name="de" value="<?php echo isset($_REQUEST["de"]) ? $_REQUEST["de"] : ""; ?>" required>
							<span class="input-group-text" style="padding: 5px;"> até </span>
							<input type="date" class="form-control" autocomplete="off" id="ate" name="ate" value="<?php echo isset($_REQUEST["ate"]) ? $_REQUEST["ate"] : ""; ?>">
						</div>
					</div>
					<div class="col">
						<label class="form-label fs-5 fw-bold mb-3">Logon a ser consultado:</label>
						<div class="d-flex align-items-center my-1">
							<span class="position-absolute ms-6">
								<i class="fas fa-search"></i>
							</span>
							<?php echo $this->Form->input('logon', array("div" => false, "required" => true, "label" => false, "placeholder" => "Logon", "class" => "form-control form-control-solid ps-15"));  ?>
						</div>
					</div>
					<div class="col-12 mt-3">
						<button type="submit" class="btn btn-primary js-salvar waves-effect" data-loading-text="Aguarde...">Download do Arquivo</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>