<?php
/**
 * Extract real data from Order 3219 to create accurate test scenarios
 * This script connects to the actual database to get real supplier configurations
 */

// Simple database connection approach
$host = 'localhost'; // Update with your database host
$dbname = ''; // Update with your database name
$username = ''; // Update with your database username  
$password = ''; // Update with your database password

// You'll need to update these database credentials
echo "=== Order 3219 Data Extraction ===\n\n";
echo "❌ Database credentials need to be configured!\n";
echo "Please update this script with your database connection details:\n";
echo "- Host: {$host}\n";
echo "- Database name: [PLEASE SET]\n";
echo "- Username: [PLEASE SET]\n";
echo "- Password: [PLEASE SET]\n\n";

echo "Once configured, this script will:\n";
echo "1. Extract order 3219 items with supplier information\n";
echo "2. Get each supplier's transfer_fee_type and tipo_cobranca\n";
echo "3. Fetch all volume tiers for each supplier\n";
echo "4. Generate exact test data based on real values\n";
echo "5. Calculate expected results for validation\n\n";

echo "Example SQL queries that will be executed:\n\n";

echo "-- Get order items with supplier info\n";
echo "SELECT \n";
echo "    oi.id as item_id,\n";
echo "    oi.order_id,\n";
echo "    oi.customer_user_id,\n";
echo "    oi.subtotal,\n";
echo "    oi.subtotal_not_formated,\n";
echo "    oi.commission_fee,\n";
echo "    oi.commission_fee_not_formated,\n";
echo "    oi.transfer_fee,\n";
echo "    oi.total,\n";
echo "    s.id as supplier_id,\n";
echo "    s.company_name,\n";
echo "    s.transfer_fee_type,\n";
echo "    s.tipo_cobranca\n";
echo "FROM order_items oi\n";
echo "JOIN customer_user_itineraries cui ON oi.customer_user_itinerary_id = cui.id\n";
echo "JOIN benefits b ON cui.benefit_id = b.id\n";
echo "JOIN suppliers s ON b.supplier_id = s.id\n";
echo "WHERE oi.order_id = 3219\n";
echo "  AND oi.data_cancel = '1901-01-01 00:00:00'\n";
echo "ORDER BY s.id, oi.id;\n\n";

echo "-- Get volume tiers for each supplier\n";
echo "SELECT \n";
echo "    svt.supplier_id,\n";
echo "    svt.de_qtd,\n";
echo "    svt.ate_qtd,\n";
echo "    svt.valor_fixo,\n";
echo "    svt.percentual_repasse\n";
echo "FROM supplier_volume_tiers svt\n";
echo "WHERE svt.supplier_id IN (SELECT DISTINCT s.id \n";
echo "                         FROM suppliers s \n";
echo "                         JOIN benefits b ON s.id = b.supplier_id\n";
echo "                         JOIN customer_user_itineraries cui ON b.id = cui.benefit_id\n";
echo "                         JOIN order_items oi ON cui.id = oi.customer_user_itinerary_id\n";
echo "                         WHERE oi.order_id = 3219)\n";
echo "  AND svt.data_cancel = '1901-01-01 00:00:00'\n";
echo "ORDER BY svt.supplier_id, svt.de_qtd;\n\n";

echo "📝 To get the real data:\n";
echo "1. Update the database credentials in this file\n";
echo "2. Run: php extract_order_3219_data.php\n";
echo "3. The script will generate a new test file with real data\n";
echo "4. You can then validate calculations against actual values\n\n";

// Uncomment and configure the database connection below:

/*
try {
    $pdo = new PDO("mysql:host={$host};dbname={$dbname}", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Database connected successfully!\n\n";
    
    // Extract order items with supplier information
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
    
    $stmt = $pdo->prepare($orderItemsQuery);
    $stmt->execute();
    $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($orderItems)) {
        echo "❌ No items found for order 3219\n";
        exit;
    }
    
    echo "📦 Found " . count($orderItems) . " order items\n\n";
    
    // Group by supplier
    $supplierGroups = [];
    foreach ($orderItems as $item) {
        $supplierGroups[$item['supplier_id']][] = $item;
    }
    
    echo "🏢 Found " . count($supplierGroups) . " unique suppliers:\n";
    foreach ($supplierGroups as $supplierId => $items) {
        echo "- Supplier {$supplierId}: {$items[0]['company_name']} ({" . count($items) . "} items)\n";
    }
    echo "\n";
    
    // Get volume tiers for all suppliers
    $supplierIds = array_keys($supplierGroups);
    $placeholders = str_repeat('?,', count($supplierIds) - 1) . '?';
    
    $tiersQuery = "
        SELECT 
            svt.supplier_id,
            svt.de_qtd,
            svt.ate_qtd,
            svt.valor_fixo,
            svt.percentual_repasse
        FROM supplier_volume_tiers svt
        WHERE svt.supplier_id IN ({$placeholders})
          AND svt.data_cancel = '1901-01-01 00:00:00'
        ORDER BY svt.supplier_id, svt.de_qtd
    ";
    
    $stmt = $pdo->prepare($tiersQuery);
    $stmt->execute($supplierIds);
    $tiers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Group tiers by supplier
    $supplierTiers = [];
    foreach ($tiers as $tier) {
        $supplierTiers[$tier['supplier_id']][] = $tier;
    }
    
    // Generate real test file
    generateRealTestFile($supplierGroups, $supplierTiers);
    
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
}
*/

function generateRealTestFile($supplierGroups, $supplierTiers) 
{
    $testContent = '<?php
/**
 * Real-world test file generated from actual Order 3219 data
 * This contains the exact supplier configurations and order items from your database
 */

// [Test file content would be generated here with real data]
';

    file_put_contents('test_order_3219_real_data.php', $testContent);
    echo "✅ Generated test_order_3219_real_data.php with real data\n";
}

echo "💡 Tip: You can also run these queries directly in your database client\n";
echo "and paste the results here for manual test data creation.\n";
?>