<?php echo $this->Html->script('jquery-maskmoney'); ?>
<script type="text/javascript">
    $(document).ready(function(){
        $("#ChargesHistoryGenerateNewIncome").on("change", function(){
            var val = $(this).val();

            if (val == 1) {
                $(".nova_conta").show();
                $(".retorno").hide();
            } else {
                $(".nova_conta").hide();
                $(".retorno").show();
            }
        })

        $("#ChargesHistoryCallStatus").on("change", function(){
            var val = $(this).val();

            if (val == 1) {
                $(".ligacao_sucesso").show();
                $("input:not([type='hidden'])").prop('disabled',false);
                $("textarea").prop('disabled',false);
                $("select:not(#ChargesHistoryCallStatus)").prop('disabled',false);
            } else {
                $(".ligacao_sucesso").hide();
                $("input:not([type='hidden'])").prop('disabled',true);
                $("textarea").prop('disabled',true);
                $("select:not(#ChargesHistoryCallStatus)").prop('disabled',true);
            }
        });

        $('.money_exchange').maskMoney({
            decimal: ',',
            thousands: '.',
            precision: 2
        });

        $(".datepicker").datepicker({format: 'dd/mm/yyyy', weekStart: 1, autoclose: true, language: "pt-BR", todayHighlight: true, toggleActive: true , startDate: "today"});
    })
</script>

<ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
    <li class="nav-item">
        <a class="nav-link" href="<?php echo $this->base.'/cobrancas/visualizar/'.$id ?>">Dados</a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="<?php echo $this->base.'/cobrancas/historico/'.$id; ?>">Histórico</a>
    </li>
</ul>

<div class="card mb-5 mb-xl-8">
    <div class="card-body">
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th class="ps-4 w-250px min-w-250px rounded-start">Status da ligação</th>
                        <th>Histórico</th>
                        <th>Usuário</th>
                        <th>Data criação</th>
                        <th>Novo boleto gerado?</th>
                        <th>Data de retorno</th>
                        <th class="w-150px min-w-150px rounded-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($data) { ?>
                        <?php for ($i=0; $i < count($data); $i++) { ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["ChargesHistory"]["call_status"] == 1 ? "Com sucesso" : "Sem sucesso"; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["ChargesHistory"]["text"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["User"]["name"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo date('d/m/Y H:i:s', strtotime($data[$i]["ChargesHistory"]["created"])); ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["ChargesHistory"]["generate_new_income"] == 1 ? 'Sim' : 'Não'; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["ChargesHistory"]["return_date"]; ?></td>
                                <td class="fw-bold fs-7 ps-4">
                                    <a href="javascript:" onclick="verConfirm('<?php echo $this->base.'/cobrancas/delete_historico/'.$id.'/'.$data[$i]["ChargesHistory"]["id"]; ?>');" rel="tooltip" title="Excluir" class="btn btn-danger btn-sm">
                                        Excluir
                                    </a>
                                </td>
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
    </div>
</div>