<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url([ "controller" => "contas_pagas", "action" => "index"]); ?>" role="form" id="busca" autocomplete="off">
        <?php if (isset($_GET['logon'])): ?>
            <input type="hidden" name="logon" value="">
        <?php endif ?>
        <div class="card-header border-0 pt-6 pb-6">
            <div class="card-title">
                <div class="row">
                    <div class="col d-flex align-items-center">
                        <span class="position-absolute ms-6">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control form-control-solid ps-15" id="q" name="q" value="<?php echo isset($_GET["q"]) ? $_GET["q"] : ""; ?>" placeholder="<?php echo isset($_GET['logon']) ? 'Digite o logon' : 'Buscar' ?>" />
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
                        <th class="ps-4 w-150px min-w-150px rounded-start">Status</th>
                        <th>Código do Cliente</th>
                        <th>Nome</th>
                        <th>Valor R$</th>
                        <th class="w-200px min-w-200px rounded-end">Data</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($dados) { ?>
                        <?php for ($i=0; $i < count($dados); $i++) { ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4">
                                    <span class='badge <?php echo $dados[$i]["Status"]["label"] ?>'>
                                        <?php echo $dados[$i]["Status"]["name"] ?>
                                    </span>
                                </td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $dados[$i]["Customer"]["codigo_associado"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $dados[$i]["Customer"]["nome_primario"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $dados[$i]["Income"]["valor_total"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $dados[$i]["Income"]["created"]; ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4" colspan="6">Nenhum registro encontrado</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <?php echo $dados ? $this->element("pagination") : ''; ?>
    </div>
</div>

<script>
    $( document ).ready(function() {
        $('#q').on('change', function () {
            $("#busca").submit();
        });
    });
</script>