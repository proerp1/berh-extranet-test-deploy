# Sistema de Repasse por Volume - Implementação

## Resumo da Implementação

Este documento descreve a implementação completa do sistema de repasse baseado em volume conforme os requisitos especificados.

## Funcionalidades Implementadas

### 1. Novos Campos no Cadastro de Fornecedores

- **Tipo de Repasse**: Expandido para incluir opção "Tabela" (valor 3)
  - Valor (1)
  - Percentual (2) 
  - **Tabela (3)** - NOVO

- **Tipo de Cobrança**: Novo campo obrigatório quando tipo = "Tabela"
  - Por Pedido
  - Por CPF

### 2. Tabela de Faixas de Volume

Nova tabela `supplier_volume_tiers` com:
- `de_qtd`: Quantidade inicial da faixa
- `ate_qtd`: Quantidade final da faixa  
- `percentual_repasse`: Percentual aplicado sobre o valor
- Campos de auditoria completos

### 3. Interface de Usuário

#### Formulário de Fornecedor
- Campos condicionais baseados no tipo de repasse
- Seção para gerenciar faixas de volume
- Validação em tempo real

#### Gestão de Faixas de Volume
- Listagem de faixas com ordenação
- Formulário para adicionar/editar faixas
- Validação de sobreposição de faixas
- Aba condicional no menu de fornecedor

### 4. Lógica de Negócio

#### Classe RepaymentCalculator
Localizada em `app/Lib/RepaymentCalculator.php`, oferece:

- `calculateRepayment($supplierId, $quantity, $baseValue)`: Cálculo principal
- `calculateByOrder($supplierId, $orderItems)`: Consolidação por pedido
- `calculateByCpf($supplierId, $cpf, $orderItems)`: Consolidação por CPF
- `validateSupplierConfiguration($supplierId)`: Validação de configuração
- `generateSimulationReport($supplierId, $quantities)`: Relatório de simulação

### 5. Validações Implementadas

#### Modelo SupplierVolumeTier
- Faixas não podem se sobrepor
- Quantidade final > quantidade inicial
- Percentuais entre 0,01% e 100%
- Validação de integridade referencial

#### Modelo Supplier
- Tipo de cobrança obrigatório para tipo "Tabela"
- Validações customizadas para cada tipo de repasse

## Arquivos Criados/Modificados

### Novos Arquivos
```
/migrations/20250723000001_add_supplier_volume_repayment_features.php
/app/Model/SupplierVolumeTier.php
/app/Lib/RepaymentCalculator.php
/app/View/Suppliers/volume_tiers.ctp
/app/View/Suppliers/add_volume_tier.ctp
```

### Arquivos Modificados
```
/app/Model/Supplier.php
/app/Controller/SuppliersController.php  
/app/View/Suppliers/add.ctp
/app/View/Elements/abas_suppliers.ctp
```

## Como Usar

### 1. Executar a Migration
```bash
php lib/Cake/Console/cake Migrations.migration run -c default
```

### 2. Configurar Fornecedor

1. Acesse **Cadastros > Fornecedores**
2. Edite ou crie um fornecedor
3. Selecione **Tipo de Repasse = "Tabela"**
4. Escolha o **Tipo de Cobrança** (Por Pedido ou Por CPF)
5. Salve o fornecedor

### 3. Configurar Faixas de Volume

1. Na aba **"Faixas de Volume"** do fornecedor
2. Clique em **"Nova Faixa"**
3. Defina os intervalos e percentuais
4. Exemplo:
   - De: 1, Até: 10, % Repasse: 5,00%
   - De: 11, Até: 50, % Repasse: 4,50%
   - De: 51, Até: 99999, % Repasse: 4,00%

### 4. Usar nos Cálculos

```php
// Exemplo de uso
App::uses('RepaymentCalculator', 'Lib');

// Cálculo simples
$result = RepaymentCalculator::calculateRepayment($supplierId, $quantity, $baseValue);

// Cálculo por pedido
$result = RepaymentCalculator::calculateByOrder($supplierId, $orderItems);

// Cálculo por CPF
$result = RepaymentCalculator::calculateByCpf($supplierId, $cpf, $orderItems);

// Validar configuração
$validation = RepaymentCalculator::validateSupplierConfiguration($supplierId);
```

## Comportamento do Sistema

### Processamento de Repasse

1. **Verificar tipo de cobrança** do fornecedor:
   - **Por Pedido**: Consolidar volume por pedido
   - **Por CPF**: Consolidar volume por CPF

2. **Identificar faixa aplicável** baseada no volume consolidado

3. **Aplicar percentual** correspondente da tabela

4. **Calcular valor final** para composição do boleto

### Validações de Integridade

- Faixas não podem ter sobreposição
- Gaps entre faixas geram avisos
- Tipo de cobrança obrigatório para tipo "Tabela"
- Auditoria completa de todas as alterações

## Retrocompatibilidade

✅ **Totalmente compatível** com fornecedores existentes:
- Fornecedores com tipo "Valor" ou "Percentual" continuam funcionando normalmente
- Novos campos são opcionais para tipos existentes
- Interface se adapta automaticamente ao tipo selecionado

## Próximos Passos (Opcional)

Para implementação completa no fluxo de pedidos:

1. **Integrar** RepaymentCalculator no processamento de pedidos
2. **Modificar** geração de boletos para usar novos cálculos
3. **Criar relatórios** de repasse por fornecedor
4. **Implementar** logs de cálculo para auditoria

## Exemplo de Uso Completo

```php
// 1. Validar configuração do fornecedor
$validation = RepaymentCalculator::validateSupplierConfiguration($supplierId);
if (!$validation['is_valid']) {
    throw new Exception('Fornecedor mal configurado: ' . implode(', ', $validation['errors']));
}

// 2. Calcular repasse baseado no tipo de cobrança
$supplier = ClassRegistry::init('Supplier')->findById($supplierId);
$tipoCobranca = $supplier['Supplier']['tipo_cobranca'];

if ($tipoCobranca == 'pedido') {
    $result = RepaymentCalculator::calculateByOrder($supplierId, $orderItems);
} else {
    $result = RepaymentCalculator::calculateByCpf($supplierId, $cpf, $orderItems);
}

// 3. Usar resultado para geração do boleto
$repasseValue = $result['repayment_value'];
$percentualUsado = $result['repayment_percentage'];
```

A implementação está completa e pronta para uso! 🎉