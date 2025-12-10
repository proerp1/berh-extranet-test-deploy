<?php
echo $this->element("abas_resales", array('id' => $id));

function cleanString(string $str): string {
    $str = strip_tags($str);
    $str = html_entity_decode($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $str = preg_replace('/^\s+|\s+$/u', '', $str);
    return preg_replace('/\s+/u', ' ', $str);
}

function compararValoresLog($log): string {
    $old_json = json_decode($log['Log']['old_value'], true);
    $new_json = json_decode($log['Log']['new_value'], true);

    $old_values = $old_json[$log['Log']['log_table']];
    $new_values = $new_json[$log['Log']['log_table']];
    $html = "<ul>";

    $todasChaves = array_intersect(array_keys($old_values), array_keys($new_values));

    foreach ($todasChaves as $chave) {
        $oldValue = $old_values[$chave] ?? null;
        $newValue = $new_values[$chave] ?? null;
        if ($chave == 'user_updated_id' || $chave == 'updated' || is_array($newValue) || is_array($oldValue)) continue;

        $parsedOldValue = cleanString((string)$oldValue);
        $parsedNewValue = cleanString((string)$newValue);

        if ($parsedOldValue != $parsedNewValue) {
            $html .= "<li class='mb-3'>"
                . cleanString((string)$chave) . ": "
                . "<span class='old_value'>" . ($parsedOldValue ?: '&nbsp;') . "</span>&nbsp;=>&nbsp;"
                . "<span class='new_value'>" . $parsedNewValue . "</span>"
                . "</li>";
        }
    }

    $html .= "</ul>";

    return $html == '<ul></ul>' ? 'Nenhuma alteração realizada.' : $html;
}
?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-0 py-3">
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th>Data e hora da Alteração</th>
                        <th>Usuário Alteração</th>
                        <th>Alteração</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($data) { ?>
                        <?php for ($i=0; $i < count($data); $i++) { ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4"><?php echo date('d/m/Y H:i:s', strtotime($data[$i]['Log']['log_date'])); ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Creator']['name']; ?></td>
                                <td class="fw-bold fs-7 ps-4">
                                    <?php echo compararValoresLog($data[$i]); ?>
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
        <?php echo $this->element("pagination"); ?>
    </div>
</div>

<style>
    .new_value {
        background: #60fda7;
        color: #262626;
        border-radius: 5px;
        padding: 2px;
    }
    .old_value {
        background: #f15076;
        color: #262626;
        border-radius: 5px;
        padding: 2px;
    }
</style>
