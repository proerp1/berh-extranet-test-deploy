<?php

class ArrayCompareComponent extends Component {
    private $skipKeys = ['updated', 'user_updated_id', 'id'];

    private function getValue($value, $key, $options) {
        $optionsKeys = array_keys($options);
        if (in_array($key, $optionsKeys)) {
            return $options[$key][$value] ?? $value;
        }
        return $value;
    }

    function arrayDiff($old, $new, $keyTranslation, $selectOptions) {
        $messages = [];

        // Verificar valores alterados ou adicionados
        foreach ($new as $key => $value) {
            if (str_contains($key, '_nao_formatado') || in_array($key, $this->skipKeys)) continue;

            $translatedKey = $keyTranslation[$key] ?? $key;

            $newValue = $this->getValue($value, $key, $selectOptions);
            $oldValue = $this->getValue($old[$key], $key, $selectOptions);
            if (!array_key_exists($key, $old)) {
                $messages[] = "O campo '$translatedKey' foi adicionado com valor '$newValue'.";
            } elseif ($old[$key] != $value) {
                $messages[] = "O campo '$translatedKey' mudou de '{$oldValue}' para '$newValue'.";
            }
        }

        if (empty($messages)) {
            return "Nenhuma alteração encontrada.";
        }

        return implode("<br>", $messages);
    }

    function arrayDiffRecursive($old, $new) {
        $diff = [];

        foreach ($new as $key => $value) {
            // Valor novo ainda não existia → adicionado
            if (!array_key_exists($key, $old)) {
                $diff[$key] = ['old' => null, 'new' => $value];
                continue;
            }

            // Se for array, compara recursivamente
            if (is_array($value)) {
                $subDiff = arrayDiffRecursive($old[$key], $value);
                if (!empty($subDiff)) {
                    $diff[$key] = $subDiff;
                }
            } else {
                // Valor alterado
                if ($old[$key] !== $value) {
                    $diff[$key] = [
                        'old' => $old[$key],
                        'new' => $value
                    ];
                }
            }
        }

        // Detectar valores removidos
        foreach ($old as $key => $value) {
            if (!array_key_exists($key, $new)) {
                $diff[$key] = ['old' => $value, 'new' => null];
            }
        }

        return $diff;
    }
}
