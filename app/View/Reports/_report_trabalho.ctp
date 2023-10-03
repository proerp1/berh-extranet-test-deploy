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
            CEP
        </th>
        <th>
            Rua
        </th>
        <th>
            Nº
        </th>
        <th>
            Compto
        </th>
        <th>
            Bairro
        </th>
        <th>
            Cidade
        </th>
        <th>
            Estado
        </th>
    </tr>
</thead>
<tbody>
    <?php $total = 0; ?>
    <?php for ($i = 0; $i < count($data); $i++) {?>
        <tr>
            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Customer"]["documento"]; ?></td>
            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Customer"]["nome_secundario"]; ?></td>
            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerUser"]["name"]; ?></td>
            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerUser"]["cpf"]; ?></td>
            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['CustomerUserAddress']["zip_code"]; ?></td>
            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['CustomerUserAddress']["address_line"]; ?></td>
            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['CustomerUserAddress']["address_number"]; ?></td>
            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['CustomerUserAddress']["address_complement"]; ?></td>
            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerUserAddress"]["neighborhood"]; ?></td>
            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerUserAddress"]["city"]; ?></td>
            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerUserAddress"]["state"]; ?></td>
        </tr>
    <?php } ?>
</tbody>