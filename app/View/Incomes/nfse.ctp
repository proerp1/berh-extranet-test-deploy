<script type="text/javascript">
    $(document).ready(function() {
        $('#confirm_cancel_nfse').on('click', function(e) {
            $('#modal-confirm').modal('show');
        });

        $('#exit').on('click', function(e) {
            $('#modal-confirm').modal('hide');
        });

        $('#cancel_nfse').on('click', function(e) {
            window.location = "<?php echo $this->base.'/incomes/cancela_nfse/'.$id ?>";
        });
    })
</script>

<?php
	echo $this->element("abas_incomes", ['id' => $id]);
?>
<div class="card mb-5 mb-xl-8">
	<div class="card-body">
        <div class="mb-7 col">
            <label class="fw-semibold fs-6 mb-2">Status da NFS-e</label><br>
            <span class='badge <?php echo $this->request->data["NfseStatus"]["label"] ?>'>
                <?php echo $this->request->data["NfseStatus"]["name"] ?>
            </span>
        </div>
        <div class="col">
            <h4>Prévia da NFS-e</h4>
            <?php echo nl2br($preview_data['obs']) ?>
        </div>
        <hr>
        <div class="col">
            <?php if ($this->request->data['Income']['nfse_status_id'] == 105){ ?>
                <a href="<?php echo $this->base.'/incomes/cria_nfse/'.$id ?>" class="btn btn-success" data-loading-text="Aguarde...">Enviar</a>
            <?php } else if ($this->request->data['Income']['nfse_status_id'] == 107){ ?>
                <button id="confirm_cancel_nfse" type="submit" class="btn btn-danger">Cancelar</button>
            <?php } ?>
            <?php if ($pdf_link){ ?>
                <a href="<?php echo $pdf_link ?>" target="_blank" class="btn btn-secondary">Imprimir</a>
            <?php } ?>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-confirm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">Confirmação</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body py-lg-10 px-lg-10">
                <div class="fv-row mb-10">
                    <div class="fs-2 fw-bold">Deseja cancelar a nota fiscal?</div>
                    <div class="fs-6">Essa ação não pode ser desfeita</div>
                </div>
            </div>

            <div class="modal-footer flex-right">
                <button type="button" class="btn btn-light" id="exit">Não Cancelar</button>
                <button type="button" class="btn btn-danger" id="cancel_nfse">Cancelar NFS-e</button>
            </div>
        </div>
    </div>
</div>