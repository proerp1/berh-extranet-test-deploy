<?php
/**
 * Real Order 3219 Validation Test
 * Generated from actual database data on 2025-08-30
 * 
 * This test validates our centralized RepaymentCalculator using the exact
 * supplier configurations and order items from your database
 */

// Mock dependencies
class ClassRegistry 
{
    private static $mocks = [];
    
    public static function init($modelName) 
    {
        if (isset(self::$mocks[$modelName])) {
            return self::$mocks[$modelName];
        }
        throw new Exception("Mock not found for model: {$modelName}");
    }
    
    public static function setMock($modelName, $mock) 
    {
        self::$mocks[$modelName] = $mock;
    }
}

class MockSupplier 
{
    private $suppliers = [];
    
    public function addSupplier($id, $transferFeeType, $tipoCobranca, $name = '') 
    {
        $this->suppliers[$id] = [
            'Supplier' => [
                'id' => $id,
                'transfer_fee_type' => $transferFeeType,
                'tipo_cobranca' => $tipoCobranca,
                'company_name' => $name
            ]
        ];
    }
    
    public function findById($id) 
    {
        return isset($this->suppliers[$id]) ? $this->suppliers[$id] : null;
    }
}

class MockSupplierVolumeTier 
{
    private $tiers = [];
    
    public function addTier($supplierId, $deQtd, $ateQtd, $valorFixo, $percentual) 
    {
        $this->tiers[] = [
            'SupplierVolumeTier' => [
                'supplier_id' => $supplierId,
                'de_qtd' => $deQtd,
                'ate_qtd' => $ateQtd,
                'valor_fixo' => $valorFixo,
                'percentual_repasse' => $percentual,
                'percentual_repasse_nao_formatado' => $percentual
            ]
        ];
    }
    
    public function findTierForQuantity($supplierId, $quantity) 
    {
        foreach ($this->tiers as $tier) {
            $tierData = $tier['SupplierVolumeTier'];
            if ($tierData['supplier_id'] == $supplierId && 
                $tierData['de_qtd'] <= $quantity && 
                $tierData['ate_qtd'] >= $quantity) {
                return $tier;
            }
        }
        return null;
    }
}

class MockOrderItem 
{
    private $savedData = [];
    
    public function save($data, $options = []) 
    {
        // $options parameter kept for compatibility but not used in mock
        $this->savedData[] = $data;
        return true;
    }
    
    public function getSavedData() 
    {
        return $this->savedData;
    }
    
    public function clearSavedData() 
    {
        $this->savedData = [];
    }
}

class MockCakeLog 
{
    public static $logs = [];
    
    public static function write($level, $message) 
    {
        self::$logs[] = "[{$level}] {$message}";
    }
    
    public static function getLogs() 
    {
        return self::$logs;
    }
    
    public static function clearLogs() 
    {
        self::$logs = [];
    }
}

if (!class_exists('CakeLog')) {
    class CakeLog extends MockCakeLog {}
}

// Include RepaymentCalculator
require_once 'app/Lib/RepaymentCalculator.php';

// Test Controller
class TestOrdersController 
{
    public $OrderItem;
    
    public function __construct() 
    {
        $this->OrderItem = new MockOrderItem();
    }
    
    public function parseFormattedNumber($formattedValue)
    {
        if ($formattedValue === null || $formattedValue === '') {
            return 0.0;
        }
        
        if (is_numeric($formattedValue)) {
            return floatval($formattedValue);
        }
        
        $value = trim(strval($formattedValue));
        
        if ($value === '') {
            return 0.0;
        }
        
        // Handle Brazilian number format
        if (strpos($value, '.') !== false && strpos($value, ',') !== false) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } elseif (strpos($value, ',') !== false) {
            $value = str_replace(',', '.', $value);
        } elseif (strpos($value, '.') !== false) {
            $dotPos = strrpos($value, '.');
            $afterDot = substr($value, $dotPos + 1);
            if (strlen($afterDot) == 3 && ctype_digit($afterDot)) {
                $value = str_replace('.', '', $value);
            }
        }
        
        return floatval($value);
    }
    
    public function calculateTransferFeesForSupplier($orderId, $supplierId, $supplier, $items)
    {
        // $orderId parameter kept for compatibility but not used in test
        // Calculate total subtotal for tier determination
        $totalSubtotal = 0;
        foreach ($items as $item) {
            $itemSubtotal = isset($item['OrderItem']['subtotal_not_formated']) 
                ? $item['OrderItem']['subtotal_not_formated'] 
                : $this->parseFormattedNumber($item['OrderItem']['subtotal']);
            $totalSubtotal += $itemSubtotal;
        }
        
        CakeLog::write('info', "Supplier {$supplierId}: Processing " . count($items) . " items, total subtotal: R$ " . number_format($totalSubtotal, 2, ',', '.'));
        
        try {
            $calculationResult = RepaymentCalculator::calculateRepayment($supplierId, $totalSubtotal, $totalSubtotal);
            
            $transferFeeType = $supplier['transfer_fee_type'];
            $tipoCobranca = isset($supplier['tipo_cobranca']) ? $supplier['tipo_cobranca'] : 'pedido';
            
            CakeLog::write('info', "Supplier {$supplierId}: Type={$transferFeeType}, Cobranca={$tipoCobranca}, Method={$calculationResult['calculation_method']}");
            
            if ($transferFeeType == 1) { // Fixed Value
                if ($tipoCobranca == 'cpf') {
                    $this->applyFixedValueByCpf($calculationResult, $items, $supplierId);
                } else {
                    $this->applyFixedValueByOrder($calculationResult, $items, $supplierId);
                }
            } elseif ($transferFeeType == 2) { // Percentage
                $this->applyPercentageToEachItem($calculationResult, $items, $supplierId);
            }
            
        } catch (Exception $e) {
            CakeLog::write('error', "Supplier {$supplierId}: Error - " . $e->getMessage());
        }
    }
    
    private function applyFixedValueByCpf($calculationResult, $items, $supplierId)
    {
        $fixedValue = $calculationResult['repayment_value'];
        
        if ($fixedValue <= 0) {
            CakeLog::write('debug', "Supplier {$supplierId}: No fixed value from tier, skipping");
            return;
        }
        
        CakeLog::write('info', "Supplier {$supplierId}: Applying R$ {$fixedValue} to each item (Fixed by CPF)");
        
        foreach ($items as $item) {
            $this->updateOrderItemWithTransferFee($item, $fixedValue, [
                'type' => 'fixed_by_cpf',
                'fixed_value' => $fixedValue,
                'supplier_id' => $supplierId,
                'tier_used' => $calculationResult['tier_used']
            ]);
        }
    }
    
    private function applyFixedValueByOrder($calculationResult, $items, $supplierId)
    {
        $totalFixedValue = $calculationResult['repayment_value'];
        
        if ($totalFixedValue <= 0) {
            CakeLog::write('debug', "Supplier {$supplierId}: No fixed value from tier, skipping");
            return;
        }
        
        $itemCount = count($items);
        $feePerItem = $totalFixedValue / $itemCount;
        
        CakeLog::write('info', "Supplier {$supplierId}: Dividing R$ {$totalFixedValue} among {$itemCount} items = R$ " . number_format($feePerItem, 2, ',', '.') . " each");
        
        foreach ($items as $item) {
            $this->updateOrderItemWithTransferFee($item, $feePerItem, [
                'type' => 'volume_tier_fixed',
                'total_fixed_value' => $totalFixedValue,
                'fee_per_item' => $feePerItem,
                'supplier_id' => $supplierId,
                'tier_used' => $calculationResult['tier_used']
            ]);
        }
    }
    
    private function applyPercentageToEachItem($calculationResult, $items, $supplierId)
    {
        $percentage = $calculationResult['repayment_percentage'];
        
        if ($percentage <= 0) {
            CakeLog::write('debug', "Supplier {$supplierId}: No percentage from tier, skipping");
            return;
        }
        
        CakeLog::write('info', "Supplier {$supplierId}: Applying {$percentage}% to each item individually");
        
        foreach ($items as $item) {
            $itemSubtotal = isset($item['OrderItem']['subtotal_not_formated']) 
                ? $item['OrderItem']['subtotal_not_formated'] 
                : $this->parseFormattedNumber($item['OrderItem']['subtotal']);
                
            $itemTransferFee = ($itemSubtotal * $percentage) / 100;
            
            CakeLog::write('debug', "Supplier {$supplierId}: Item {$item['OrderItem']['id']} - R$ {$itemSubtotal} * {$percentage}% = R$ " . number_format($itemTransferFee, 2, ',', '.'));
            
            $this->updateOrderItemWithTransferFee($item, $itemTransferFee, [
                'type' => 'percentage_individual',
                'percentage' => $percentage,
                'item_subtotal' => $itemSubtotal,
                'calculated_fee' => $itemTransferFee,
                'supplier_id' => $supplierId,
                'tier_used' => $calculationResult['tier_used']
            ]);
        }
    }
    
    private function updateOrderItemWithTransferFee($item, $transferFee, $calculationDetails)
    {
        $itemCommissionFee = isset($item['OrderItem']['commission_fee_not_formated']) 
            ? $item['OrderItem']['commission_fee_not_formated'] 
            : (isset($item['OrderItem']['commission_fee']) ? $item['OrderItem']['commission_fee'] : 0);
        $itemCommissionFee = $this->parseFormattedNumber($itemCommissionFee);
        
        $itemSubtotal = isset($item['OrderItem']['subtotal_not_formated']) 
            ? $item['OrderItem']['subtotal_not_formated'] 
            : (isset($item['OrderItem']['subtotal']) ? $item['OrderItem']['subtotal'] : 0);
        $itemSubtotal = $this->parseFormattedNumber($itemSubtotal);
        
        $transferFee = $this->parseFormattedNumber($transferFee);
        
        $newTotal = $itemSubtotal + $transferFee + $itemCommissionFee;
        
        $updateData = [
            'OrderItem' => [
                'id' => $item['OrderItem']['id'],
                'transfer_fee' => $transferFee,
                'total' => $newTotal,
                'calculation_details_log' => json_encode($calculationDetails)
            ]
        ];
        
        $this->OrderItem->save($updateData);
    }
}

class RealOrder3219Test 
{
    private $controller;
    private $supplierMock;
    private $tierMock;
    
    public function __construct() 
    {
        $this->controller = new TestOrdersController();
        $this->supplierMock = new MockSupplier();
        $this->tierMock = new MockSupplierVolumeTier();
        
        ClassRegistry::setMock('Supplier', $this->supplierMock);
        ClassRegistry::setMock('SupplierVolumeTier', $this->tierMock);
        
        $this->setupRealData();
    }
    
    private function setupRealData() 
    {
        // REAL DATA FROM ORDER 3219 DATABASE
        
        // Supplier 3: Vamu Mobilidade - Fixed Value + CPF
        $this->supplierMock->addSupplier(3, 1, 'cpf', 'Vamu Mobilidade');
        $this->tierMock->addTier(3, 1, 100, 16.20, 0.00);      // R$16.20 for 1-100
        $this->tierMock->addTier(3, 101, 999999, 13.30, 0.00); // R$13.30 for 101-999999
        
        // Supplier 337: Riocard - Fixed Value + Order  
        $this->supplierMock->addSupplier(337, 1, 'pedido', 'Riocard');
        $this->tierMock->addTier(337, 1, 10, 13.50, 10.00);    // R$13.50 for 1-10
        $this->tierMock->addTier(337, 11, 50, 12.50, 12.50);   // R$12.50 for 11-50
        $this->tierMock->addTier(337, 51, 100, 11.50, 15.00);  // R$11.50 for 51-100
        
        // Supplier 755: Sptrans - Percentage + Order
        $this->supplierMock->addSupplier(755, 2, 'pedido', 'Sptrans');
        $this->tierMock->addTier(755, 1, 999999, 0.00, 2.50);  // 2.5% for 1-999999
    }
    
    public function runTests() 
    {
        echo "=== Real Order 3219 Validation Test ===\n";
        echo "Generated from actual database data\n\n";
        
        MockCakeLog::clearLogs();
        
        $this->testVamuMobilidade();
        $this->testRiocard();
        $this->testSptrans();
        
        $this->displayResults();
        $this->showCalculationLogs();
    }
    
    private function testVamuMobilidade() 
    {
        echo "🚌 Testing Vamu Mobilidade (Supplier 3) - Fixed by CPF\n";
        echo "Configuration: Fixed Value + CPF (each item gets full amount)\n";
        
        $supplier = ['transfer_fee_type' => 1, 'tipo_cobranca' => 'cpf'];
        
        // Real order item from database
        $items = [
            ['OrderItem' => [
                'id' => 489678, 
                'customer_user_id' => 40311, 
                'subtotal_not_formated' => 98.00, 
                'commission_fee_not_formated' => 0.98
            ]]
        ];
        
        echo "Current DB values: Transfer Fee = R$ 16,20 | Total = R$ 115,18\n";
        echo "Item subtotal: R$ 98,00 (falls in tier 1-100)\n";
        echo "Expected: Tier 1-100 → R$ 16,20 (Fixed by CPF)\n\n";
        
        $this->controller->OrderItem->clearSavedData();
        $this->controller->calculateTransferFeesForSupplier(3219, 3, $supplier, $items);
        $savedData = $this->controller->OrderItem->getSavedData();
        
        if (count($savedData) > 0) {
            $calculated = $savedData[0]['OrderItem'];
            echo "✅ Our calculation:\n";
            echo "   Transfer Fee: R$ " . number_format($calculated['transfer_fee'], 2, ',', '.') . "\n";
            echo "   Total: R$ " . number_format($calculated['total'], 2, ',', '.') . "\n";
            
            // Compare with database values
            $matches = (abs($calculated['transfer_fee'] - 16.20) < 0.01);
            echo "   Match with DB: " . ($matches ? "✅ YES" : "❌ NO") . "\n";
        } else {
            echo "❌ No calculation performed\n";
        }
        echo "\n" . str_repeat("-", 60) . "\n\n";
    }
    
    private function testRiocard() 
    {
        echo "🎫 Testing Riocard (Supplier 337) - Fixed by Order\n";
        echo "Configuration: Fixed Value + Order (amount divided equally)\n";
        
        $supplier = ['transfer_fee_type' => 1, 'tipo_cobranca' => 'pedido'];
        
        // Real order items from database
        $items = [
            ['OrderItem' => [
                'id' => 489676, 
                'customer_user_id' => 40314, 
                'subtotal_not_formated' => 94.00, 
                'commission_fee_not_formated' => 0.94
            ]],
            ['OrderItem' => [
                'id' => 489677, 
                'customer_user_id' => 40314, 
                'subtotal_not_formated' => 126.40, 
                'commission_fee_not_formated' => 1.26
            ]]
        ];
        
        echo "Current DB values: Both items have R$ 13,50 transfer fee\n";
        echo "Total subtotal: R$ 220,40 (94+126.40) - falls in tier 51-100\n";
        echo "Expected: Tier 51-100 → R$ 11,50 total ÷ 2 items = R$ 5,75 each\n\n";
        
        $this->controller->OrderItem->clearSavedData();
        $this->controller->calculateTransferFeesForSupplier(3219, 337, $supplier, $items);
        $savedData = $this->controller->OrderItem->getSavedData();
        
        if (count($savedData) == 2) {
            echo "✅ Our calculation:\n";
            foreach ($savedData as $i => $data) {
                $calculated = $data['OrderItem'];
                echo "   Item " . ($i + 1) . ": Transfer Fee = R$ " . number_format($calculated['transfer_fee'], 2, ',', '.') . 
                     " | Total = R$ " . number_format($calculated['total'], 2, ',', '.') . "\n";
            }
            
            $expectedFeePerItem = 5.75; // R$ 11.50 / 2 items
            $actualFee = $savedData[0]['OrderItem']['transfer_fee'];
            $matches = (abs($actualFee - $expectedFeePerItem) < 0.01);
            echo "   Expected R$ 5,75 each, got R$ " . number_format($actualFee, 2, ',', '.') . 
                 " - Match: " . ($matches ? "✅ YES" : "❌ NO") . "\n";
        } else {
            echo "❌ Expected 2 items, got " . count($savedData) . "\n";
        }
        echo "\n" . str_repeat("-", 60) . "\n\n";
    }
    
    private function testSptrans() 
    {
        echo "🚇 Testing Sptrans (Supplier 755) - Percentage Individual\n";
        echo "Configuration: Percentage + Order (applied to each item)\n";
        
        $supplier = ['transfer_fee_type' => 2, 'tipo_cobranca' => 'pedido'];
        
        // Real order items from database
        $items = [
            ['OrderItem' => [
                'id' => 489679, 
                'customer_user_id' => 40311, 
                'subtotal_not_formated' => 153.72, 
                'commission_fee_not_formated' => 1.54
            ]],
            ['OrderItem' => [
                'id' => 489680, 
                'customer_user_id' => 40007, 
                'subtotal_not_formated' => 309.12, 
                'commission_fee_not_formated' => 3.09
            ]]
        ];
        
        echo "Current DB values: R$ 3,84 and R$ 7,73 transfer fees\n";
        echo "Total subtotal: R$ 462,84 (153.72+309.12) - falls in tier 1-999999\n";
        echo "Expected: 2.5% applied individually\n";
        echo "  Item 1: R$ 153,72 × 2,5% = R$ 3,84\n";
        echo "  Item 2: R$ 309,12 × 2,5% = R$ 7,73\n\n";
        
        $this->controller->OrderItem->clearSavedData();
        $this->controller->calculateTransferFeesForSupplier(3219, 755, $supplier, $items);
        $savedData = $this->controller->OrderItem->getSavedData();
        
        if (count($savedData) == 2) {
            echo "✅ Our calculation:\n";
            $expectedFees = [3.84, 7.73];
            
            foreach ($savedData as $i => $data) {
                $calculated = $data['OrderItem'];
                $expected = $expectedFees[$i];
                echo "   Item " . ($i + 1) . ": Transfer Fee = R$ " . number_format($calculated['transfer_fee'], 2, ',', '.') . 
                     " (expected R$ " . number_format($expected, 2, ',', '.') . ") | " .
                     "Total = R$ " . number_format($calculated['total'], 2, ',', '.') . "\n";
                
                $matches = (abs($calculated['transfer_fee'] - $expected) < 0.01);
                echo "     Match: " . ($matches ? "✅ YES" : "❌ NO") . "\n";
            }
        } else {
            echo "❌ Expected 2 items, got " . count($savedData) . "\n";
        }
        echo "\n" . str_repeat("-", 60) . "\n\n";
    }
    
    private function displayResults() 
    {
        echo str_repeat("=", 80) . "\n";
        echo "VALIDATION SUMMARY\n";
        echo str_repeat("=", 80) . "\n";
        
        $allSavedData = $this->controller->OrderItem->getSavedData();
        
        if (empty($allSavedData)) {
            echo "❌ No calculations were performed!\n";
            return;
        }
        
        echo "✅ Processed " . count($allSavedData) . " order items\n\n";
        
        echo "COMPARISON WITH DATABASE VALUES:\n";
        echo "Supplier 3 (Vamu): Expected R$ 16,20 vs Our calculation\n";
        echo "Supplier 337 (Riocard): Expected R$ 5,75 each vs Our calculation  \n";
        echo "Supplier 755 (Sptrans): Expected R$ 3,84 & R$ 7,73 vs Our calculation\n\n";
        
        echo "KEY INSIGHTS:\n";
        echo "- Fixed by CPF: Each item gets the full tier amount\n";
        echo "- Fixed by Order: Tier amount divided equally among items\n";
        echo "- Percentage: Applied individually to each item's subtotal\n";
        echo "- All calculations use total subtotal to determine tier\n\n";
    }
    
    private function showCalculationLogs() 
    {
        echo str_repeat("=", 80) . "\n";
        echo "DETAILED CALCULATION LOGS\n";
        echo str_repeat("=", 80) . "\n";
        
        $logs = MockCakeLog::getLogs();
        
        foreach ($logs as $log) {
            echo $log . "\n";
        }
        echo "\n";
    }
}

// Run the real validation test
$test = new RealOrder3219Test();
$test->runTests();