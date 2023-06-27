<?php
  $html = '<!-- body -->
          <table width="100%">
            <tr>
              <td align="center" bgcolor="#f3f3f3">
                <table align="center" class="table-inner" width="500" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td height="25"></td>
                  </tr>
                  <!-- title -->
                  <tr>
                    <td align="center" style="font-family: \'Open Sans\', Arial, sans-serif; font-size:36px; color:#3b3b3b; font-weight: bold;">Prezado Cliente</td>
                  </tr>
                  <!-- end title -->
                  <tr>
                    <td align="center">
                      <table width="25" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td height="15" style="border-bottom:2px solid #5cb85c;"></td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr>
                    <td height="20"></td>
                  </tr>
                  <!-- content -->
                  <tr>
                    <td align="center" style="font-family: \'Open Sans\', Arial, sans-serif; font-size:13px; color:#7f8c8d; line-height:30px;">
                      <p><strong>'.$nome_fantasia.'</strong> - CNPJ: <strong>'.$cnpj.'</strong> - CONTRATO Nº <strong>'.$codigo_associado.'</strong></p>
                      Solicitamos a comunicação a respeito do não pagamento dos valores em aberto. Como até o presente momento não recebemos nenhuma resposta em nossas tentativas de contato, reafirmamos a urgência na regularização da sua conta. <br>
                      Para sua comodidade segue o boleto abaixo para liquidação do(s) débito(s) em aberto. <br>
                      A permanência do débito fará com que sejamos obrigados a reaver seu cadastro e recorrer às consequências cabíveis.<br><br>
                      Para acessar seu boleto clique no botão abaixo: <br>
                      Dúvidas 4020-7705 ou 7734293800
                    </td>
                  </tr>
                  <!-- end content -->
                  <tr>
                    <td height="25"></td>
                  </tr>
                </table>
              </td>
            </tr>
            <!-- end body -->
            <tr>
              <td height="40"></td>
            </tr>
            <!-- button -->
            <tr>
              <td align="center">
                <table class="textbutton" align="center" bgcolor="#5cb85c" border="0" cellspacing="0" cellpadding="0" style=" border-radius:4px; box-shadow: 0px 2px 0px #dedfdf;">
                  <tr>
                    <td height="55" align="center" style="font-family: \'Open Sans\', Arial, sans-serif; font-size:16px; color:#FFFFFF;font-weight: bold;padding-left: 25px;padding-right: 25px;"><a href="'.$link.'" style="color:#FFFFFF;text-decoration: none;">GERAR BOLETO</a></td>
                  </tr>
                </table>
              </td>
            </tr>
            <!-- end button -->
          </table>';

  echo $html;
?>