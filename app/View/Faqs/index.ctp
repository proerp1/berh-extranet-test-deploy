<?php $url_novo = $this->Html->url(['controller' => 'faqs', 'action' => 'add']); ?>

<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(['controller' => 'faqs', 'action' => 'index']); ?>" method="get" role="form" id="busca" autocomplete="off">
        <div class="card-header border-0 pt-6 pb-6">
            <div class="card-title">
                <div class="row">
                    <div class="col d-flex align-items-center">
                        <span class="position-absolute ms-6">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control form-control-solid ps-15" id="q" name="q"
                            value="<?php echo isset($_GET["q"]) ? h($_GET["q"]) : ""; ?>" placeholder="Buscar pergunta..." />
                    </div>
                </div>
            </div>

            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                    <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                        <i class="fas fa-filter"></i> Filtro
                    </button>

                    <a href="<?php echo $url_novo; ?>" class="btn btn-primary me-3">Novo</a>

                    <div class="menu menu-sub menu-sub-dropdown w-300px w-md-400px" data-kt-menu="true" id="kt-toolbar-filter">
                        <div class="px-7 py-5">
                            <div class="fs-4 text-dark fw-bolder">Opções de Filtro</div>
                        </div>
                        <div class="separator border-gray-200"></div>
                        <div class="px-7 py-5">
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Categoria:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true"
                                        data-placeholder="Selecione" data-allow-clear="true" name="categoria" id="categoria">
                                    <option></option>
                                    <?php foreach ($categoriasFaq as $id => $nome): ?>
                                        <option value="<?php echo $id; ?>" <?php echo (isset($_GET['categoria']) && $_GET['categoria'] == $id) ? 'selected' : ''; ?>>
                                            <?php echo h($nome); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Sistema:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true"
                                        data-placeholder="Todos" name="sistema" id="sistema">
                                    <option></option>
                                    <option value="sig" <?php echo (isset($_GET['sistema']) && $_GET['sistema'] === 'sig') ? 'selected' : ''; ?>>SIG (Extranet)</option>
                                    <option value="cliente" <?php echo (isset($_GET['sistema']) && $_GET['sistema'] === 'cliente') ? 'selected' : ''; ?>>Cliente</option>
                                    <option value="todos" <?php echo (isset($_GET['sistema']) && $_GET['sistema'] === 'todos') ? 'selected' : ''; ?>>SIG e Cliente</option>
                                </select>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="reset" class="btn btn-light btn-active-light-primary me-2" data-kt-menu-dismiss="true" data-kt-customer-table-filter="reset">Limpar</button>
                                <button type="submit" class="btn btn-primary" data-kt-menu-dismiss="true" data-kt-customer-table-filter="filter">Filtrar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="card-body pt-0 py-3">
        <?php echo $this->element("pagination"); ?>
        <br>
        <div class="table-responsive">
            <table class="table table-row-bordered gy-5">
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th>Pergunta</th>
                        <th>Resposta</th>
                        <th>Categoria</th>
                        <th>Anexo</th>
                        <th>Destino</th>
                        <th class="w-200px min-w-200px">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data)) : ?>
                        <?php foreach ($data as $faq): ?>
                            <tr>
                                <td class="fw-bold fs-7"><?php echo h($faq["Faq"]["pergunta"]); ?></td>
                                <td class="fw-bold fs-7"><?php echo h($faq["Faq"]["resposta"]); ?></td>
                                <td class="fw-bold fs-7"><?php echo h($faq["CategoriaFaq"]["nome"]); ?></td>
                                <td class="fw-bold fs-7">
    <?php if (!empty($faq["Faq"]["file"])): ?>
        <a download href="<?php echo $this->webroot . 'files/faq/file/' . $faq["Faq"]["id"] . '/' . h($faq["Faq"]["file"]); ?>"
           style="color: #ED0677; font-weight: 500;">
           📎 <?php echo h($faq["Faq"]["file"]); ?>
        </a>
    <?php else: ?>
        —
    <?php endif; ?>
</td>

                                <td class="fw-bold fs-7">
                                    <?php
                                        switch ($faq["Faq"]["sistema_destino"]) {
                                            case 'sig': echo 'SIG'; break;
                                            case 'cliente': echo 'Cliente'; break;
                                            case 'todos': echo 'SIG e Cliente'; break;
                                            default: echo '-'; break;
                                        }
                                    ?>
                                </td>
                                <td>
                                    <a href="<?php echo $this->Html->url(['controller' => 'faqs', 'action' => 'edit', $faq["Faq"]["id"]]); ?>" class="btn btn-info btn-sm">Editar</a>
                                    <a href="javascript:void(0);" onclick="verConfirm('<?php echo $this->Html->url(['controller' => 'faqs', 'action' => 'delete', $faq["Faq"]["id"]]); ?>');" class="btn btn-danger btn-sm">Excluir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="5" class="fw-bold fs-7">Nenhum FAQ encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php echo $this->element("pagination"); ?>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#q, #categoria, #sistema').on('change', function () {
        $("#busca").submit();
    });

    $('[data-kt-customer-table-filter="reset"]').on('click', function () {
        $('#q').val('');
        $('#categoria').val(null).trigger('change');
        $('#sistema').val(null).trigger('change');
        $("#busca").submit();
    });
});
</script>
