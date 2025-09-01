<?php
/**
 * SQL Queries to extract Order 3219 data manually
 * Run these queries in your database and paste the results back
 */

echo "=== SQL Queries to Get Real Order 3219 Data ===\n\n";

echo "Please run these 2 queries in your database client and provide the results:\n\n";

echo "📋 QUERY 1: Order 3219 Items with Supplier Information\n";
echo str_repeat("-", 60) . "\n";
echo "SELECT \n";
echo "    oi.id as item_id,\n";
echo "    oi.customer_user_id,\n";
echo "    oi.subtotal,\n";
echo "    oi.subtotal_not_formated,\n";
echo "    oi.commission_fee,\n";
echo "    oi.commission_fee_not_formated,\n";
echo "    oi.transfer_fee as current_transfer_fee,\n";
echo "    oi.total as current_total,\n";
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

echo "📊 QUERY 2: Volume Tiers for Suppliers in Order 3219\n";
echo str_repeat("-", 60) . "\n";
echo "SELECT \n";
echo "    svt.supplier_id,\n";
echo "    svt.de_qtd,\n";
echo "    svt.ate_qtd,\n";
echo "    svt.valor_fixo,\n";
echo "    svt.percentual_repasse\n";
echo "FROM supplier_volume_tiers svt\n";
echo "WHERE svt.supplier_id IN (\n";
echo "    SELECT DISTINCT s.id \n";
echo "    FROM suppliers s \n";
echo "    JOIN benefits b ON s.id = b.supplier_id\n";
echo "    JOIN customer_user_itineraries cui ON b.id = cui.benefit_id\n";
echo "    JOIN order_items oi ON cui.id = oi.customer_user_itinerary_id\n";
echo "    WHERE oi.order_id = 3219\n";
echo "      AND oi.data_cancel = '1901-01-01 00:00:00'\n";
echo ")\n";
echo "AND svt.data_cancel = '1901-01-01 00:00:00'\n";
echo "ORDER BY svt.supplier_id, svt.de_qtd;\n\n";

echo "📝 Instructions:\n";
echo "1. Copy and run QUERY 1 in your database client\n";
echo "2. Export results as CSV or copy as text\n";
echo "3. Copy and run QUERY 2 in your database client  \n";
echo "4. Export results as CSV or copy as text\n";
echo "5. Provide both results and I'll generate the real test file\n\n";

echo "💡 Alternative: If you prefer, you can also just tell me:\n";
echo "- How many suppliers are in order 3219?\n";
echo "- What are their transfer_fee_type and tipo_cobranca values?\n";
echo "- How many items per supplier?\n";
echo "- What are the approximate subtotal ranges?\n";
echo "And I can create realistic test scenarios based on that info.\n\n";

// Create a simple manual data entry template
echo "📄 MANUAL DATA ENTRY TEMPLATE\n";
echo str_repeat("-", 60) . "\n";
echo "If the SQL queries don't work, you can manually fill this template:\n\n";

echo "// Example format - replace with real data\n";
echo "Supplier 1:\n";
echo "  - ID: ?\n";
echo "  - Name: ?\n";
echo "  - transfer_fee_type: ? (1=Fixed, 2=Percentage, 3=Volume)\n";
echo "  - tipo_cobranca: ? (cpf or pedido)\n";
echo "  - Items: ? (how many order items)\n";
echo "  - Total subtotal: R$ ?\n";
echo "  - Current transfer fees in DB: [item1: R$?, item2: R$?, ...]\n";
echo "  - Tiers: [range1: ?-?, fixed: R$?, percent: ?%] [range2: ?-?, ...]\n\n";

echo "Supplier 2:\n";
echo "  - (same format as above)\n\n";

echo "Just provide this information in any format and I'll create the proper test!\n";
?>