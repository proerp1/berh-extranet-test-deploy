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
            Tipo De Conta
        </th>
        <th>
            Banco
        </th>
        <th>
            Cod.
        </th>
        <th>
            Ag.
        </th>
        <th>
            Dig.Ag,
        </th>
        <th>
            Conta
        </th>
        <th>
            Dig.Conta
        </th>
        <th>
            Tipo Chave
        </th>
        <th>
            Chave Pix
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
            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['BankAccountType']["description"]; ?></td>
            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['BankCode']["name"]; ?></td>
            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['BankCode']["code"]; ?></td>
            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['CustomerUserBankAccount']["branch_number"]; ?></td>
            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerUserBankAccount"]["branch_digit"]; ?></td>
            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerUserBankAccount"]["acc_number"]; ?></td>
            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerUserBankAccount"]["acc_digit"]; ?></td>
            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerUserBankAccount"]["pix_type"]; ?></td>
            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerUserBankAccount"]["pix_id"]; ?></td>
        </tr>
    <?php } ?>
</tbody>