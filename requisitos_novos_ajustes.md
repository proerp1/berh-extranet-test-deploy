https://sig.berh.com.br/suppliers/edit/527

📌 Ajustes no Cadastro das Operadoras – Modalidade de Repasse

1. Novo Campo: Tipo de Repasse

Local: Na aba onde já é definido o tipo de repasse atual.

Campo: Tipo de Repasse

Novos valores possíveis:  Tabela    Valores possíveis atualmente:  Valor e Percentual

2. Modalidade "Tabela" – Cadastro da Tabela de Repasse por Faixa de Volume

2.1. Nova seção: Tabela de Faixas por Volume

Disponível apenas quando o Tipo de Repasse for "Tabela".

Campos da tabela:

De (Qtd)

Até (Qtd)

% Repasse (Percentual aplicado sobre o valor)

2.2. Exemplo de preenchimento:

IMAGE HERE < ASK ME ABOUT IT

3. Novo Campo: Tipo de Cobrança

Local: Ao lado do campo "Tipo de Repasse".

Campo: Tipo de Cobrança

Valores possíveis: Por Pedido ou por CPF 

Validação: Campo obrigatório quando o tipo de repasse for "Tabela". Pode ser opcional para os demais tipos, conforme regras atuais.  Para os demais casos seguirá aplicando o tipo e o valor cadastrado para cálculo na geração do pedido e cobrança cliente.

4. Comportamento no Sistema

Ao processar os valores de repasse da operadora, o sistema deverá:

Verificar o volume consolidado por pedido ou por CPF, conforme o novo campo;

Identificar em qual faixa a quantidade se encaixa (se aplicável);

Aplicar o percentual correspondente da tabela.

fazer os cálculos para compor o boleto de pagamento cliente

5. Requisitos Técnicos Complementares

Permitir múltiplas faixas sem sobreposição (validar que "Até" de uma faixa é menor que "De" da próxima).

Permitir edição e exclusão das faixas.

Registrar auditoria sempre que a tabela for alterada.
