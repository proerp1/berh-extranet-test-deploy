<?php if (!empty($itineraries)): ?>
<div class="table-responsive">
    <table class="table table-row-bordered table-row-gray-100 align-middle gs-0 gy-3">
        <thead>
            <tr class="fw-bolder text-muted">
                <th>Beneficiário</th>
                <th>CPF</th>
                <th>E-mail</th>
                <th>Status do Vínculo</th>
                <th>Dias Úteis</th>
                <th>Quantidade</th>
                <th>Preço Unitário</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($itineraries as $item): ?>
            <tr>
                <td class="fw-bold fs-7"><?php echo isset($item['CustomerUser']['name']) ? $item['CustomerUser']['name'] : ''; ?></td>
                <td class="fw-bold fs-7"><?php echo isset($item['CustomerUser']['cpf']) ? $item['CustomerUser']['cpf'] : ''; ?></td>
                <td class="fw-bold fs-7"><?php echo isset($item['CustomerUser']['email']) ? $item['CustomerUser']['email'] : ''; ?></td>
                <td>
                    <span class='badge <?php echo isset($item['Status']['label']) ? $item['Status']['label'] : '' ?>'>
                        <?php echo isset($item['Status']['name']) ? $item['Status']['name'] : 'N/A' ?>
                    </span>
                </td>
                <td class="fw-bold fs-7"><?php echo isset($item['CustomerUserItinerary']['working_days']) ? $item['CustomerUserItinerary']['working_days'] : '0'; ?></td>
                <td class="fw-bold fs-7"><?php echo isset($item['CustomerUserItinerary']['quantity']) ? $item['CustomerUserItinerary']['quantity'] : '0'; ?></td>
                <td class="fw-bold fs-7">R$ <?php echo isset($item['CustomerUserItinerary']['unit_price']) ? $item['CustomerUserItinerary']['unit_price'] : '0,00'; ?></td>
                <td>
                    <a href="<?php echo $this->base . '/customer_users/itineraries/' . $customerId . '/' . $item['CustomerUserItinerary']['customer_user_id']; ?>"
                       class="btn btn-sm btn-info" target="_blank">
                        Ver Detalhes Completos
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="mt-4">
    <p class="fw-bold">Total de beneficiários: <?php echo count($itineraries); ?></p>
</div>

<?php else: ?>
<div class="alert alert-info">
    Nenhum beneficiário encontrado para este benefício.
</div>
<?php endif; ?>
