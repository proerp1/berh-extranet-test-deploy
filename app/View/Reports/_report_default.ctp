<thead>
    <tr class="fw-bolder text-muted bg-light">
        <th>CNPJ</th>
        <th>
            Cliente
        </th>
        <th>
            Nome
        </th>
        <th>
            CPF
        </th>
        <th>
            Departamento
        </th>
        <th>
            Dias Úteis
        </th>
        <th>
            Código Operadora
        </th>
        <th>
            Código do Benefício (Ìtem)
        </th>
        <th>
            Valor Unitário
        </th>
        <th>
            Quantidade
        </th>
        <th>
            Var
        </th>
        <th>
            Total
        </th>
        <th>
            SIC Ctba
        </th>
    </tr>
</thead>
<tbody>
    <?php $total = 0; ?>
    <?php for ($i = 0; $i < count($data); $i++) {
        $total += $data[$i]["OrderItem"]["subtotal_not_formated"];
    ?>
        <tr>
            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Customer"]["documento"]; ?></td>
            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Customer"]["nome_secundario"]; ?></td>
            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerUser"]["name"]; ?></td>
            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerUser"]["cpf"]; ?></td>
            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['CustomerDepartment']["name"]; ?></td>
            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["OrderItem"]["working_days"]; ?></td>
            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Supplier']["code"]; ?></td>
            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Benefit']["code"]; ?></td>
            <td class="fw-bold fs-7 ps-4">R$<?php echo $data[$i]["CustomerUserItinerary"]["unit_price"]; ?></td>
            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerUserItinerary"]["quantity"]; ?></td>
            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["OrderItem"]["var"]; ?></td>
            <td class="fw-bold fs-7 ps-4">R$<?php echo $data[$i]["OrderItem"]["total"]; ?></td>
            <td>-</td>
        </tr>
    <?php } ?>
    <tr>
        <td colspan="10"></td>
        <td>Total</td>
        <td>R$<?php echo number_format($total, 2, ',', '.'); ?></td>
    </tr>
</tbody>