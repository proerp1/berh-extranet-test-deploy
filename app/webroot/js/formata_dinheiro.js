////////// FORMATA DINHEIRO /////////////
function formata_dinheiro(valor, casas, separdor_decimal, separador_milhar) {

  var valor_total = parseInt(valor * (Math.pow(10, casas)));
  var inteiros = parseInt(parseInt(valor * (Math.pow(10, casas))) / parseFloat(Math.pow(10, casas)));
  var centavos = parseInt(parseInt(valor * (Math.pow(10, casas))) % parseFloat(Math.pow(10, casas)));
  var teste = "Joao";

  if (centavos % 10 == 0 && centavos + "".length < 2) {
    centavos = centavos + "0";
  } else if (centavos < 10) {
    centavos = "0" + centavos;
  }

  var milhares = parseInt(inteiros / 1000);
  inteiros = inteiros % 1000;

  var retorno = "";

  if (milhares > 0) {
    retorno = milhares + "" + separador_milhar + "" + retorno
    if (inteiros == 0) {
      inteiros = "000";
    } else if (inteiros < 10) {
      inteiros = "00" + inteiros;
    } else if (inteiros < 100) {
      inteiros = "0" + inteiros;
    }
  }
  retorno += inteiros + "" + separdor_decimal + "" + centavos;


  return retorno;
}

function formatarMoeda() {
  var elemento = document.getElementById('valor');
  var valor = elemento.value;

  valor = valor + '';
  valor = parseInt(valor.replace(/[\D]+/g,''));
  valor = valor + '';
  valor = valor.replace(/([0-9]{2})$/g, ",$1");

  if (valor.length > 6) {
    valor = valor.replace(/([0-9]{3}),([0-9]{2}$)/g, ".$1,$2");
  }

  elemento.value = valor;
}

function replaceAll(string, token, newtoken) {
  while (string.indexOf(token) != -1) {
    string = string.replace(token, newtoken);
  }
  return string;
}

function retorna_dinheiro(vlr) {
  vlr = vlr + "";
  tmp = parseFloat(vlr).toFixed(2);
  var novo_val = replaceAll(tmp + "", ".", ',');
  return novo_val;
}

function retorna_dinheiro_us(vlr) {
  vlr = vlr + "";
  var val_sem_ponto = replaceAll(vlr, ".", '');

  var novo_val = replaceAll(val_sem_ponto, ",", ".");
  return novo_val;
}