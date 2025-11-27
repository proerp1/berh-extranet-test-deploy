<script type="text/javascript">
    $(document).ready(function() {
        let nfseId = null
        $('.confirm_cancel_nfse').on('click', function(e) {
            nfseId = e.target.dataset.id
            $('#modal-confirm').modal('show');
        });

        $('#exit').on('click', function(e) {
            nfseId = null
            $('#modal-confirm').modal('hide');
        });

        $('#cancel_nfse').on('click', function(e) {
            nfseId = null
            window.location = "<?php echo $this->base.'/incomes/cancela_nfse/' ?>"+nfseId;
        });
    })
</script>

<?php
	echo $this->element("abas_incomes", ['id' => $id]);

    $hasMergedNfse = !!array_filter($nfses, function ($nfse) {
        return $nfse['tipo'] === 'ge-tpp' && isset($nfse['id']);
    });

    $hasSingleNfse = !!array_filter($nfses, function ($nfse) {
        return $nfse['tipo'] !== 'ge-tpp' && isset($nfse['id']);
    });

    $nfse_type_title = ['ge' => 'Gestão Eficiente', 'tpp' => 'ADM', 'ge-tpp' => 'GE e ADM'];
?>
<div class="card mb-5 mb-xl-8">
	<div class="card-body">
        <div class="row">
            <?php for ($i = 0; $i < count($nfses); $i++) { ?>
                <?php $nfse = $nfses[$i] ?>
                <?php echo $this->Form->create('Income', array("id" => "js-form-submit", "action" => "/cria_nfse/$id/".$nfse['tipo'], "class" => 'col', "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false])); ?>
                    <?php if (($nfse['tipo'] === 'ge-tpp' && !$hasSingleNfse) || ($nfse['tipo'] !== 'ge-tpp' && !$hasMergedNfse)) { ?>
                        <h2><?php echo $nfse_type_title[$nfse['tipo']] ?></h2>
                            <div>
                                <label class="fw-bold fs-6 mb-2 d-block">Status da NFS-e</label>
                                <span class='badge <?php echo $nfse["Status"]["label"] ?>'>
                                    <?php echo $nfse["Status"]["name"] ?>
                                </span>
                            </div>
                            <div class="mt-5">
                                <label class="fw-bold fs-6 mb-2 d-block">Prévia da NFS-e</label>
                                <p><?php echo nl2br($nfse['preview']) ?></p>
                                <?php if (isset($nfse['description'])) { ?>
                                   <p><?= nl2br($nfse['description']) ?></p>
                                <?php } else { ?>
                                    <div class="col-12 mb-2">
                                        <label class="form-label">Complemento da Nota Fiscal</label>
                                        <textarea name="data[IncomeNfse][description]" class="form-control auto-expand" style="height: 250px"><?php
                                          echo $this->request->data['Customer']['observacao_notafiscal'] ?></textarea>
                                    </div>
                                <?php } ?>
                            <?php if (!isset($nfse['status_id'])){ ?>
                                <button type="submit" class="btn btn-success" data-loading-text="Aguarde...">Enviar</button>
                            <?php } else if ($nfse['status_id'] == 106){ ?>
                                <a href="<?php echo $this->base.'/incomes/check_nfse/'.$nfse['id'] ?>" class="btn btn-warning">
                                    Verificar Processamento
                                </a>
                            <?php } else if ($nfse['status_id'] == 107){ ?>
                                <button data-id="<?php echo $nfse['id'] ?>" type="button" class="btn btn-danger confirm_cancel_nfse">Cancelar</button>
                            <?php } ?>
                            <?php if (isset($nfse['pdf_link'])){ ?>
                                <a href="<?php echo $this->base.'/incomes/imprime_nfse/'.$nfse['id'] ?>" class="btn btn-secondary">Imprimir</a>
                            <?php } ?>
                            </div>
                    <?php } ?>
                </form>
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