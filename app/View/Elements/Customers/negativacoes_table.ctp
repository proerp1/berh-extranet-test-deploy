<?php echo $this->element("table"); ?>
    <thead>
        <tr class="fw-bolder text-muted bg-light">
            <th class="ps-4 w-150px min-w-150px rounded-start">Status</th>
            <th>Nome</th>
            <th>Tipo</th>
            <th>Documento</th>
            <th>Número do titulo negativado</th>
            <th>Valor</th>
            <th>Coobrigado/Avalista</th>
            <th>Cadastro</th>
            <th <?php echo isset($pdf) ? 'class="w-300px min-w-300px rounded-end"' : '' ?>>Erros</th>
            <?php if (!isset($pdf)): ?>
                <th class="w-300px min-w-300px rounded-end">Ações</th>
            <?php endif ?>
        </tr>
    </thead>
    <tbody>
        <?php $total = 0; ?>
        <?php if ($data) { ?>
            <?php for ($i=0; $i < count($data); $i++) { ?>
                <?php $erros = $data[$i]['CadastroPefinErros']; ?>
                <?php 
                    $status = $data[$i]["Status"]["name"];
                    $sLabel = $data[$i]["Status"]["label"];
                ?>
                <tr>
                    <td class="fw-bold fs-7 ps-4">
                        <span class='badge <?php echo $sLabel ?>'>
                            <?php echo $status ?>
                        </span>
                    </td>
                    <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['CadastroPefin']['nome'] ?></td>
                    <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['NaturezaOperacao']['nome'] ?></td>
                    <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['CadastroPefin']['documento'] ?></td>
                    <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['CadastroPefin']['numero_titulo'] ?></td>
                    <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['CadastroPefin']['valor'] ?></td>
                    <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['CadastroPefin']['coobrigado_nome'] ?></td>
                    <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['CadastroPefin']['created']; //date('d/m/Y H:i:s', strtotime($data[$i]['CadastroPefin']['created'])) ?></td>
                    <td class="fw-bold fs-7 ps-4">
                        <?php 
                            if (!empty($erros) and $data[$i]["Status"]["id"] == 23) {
                                for ($a=0; $a < count($erros); $a++) { 
                                    echo $erros[$a]['ErrosPefin']['descricao'].'<br>';
                                }
                            }
                        ?>
                    </td>
                    <?php if (!isset($pdf)): ?>
                        <td class="fw-bold fs-7 ps-4">
                            <a href="<?php echo $this->base.'/negativacao/view/'.$data[$i]["CadastroPefin"]["id"]; ?>" class="btn btn-info btn-sm">Detalhes</a>
                            <a target="_blank" href="<?php echo $this->base.'/negativacao/imprimir/'.$data[$i]["CadastroPefin"]["id"]; ?>" class="btn btn-primary  btn-icon btn-sm"><i class="fa fa-print"></i></a>
                            <?php if ($data[$i]["Status"]["id"] == 25 && !empty($data[$i]['MotivoBaixa'])): ?>
                                <a href="<?php echo $this->base.'/negativacao/baixa/'.$data[$i]["CadastroPefin"]["id"]; ?>" class="btn btn-danger btn-sm">Baixar</a>
                            <?php endif ?>
                            <?php if ($data[$i]["Status"]["id"] == 23): ?>
                                <a href="javascript:" onclick="verConfirm('<?php echo $this->base.'/negativacao/delete/'.$data[$i]["CadastroPefin"]["id"]; ?>');" rel="tooltip" title="Excluir" class="btn btn-danger btn-icon btn-sm">
                                    <i class="fa fa-trash"></i>
                                </a>
                            <?php endif ?>
                        </td>
                    <?php endif ?>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="10" class="fw-bold fs-7 ps-4">Nenhum registro encontrado</td>
            </tr>
        <?php } ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="7" style="text-align: right;">Total:</th>
            <th><?php echo count($data) ?></th>
        </tr>
    </tfoot>
</table>
