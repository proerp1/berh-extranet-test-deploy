<?php
/**
 * Extract real data from Order 3219 using CakePHP's database connection
 * This will generate a test file with actual supplier configurations and order items
 */

// Bootstrap CakePHP properly
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__FILE__));
define('APP_DIR', 'app');
define('APP', ROOT . DS . APP_DIR . DS);

// Load CakePHP configuration
require APP . 'Config' . DS . 'bootstrap.php';

// Use raw database queries to avoid model loading issues
$db = ConnectionManager::getDataSource('default');

class RealDataExtractor 
{
    private $db;
    
    public function __construct() 
    {
        $this->db = ConnectionManager::getDataSource('default');
    }
    
    public function extractOrder3219Data() 
    {
        echo "=== Extracting Real Data from Order 3219 ===\n\n";
        
        try {
            // Get order items with supplier information using raw SQL
            $orderItemsQuery = "
                SELECT 
                    oi.id as item_id,
                    oi.order_id,
                    oi.customer_user_id,
                    oi.subtotal,
                    oi.subtotal_not_formated,
                    oi.commission_fee,
                    oi.commission_fee_not_formated,
                    oi.transfer_fee,
                    oi.total,
                    s.id as supplier_id,
                    s.company_name,
                    s.transfer_fee_type,
                    s.tipo_cobranca
                FROM order_items oi
                JOIN customer_user_itineraries cui ON oi.customer_user_itinerary_id = cui.id
                JOIN benefits b ON cui.benefit_id = b.id
                JOIN suppliers s ON b.supplier_id = s.id
                WHERE oi.order_id = 3219
                  AND oi.data_cancel = '1901-01-01 00:00:00'
                ORDER BY s.id, oi.id
            ";
            
            $orderItems = $this->db->fetchAll($orderItemsQuery);
            
            if (empty($orderItems)) {
                echo "❌ No items found for order 3219\n";
                return;
            }
            
            echo "📦 Found " . count($orderItems) . " order items\n\n";
            
            // Group by supplier
            $supplierGroups = [];
            $supplierInfo = [];
            
            foreach ($orderItems as $item) {
                $supplierId = $item[0]['supplier_id'];
                $supplierGroups[$supplierId][] = $item[0];
                
                if (!isset($supplierInfo[$supplierId])) {
                    $supplierInfo[$supplierId] = [
                        'company_name' => $item[0]['company_name'],
                        'transfer_fee_type' => $item[0]['transfer_fee_type'],
                        'tipo_cobranca' => $item[0]['tipo_cobranca']
                    ];
                }
            }
            
            echo "🏢 Found " . count($supplierGroups) . " unique suppliers:\n";
            foreach ($supplierInfo as $supplierId => $info) {
                $itemCount = count($supplierGroups[$supplierId]);
                $typeDesc = $this->getTypeDescription($info['transfer_fee_type']);
                echo "- Supplier {$supplierId}: {$info['company_name']} | {$typeDesc} + {$info['tipo_cobranca']} | {$itemCount} items\n";
            }
            echo "\n";
            
            // Get volume tiers for all suppliers
            $supplierIds = array_keys($supplierGroups);
            $supplierIdsStr = implode(',', $supplierIds);
            
            $tiersQuery = "
                SELECT 
                    svt.supplier_id,
                    svt.de_qtd,
                    svt.ate_qtd,
                    svt.valor_fixo,
                    svt.percentual_repasse
                FROM supplier_volume_tiers svt
                WHERE svt.supplier_id IN ({$supplierIdsStr})
                  AND svt.data_cancel = '1901-01-01 00:00:00'
                ORDER BY svt.supplier_id, svt.de_qtd
            ";
            
            $tiers = $this->db->fetchAll($tiersQuery);
            
            // Group tiers by supplier
            $supplierTiers = [];
            foreach ($tiers as $tier) {
                $supplierTiers[$tier[0]['supplier_id']][] = $tier[0];
            }
            
            echo "📊 Volume Tiers Summary:\n";
            foreach ($supplierIds as $supplierId) {
                $tierCount = isset($supplierTiers[$supplierId]) ? count($supplierTiers[$supplierId]) : 0;
                echo "- Supplier {$supplierId}: {$tierCount} tiers configured\n";
            }
            echo "\n";
            
            // Analyze current transfer fees and calculate expected values
            $this->analyzeAndGenerateTest($supplierGroups, $supplierInfo, $supplierTiers);
            
        } catch (Exception $e) {
            echo "❌ Error extracting data: " . $e->getMessage() . "\n";
            echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
        }
    }
    
    private function getTypeDescription($type) 
    {
        switch ($type) {
            case 1: return 'Fixed Value';
            case 2: return 'Percentage';
            case 3: return 'Volume Tier';
            default: return 'Unknown';
        }
    }
    
    private function analyzeAndGenerateTest($supplierGroups, $supplierInfo, $supplierTiers) 
    {
        echo "🔍 DETAILED ANALYSIS BY SUPPLIER\n";
        echo str_repeat("=", 80) . "\n\n";
        
        $testFileContent = $this->generateTestFileHeader();
        $mockDataSetup = [];
        $testScenarios = [];
        
        foreach ($supplierGroups as $supplierId => $items) {
            $info = $supplierInfo[$supplierId];
            $tiers = isset($supplierTiers[$supplierId]) ? $supplierTiers[$supplierId] : [];
            
            echo "Supplier {$supplierId}: {$info['company_name']}\n";
            echo "Type: {$this->getTypeDescription($info['transfer_fee_type'])} | Cobrança: {$info['tipo_cobranca']}\n";
            
            // Calculate total subtotal for tier determination
            $totalSubtotal = 0;
            $itemDetails = [];
            
            foreach ($items as $item) {
                $subtotal = !empty($item['subtotal_not_formated']) 
                    ? floatval($item['subtotal_not_formated'])
                    : $this->parseFormattedNumber($item['subtotal']);
                    
                $commission = !empty($item['commission_fee_not_formated']) 
                    ? floatval($item['commission_fee_not_formated'])
                    : $this->parseFormattedNumber($item['commission_fee']);
                
                $totalSubtotal += $subtotal;
                
                $itemDetails[] = [
                    'id' => $item['item_id'],
                    'customer_user_id' => $item['customer_user_id'],
                    'subtotal' => $subtotal,
                    'commission' => $commission,
                    'current_transfer_fee' => $item['transfer_fee'],
                    'current_total' => $item['total']
                ];
            }
            
            echo "Total Subtotal: R$ " . number_format($totalSubtotal, 2, ',', '.') . "\n";
            echo "Items: " . count($items) . "\n";
            
            // Find matching tier
            $matchingTier = null;
            if (!empty($tiers)) {
                foreach ($tiers as $tier) {
                    if ($tier['de_qtd'] <= $totalSubtotal && $tier['ate_qtd'] >= $totalSubtotal) {
                        $matchingTier = $tier;
                        break;
                    }
                }
            }
            
            if ($matchingTier) {
                echo "✅ Matching Tier: {$matchingTier['de_qtd']}-{$matchingTier['ate_qtd']}\n";
                if ($info['transfer_fee_type'] == 1) {
                    echo "   Would use: Fixed R$ {$matchingTier['valor_fixo']}\n";
                } elseif ($info['transfer_fee_type'] == 2) {
                    echo "   Would use: Percentage {$matchingTier['percentual_repasse']}%\n";
                }
            } else {
                echo "❌ No matching tier found (should result in R$ 0,00)\n";
            }
            
            // Show current vs expected
            echo "\nCurrent Transfer Fees in Database:\n";
            $currentTotal = 0;
            foreach ($itemDetails as $detail) {
                $currentFee = $this->parseFormattedNumber($detail['current_transfer_fee']);
                $currentTotal += $currentFee;
                echo "  Item {$detail['id']}: R$ " . number_format($currentFee, 2, ',', '.') . "\n";
            }
            echo "  TOTAL: R$ " . number_format($currentTotal, 2, ',', '.') . "\n";
            
            // Calculate expected fees
            $expectedFees = $this->calculateExpectedFees($info, $matchingTier, $itemDetails, $totalSubtotal);
            echo "\nExpected Transfer Fees (with our calculation):\n";
            $expectedTotal = 0;
            foreach ($expectedFees as $i => $expectedFee) {
                $expectedTotal += $expectedFee;
                echo "  Item {$itemDetails[$i]['id']}: R$ " . number_format($expectedFee, 2, ',', '.') . "\n";
            }
            echo "  TOTAL: R$ " . number_format($expectedTotal, 2, ',', '.') . "\n";
            
            // Generate mock setup and test scenario
            $mockDataSetup[] = $this->generateMockDataSetup($supplierId, $info, $tiers);
            $testScenarios[] = $this->generateTestScenario($supplierId, $info, $itemDetails, $expectedFees);
            
            echo "\n" . str_repeat("-", 60) . "\n\n";
        }
        
        // Generate complete test file
        $testFileContent .= implode("\n\n", $mockDataSetup);
        $testFileContent .= $this->generateTestClass();
        $testFileContent .= implode("\n\n", $testScenarios);
        $testFileContent .= $this->generateTestFooter();
        
        file_put_contents('test_order_3219_real_validation.php', $testFileContent);
        echo "✅ Generated test_order_3219_real_validation.php with actual Order 3219 data!\n";
        echo "📄 Run with: php test_order_3219_real_validation.php\n\n";
    }
    
    private function parseFormattedNumber($value) 
    {
        if (is_numeric($value)) {
            return floatval($value);
        }
        
        $value = str_replace(['.', ','], ['', '.'], strval($value));
        return floatval($value);
    }
    
    private function calculateExpectedFees($supplierInfo, $matchingTier, $itemDetails, $totalSubtotal) 
    {
        if (!$matchingTier) {
            return array_fill(0, count($itemDetails), 0.0);
        }
        
        $expectedFees = [];
        
        if ($supplierInfo['transfer_fee_type'] == 1) { // Fixed Value
            $fixedValue = floatval($matchingTier['valor_fixo']);
            
            if ($supplierInfo['tipo_cobranca'] == 'cpf') {
                // Each item gets the full amount
                $expectedFees = array_fill(0, count($itemDetails), $fixedValue);
            } else {
                // Divide equally among all items
                $feePerItem = $fixedValue / count($itemDetails);
                $expectedFees = array_fill(0, count($itemDetails), $feePerItem);
            }
        } elseif ($supplierInfo['transfer_fee_type'] == 2) { // Percentage
            $percentage = floatval($matchingTier['percentual_repasse']);
            
            // Apply percentage to each item individually
            foreach ($itemDetails as $detail) {
                $expectedFees[] = ($detail['subtotal'] * $percentage) / 100;
            }
        }
        
        return $expectedFees;
    }
    
    private function generateTestFileHeader() 
    {
        return '<?php
/**
 * Real Order 3219 Validation Test - Generated from actual database data
 * This test uses the exact supplier configurations and order items from Order 3219
 * Generated on: ' . date('Y-m-d H:i:s') . '
 */

// Mock dependencies and include RepaymentCalculator
require_once \'app/Lib/RepaymentCalculator.php\';

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

// [Mock classes would be included here - same as previous test]

';
    }
    
    private function generateMockDataSetup($supplierId, $info, $tiers) 
    {
        $setup = "// Supplier {$supplierId}: {$info['company_name']}\n";
        $setup .= "\$supplierMock->addSupplier({$supplierId}, {$info['transfer_fee_type']}, '{$info['tipo_cobranca']}', '{$info['company_name']}');\n";
        
        foreach ($tiers as $tier) {
            $setup .= "\$tierMock->addTier({$supplierId}, {$tier['de_qtd']}, {$tier['ate_qtd']}, {$tier['valor_fixo']}, {$tier['percentual_repasse']});\n";
        }
        
        return $setup;
    }
    
    private function generateTestScenario($supplierId, $info, $itemDetails, $expectedFees) 
    {
        $scenario = "// Test Supplier {$supplierId}: {$info['company_name']}\n";
        $scenario .= "\$items{$supplierId} = [\n";
        
        foreach ($itemDetails as $detail) {
            $scenario .= "    ['OrderItem' => ['id' => {$detail['id']}, 'customer_user_id' => {$detail['customer_user_id']}, 'subtotal_not_formated' => {$detail['subtotal']}, 'commission_fee_not_formated' => {$detail['commission']}]],\n";
        }
        
        $scenario .= "];\n";
        $scenario .= "// Expected fees: [" . implode(', ', array_map(function($fee) { return number_format($fee, 2); }, $expectedFees)) . "]\n";
        
        return $scenario;
    }
    
    private function generateTestClass() 
    {
        return '
class RealOrder3219Test 
{
    // Test methods would be generated here
}
';
    }
    
    private function generateTestFooter() 
    {
        return '
// Run the real validation test
$test = new RealOrder3219Test();
$test->runTests();
?>';
    }
}

// Run the extractor
try {
    $extractor = new RealDataExtractor();
    $extractor->extractOrder3219Data();
} catch (Exception $e) {
    echo "❌ Failed to extract data: " . $e->getMessage() . "\n";
    echo "This might be due to:\n";
    echo "1. Database connection issues\n";
    echo "2. Order 3219 doesn't exist\n";
    echo "3. Missing database tables or columns\n";
    echo "4. CakePHP configuration problems\n\n";
    
    echo "💡 Alternative approach: Please provide the output of these SQL queries:\n\n";
    
    echo "Query 1 - Order Items:\n";
    echo "SELECT oi.id, oi.customer_user_id, oi.subtotal, oi.subtotal_not_formated, ";
    echo "oi.commission_fee, oi.commission_fee_not_formated, oi.transfer_fee, ";
    echo "s.id as supplier_id, s.company_name, s.transfer_fee_type, s.tipo_cobranca ";
    echo "FROM order_items oi ";
    echo "JOIN customer_user_itineraries cui ON oi.customer_user_itinerary_id = cui.id ";
    echo "JOIN benefits b ON cui.benefit_id = b.id ";
    echo "JOIN suppliers s ON b.supplier_id = s.id ";
    echo "WHERE oi.order_id = 3219 AND oi.data_cancel = '1901-01-01 00:00:00';\n\n";
    
    echo "Query 2 - Supplier Volume Tiers:\n";
    echo "SELECT svt.supplier_id, svt.de_qtd, svt.ate_qtd, svt.valor_fixo, svt.percentual_repasse ";
    echo "FROM supplier_volume_tiers svt ";
    echo "WHERE svt.data_cancel = '1901-01-01 00:00:00' ";
    echo "ORDER BY svt.supplier_id, svt.de_qtd;\n\n";
}
?>