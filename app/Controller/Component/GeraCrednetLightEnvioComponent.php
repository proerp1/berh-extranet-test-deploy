<?php
class GeraCrednetLightEnvioComponent extends Component
{
    public $components = ['Bootstrap', 'Session'];

    public function gera($dadosLight)
    {
        $str = '';

        $str .= 'B49C';
        $str .= $this->geraEspacos(6);
        $str .= $this->zerosEsq($dadosLight['doc'], 15);
        $str .= $dadosLight['tipo'];
        $str .= $this->completaComEspaco('C', 6);
        $str .= 'FI';
        $str .= $this->geraEspacos(7);
        $str .= $this->geraEspacos(12);
        $str .= 'N';
        $str .= '99';
        $str .= 'S';
        $str .= 'INI';
        $str .= 'A';
        $str .= 'S';
        $str .= $this->geraEspacos(18);
        $str .= $this->geraEspacos(10);
        $str .= $this->geraEspacos(1);
        $str .= $this->geraEspacos(1);
        $str .= 'D';
        $str .= 'N';
        $str .= $this->geraEspacos(1);
        $str .= $this->geraEspacos(9);
        $str .= $this->geraEspacos(2);
        $str .= 'N';
        $str .= $this->geraEspacos(2);
        $str .= $this->geraEspacos(1);
        $str .= $this->geraEspacos(1);
        $str .= $this->geraEspacos(1);
        $str .= $this->geraEspacos(1);
        $str .= $this->geraEspacos(1);
        $str .= 'S';
        $str .= $this->geraEspacos(10);
        $str .= $this->geraEspacos(1);
        $str .= $this->geraEspacos(4);
        $str .= $this->geraEspacos(1);
        $str .= $dadosLight['usuario'];
        $str .= $this->geraEspacos(1);
        $str .= $this->geraEspacos(1);
        $str .= $this->geraEspacos(1);
        $str .= $this->geraEspacos(1);
        $str .= $this->geraEspacos(1);
        $str .= 'N';
        $str .= $this->geraEspacos(2);
        $str .= $this->geraEspacos(4);
        $str .= $dadosLight['cnpj_empresa'];
        $str .= $this->geraEspacos(1);
        $str .= $this->geraEspacos(9);
        $str .= $this->geraEspacos(15);
        $str .= $this->geraEspacos(400-strlen($str));

        $str .= 'P002';
        $str .= 'RE02';
        $str .= $this->geraEspacos(21);
        if ($dadosLight['tipo'] == 'F') {
            $str .= $this->completaComEspaco($dadosLight['features']['score'], 8);
        } else {
            $str .= $this->completaComEspaco($dadosLight['features']['class'], 8);
        }
        $str .= $this->geraEspacos(17);
        $str .= $this->geraEspacos(61);

        $str .= 'N001';
        $str .= '00';
        $str .= 'PF';
        $str .= ($dadosLight['tipo'] == 'F' ? 'X2LF' : 'X2LJ');
        $str .= 'N';
        $str .= '0';
        $str .= 'N';
        $str .= 'N';
        $str .= $this->geraEspacos(1);
        $str .= $this->geraEspacos(1);
        $str .= $this->geraEspacos(6);
        $str .= $this->geraEspacos(12);
        $str .= $this->geraEspacos(1);
        $str .= $this->geraEspacos(13);
        $str .= 'N';
        $str .= $this->geraEspacos(64);

        $str .= 'N003';
        $str .= '00';
        $str .= $this->zerosEsq($dadosLight['ddd'], 4);
        $str .= $this->zerosEsq(substr($dadosLight['tel'], 0, 8), 8);
        $str .= $this->zerosEsq($dadosLight['cep'], 9);
        $str .= $this->completaComEspaco($dadosLight['estado'], 2);
        $str .= $this->geraEspacos(4);
        $str .= $this->geraEspacos(82);
        $str .= 'T999';

        return $str;
    }

    public function zerosEsq($campo, $tamanho)
    {
        $cp = str_pad($campo, $tamanho, 0, STR_PAD_LEFT);
        return $cp;
    }

    public function geraEspacos($qtde)
    {
        $sp = str_pad('', $qtde);
        return $sp;
    }

    public function completaComEspaco($campo, $tamanho)
    {
        $campo = substr($campo, 0, $tamanho);
        
        $cp = str_pad($campo, $tamanho);

        return $cp;
    }
}
