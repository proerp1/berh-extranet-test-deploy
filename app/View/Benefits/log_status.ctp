
<?php
    echo $this->element("abas_beneficios", array('id' => $id));
?>
        <div class="table-responsive">
        
                <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th>Nome</th>
                        <th>Data</th>
                        <th>LOG</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($data) { ?>
                        <?php for ($i=0; $i < count($data); $i++) { ?>
                            <tr>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['User']['name'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["LogBenefits"]["log_date"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["LogBenefits"]["old_value"]; ?></td>
                                
                                <td class="fw-bold fs-7 ps-4">
                                   
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4" colspan="4">Nenhum registro encontrado</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
       
    </div>
</div>

<script>
    $( document ).ready(function() {
        $('[data-kt-customer-table-filter="reset"]').on('click', function () {
            $("#t").val(null).trigger('change');
            $("#q").val(null);

            $("#busca").submit();
        });

        $('#q').on('change', function () {
            $("#busca").submit();
        });
    });
</script>