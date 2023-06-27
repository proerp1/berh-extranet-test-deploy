function MascaraMoeda(objTextBox, SeparadorMilesimo, SeparadorDecimal, e){  
    var sep = 0;  
    var key = '';  
    var i = j = 0;  
    var len = len2 = 0;  
    var strCheck = '0123456789';  
    var aux = aux2 = '';  
    var whichCode = (window.Event) ? e.which : e.keyCode;  
    if (whichCode == 13) return true;  
    key = String.fromCharCode(whichCode); // Valor para o código da Chave  
    if (strCheck.indexOf(key) == -1) return false; // Chave inválida  
    len = objTextBox.value.length;  
    for(i = 0; i < len; i++)  
        if ((objTextBox.value.charAt(i) != '0') && (objTextBox.value.charAt(i) != SeparadorDecimal)) break;  
    aux = '';  
    for(; i < len; i++)  
        if (strCheck.indexOf(objTextBox.value.charAt(i))!=-1) aux += objTextBox.value.charAt(i);  
    aux += key;  
    len = aux.length;  
    if (len == 0) objTextBox.value = '';  
    if (len == 1) objTextBox.value = '0'+ SeparadorDecimal + '0' + aux;  
    if (len == 2) objTextBox.value = '0'+ SeparadorDecimal + aux;  
    if (len > 2) {  
        aux2 = '';  
        for (j = 0, i = len - 3; i >= 0; i--) {  
            if (j == 3) {  
                aux2 += SeparadorMilesimo;  
                j = 0;  
            }  
            aux2 += aux.charAt(i);  
            j++;  
        }  
        objTextBox.value = '';  
        len2 = aux2.length;  
        for (i = len2 - 1; i >= 0; i--)  
        objTextBox.value += aux2.charAt(i);  
        objTextBox.value += SeparadorDecimal + aux.substr(len - 2, len);  
    }  
    return false;  
}


function replaceAll(string, token, newtoken) {
    while (string.indexOf(token) != -1) {
        string = string.replace(token, newtoken);
    }
    return string;
}

function retorna_dinheiro_us(vlr){
    vlr = vlr+"";
    var val_sem_ponto = replaceAll(vlr, ".", '');
            
    var novo_val = replaceAll(val_sem_ponto, ",", ".");
    return novo_val;
    
}

function retorna_dinheiro(vlr){
    vlr = vlr+"";
    tmp = parseFloat(vlr).toFixed(2);
    var novo_val = replaceAll(tmp+"", ".", ',');
    return novo_val;
}

function retorna_dinheiro_br(vlr){
    vlr = vlr+"";
    tmp = parseFloat(vlr).toFixed(2);

    var novo_val = replaceAll(tmp+"", ".", ',');


    var pedacos = novo_val.split(",");
    str = pedacos[0];
    str_final = '';
    for (var i = 0, len = str.length; i < len; i++) {
      if(i % 3 == 0){
        str_final += str[i]+".";
      } else {
        str_final += str[i];
      }
    }
    
    str_final = str_final.slice(0, -1);

    novo_val = str_final+","+pedacos[1];

    return novo_val;
}

function formata_dinheiro(valor, casas, separdor_decimal, separador_milhar){

  var valor_total = parseInt(valor * (Math.pow(10,casas) ) );
  var inteiros =  parseInt(parseInt(valor * (Math.pow(10,casas))) / parseFloat(Math.pow(10,casas)));
  var centavos = parseInt(parseInt(valor * (Math.pow(10,casas))) % parseFloat(Math.pow(10,casas)));
  var teste = "Joao";


    if(centavos%10 == 0 && centavos+"".length<2 ){
     centavos = centavos+"0";
    }else if(centavos<10){
     centavos = "0"+centavos;
    }

    var milhares = parseInt(inteiros/1000);
    inteiros = inteiros % 1000;

    var retorno = "";

    if(milhares>0){
     retorno = milhares+""+separador_milhar+""+retorno
     if(inteiros == 0){
      inteiros = "000";
     } else if(inteiros < 10){
      inteiros = "00"+inteiros;
     } else if(inteiros < 100){
      inteiros = "0"+inteiros;
     }
    }
     retorno += inteiros+""+separdor_decimal+""+centavos;


    return retorno;

}