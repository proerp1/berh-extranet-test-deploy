<?php
  $nome_fantasia = '#NomeFantasia#';
  $cnpj = '#NumeroCNPJ#';
  $codigo_associado = '#CodigoAssociado#';
  $link = '#LinkGerarBoleto#';

  $html = '
  <table width="100%" bgcolor="#686868">
      <tr>
      <td align="center">
      <table width="80%" align="center" bgcolor="#FFF" style="border-radius: 6px; margin-top: 20px;">
            <tr>
              <td align="center"><img style="margin: 30px" width="200" src="https://berh.com.br/extranet/img/berh_logo.png" alt=""></td>
            </tr>

            <!-- body -->
            <tr align="center">
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
                      Para sua comodidade, e também com a finalidade de contornar possíveis extravios, <br />
                      a BeRH - Distribuidor Autorizado Serasa disponibiliza através de nosso site, o seu boleto de cobrança.<br /><br />
                      Para acessar seu boleto clique no botão abaixo:
                    </td>
                  </tr>
                  <!-- end content -->
                  
                </table>
              </td>
            </tr>
            <!-- end body -->
            

            <!-- button -->
            <tr>
              <td align="center">
                <table class="textbutton" align="center" bgcolor="#5cb85c" border="0" cellspacing="0" cellpadding="0" style="margin:20px auto; border-radius: 6px;">
                  <tr style="border-radius: 6px;">
                    <td height="55" align="center" style="font-family: \'Open Sans\', Arial, sans-serif; font-size:16px; color:#FFFFFF;font-weight: bold;padding-left: 25px;padding-right: 25px; background-color: #5cb85c; border-radius: 6px;"><a href="'.$link.'" style="color:#FFFFFF;text-decoration: none;">GERAR BOLETO</a></td>
                  </tr>
                </table>
              </td>
            </tr>
            <!-- end button -->

      </table>

      <!-- footer -->
        <footer>
          <div style="font-family: \'Open Sans\', Arial, sans-serif; text-align: center; font-size:16px; color:#FFFFFF; margin: 20px auto;">&copy; '.date('Y').' BeRH</div>
        </footer>
        <!-- end footer -->
        </td>
      </tr>
  </table>';

  echo $html;
?>