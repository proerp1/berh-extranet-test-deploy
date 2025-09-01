<?php
if (isset($id)) {
    $url = $this->here;
    echo $this->element("abas_suppliers", array('id' => $id, 'url' => $url));
}
?>

<div class="card mb-5 mb-xl-8">
    <div class="card-header border-0 pt-5">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bolder fs-3 mb-1">Faixas de Volume - <?php echo h($supplier['Supplier']['nome_fantasia']); ?></span>
            <span class="text-muted mt-1 fw-bold fs-7">Configuração de faixas de volume</span>
            <?php if (!empty($supplier['Supplier']['tipo_cobranca'])): ?>
                <span class="badge badge-light-primary mt-2">
                    <i class="fas fa-calculator me-1"></i>
                    Tipo de Cobrança: <?php echo $supplier['Supplier']['tipo_cobranca'] == 'pedido' ? 'Por Pedido' : 'Por CPF'; ?>
                </span>
            <?php endif; ?>
        </h3>
        <div class="card-toolbar">
            <a href="<?php echo $this->base . '/suppliers/add_volume_tier/' . $id; ?>" class="btn btn-primary">
                <i class="fa fa-file"></i>
                Nova Faixa
            </a>
        </div>
    </div>

    <div class="card-body py-3">
        <?php if (empty($data)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>Nenhuma faixa cadastrada</strong><br>
                Para que os cálculos de repasse funcionem, você precisa cadastrar ao menos uma faixa de volume.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                    <thead>
                        <tr class="fw-bolder text-muted">
                            <th class="min-w-100px">De (Qtd)</th>
                            <th class="min-w-100px">Até (Qtd)</th>
                            <th class="min-w-120px">
                                <?php if ($supplier['Supplier']['transfer_fee_type'] == 1): ?>
                                    Valor Fixo
                                <?php else: ?>
                                    % Repasse
                                <?php endif; ?>
                            </th>
                            <th class="min-w-100px text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $row): ?>
                            <tr>
                                <td>
                                    <span class="text-dark fw-bolder text-hover-primary d-block fs-6">
                                        <?php echo number_format($row['SupplierVolumeTier']['de_qtd'], 0, ',', '.'); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="text-dark fw-bolder text-hover-primary d-block fs-6">
                                        <?php echo number_format($row['SupplierVolumeTier']['ate_qtd'], 0, ',', '.'); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="text-dark fw-bolder text-hover-primary d-block fs-6">
                                        <?php if ($supplier['Supplier']['transfer_fee_type'] == 1): ?>
                                            <?php echo isset($row['SupplierVolumeTier']['valor_fixo']) ? 'R$ ' . $row['SupplierVolumeTier']['valor_fixo'] : '-'; ?>
                                        <?php else: ?>
                                            <?php echo isset($row['SupplierVolumeTier']['percentual_repasse']) ? $row['SupplierVolumeTier']['percentual_repasse'] . '%' : '-'; ?>
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end flex-shrink-0">
                                        <a href="<?php echo $this->base . '/suppliers/edit_volume_tier/' . $id . '/' . $row['SupplierVolumeTier']['id']; ?>" 
                                           class="btn btn-sm btn-warning me-1" 
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?php echo $this->base . '/suppliers/delete_volume_tier/' . $id . '/' . $row['SupplierVolumeTier']['id']; ?>" 
                                           class="btn btn-sm btn-danger" 
                                           id="delete-tier-<?php echo $row['SupplierVolumeTier']['id']; ?>"
                                           onclick="return confirm('Tem certeza que deseja excluir esta faixa de volume? Esta ação não pode ser desfeita.')"
                                           title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.btn-danger[id^="delete-tier-"]').on('click', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        confirm('Tem certeza que deseja excluir esta faixa de volume? Esta ação não pode ser desfeita.', url);
        return false;
    });
});
</script>