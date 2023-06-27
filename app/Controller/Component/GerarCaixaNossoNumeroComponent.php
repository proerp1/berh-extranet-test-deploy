<?php

class GerarCaixaNossoNumeroComponent extends Component {
	public $components = array('Session');

	public function gerar($income_id){
      // Composição Nosso Numero - CEF SIGCB
      $dadosboleto["nosso_numero1"] = "000"; // tamanho 3
      $dadosboleto["nosso_numero_const1"] = "1"; //constanto 1 , 1=registrada , 2=sem registro
      $dadosboleto["nosso_numero2"] = "000"; // tamanho 3
      $dadosboleto["nosso_numero_const2"] = "4"; //constanto 2 , 4=emitido pelo proprio cliente
      $dadosboleto["nosso_numero3"] = str_pad($income_id, 9, '0', STR_PAD_LEFT); // tamanho 9

      //nosso número (sem dv) é 17 digitos
      $nnum = $this->formataNumero($dadosboleto["nosso_numero_const1"],1,0).$this->formataNumero($dadosboleto["nosso_numero_const2"],1,0).$this->formataNumero($dadosboleto["nosso_numero1"],3,0).$this->formataNumero($dadosboleto["nosso_numero2"],3,0).$this->formataNumero($dadosboleto["nosso_numero3"],9,0);
      //nosso número completo (com dv) com 18 digitos
      $nossonumero = $nnum . $this->digitoVerificadorNossonumero($nnum);

      return $nossonumero;
    }

    function digitoVerificadorNossonumero($numero) {
      $resto2 = $this->modulo11($numero, 9, 1);
         $digito = 11 - $resto2;
         if ($digito == 10 || $digito == 11) {
            $dv = 0;
         } else {
            $dv = $digito;
         }
       return $dv;
    }

    function formataNumero($numero,$loop,$insert,$tipo = "geral") {
      if ($tipo == "geral") {
        $numero = str_replace(",","",$numero);
        while(strlen($numero)<$loop){
          $numero = $insert . $numero;
        }
      }
      if ($tipo == "valor") {
        /*
        retira as virgulas
        formata o numero
        preenche com zeros
        */
        $numero = str_replace(",","",$numero);
        while(strlen($numero)<$loop){
          $numero = $insert . $numero;
        }
      }
      if ($tipo == "convenio") {
        while(strlen($numero)<$loop){
          $numero = $numero . $insert;
        }
      }
      return $numero;
    }

    function modulo11($num, $base=9, $r=0)  {
        /**
         *   Autor:
         *           Pablo Costa <pablo@users.sourceforge.net>
         *
         *   Função:
         *    Calculo do Modulo 11 para geracao do digito verificador 
         *    de boletos bancarios conforme documentos obtidos 
         *    da Febraban - www.febraban.org.br 
         *
         *   Entrada:
         *     $num: string numérica para a qual se deseja calcularo digito verificador;
         *     $base: valor maximo de multiplicacao [2-$base]
         *     $r: quando especificado um devolve somente o resto
         *
         *   Saída:
         *     Retorna o Digito verificador.
         *
         *   Observações:
         *     - Script desenvolvido sem nenhum reaproveitamento de código pré existente.
         *     - Assume-se que a verificação do formato das variáveis de entrada é feita antes da execução deste script.
         */                                        

        $soma = 0;
        $fator = 2;

        /* Separacao dos numeros */
        for ($i = strlen($num); $i > 0; $i--) {
            // pega cada numero isoladamente
            $numeros[$i] = substr($num,$i-1,1);
            // Efetua multiplicacao do numero pelo falor
            $parcial[$i] = $numeros[$i] * $fator;
            // Soma dos digitos
            $soma += $parcial[$i];
            if ($fator == $base) {
                // restaura fator de multiplicacao para 2 
                $fator = 1;
            }
            $fator++;
        }

        /* Calculo do modulo 11 */
        if ($r == 0) {
            $soma *= 10;
            $digito = $soma % 11;
            if ($digito == 10) {
                $digito = 0;
            }
            return $digito;
        } elseif ($r == 1){
            $resto = $soma % 11;
            return $resto;
        }
    }
}
?>