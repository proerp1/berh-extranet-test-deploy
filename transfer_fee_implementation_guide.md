# Transfer Fee Implementation Guide for Clients Area

## Overview
This guide documents the implementation of consolidated transfer fee calculations using RepaymentCalculator. The logic ensures proper two-phase calculation for volume tier fees while maintaining individual fee display for other types.

## Core Principles

### 1. Two-Phase Calculation Approach
**Phase 1 (Individual Item Creation):**
- Fixed value (type 1): Applied immediately
- Percentage (type 2): Applied immediately  
- Volume tier (type 3): Set to 0, calculated in Phase 2

**Phase 2 (After All Items Created):**
- `recalculateOrderTransferFees()` calculates volume tiers with full order context
- Distributes fees proportionally among items
- Handles all billing types (`pedido` vs `cpf`) correctly

### 2. RepaymentCalculator Integration
**Shared Logic:**
- RepaymentCalculator handles all calculation types
- Returns: `repayment_value`, `calculation_method`, `tier_used`, `repayment_percentage`
- Supports both `valor_fixo` (fixed) and `percentual_repasse` (percentage) from volume tiers
- Includes number parsing for Brazilian format ("10,50" → 10.50)

## Implementation Steps

### Step 1: Update Initial Order Item Creation Logic

**In your OrdersController (or equivalent) `calculateSupplierTransferFee` method:**

```php
private function calculateSupplierTransferFee($benefit, $subtotal, $quantity = 1, $orderData = [])
{
    // ... validation code ...

    try {
        switch ($transferFeeType) {
            case 1: // Fixed Value
                $result['transfer_fee'] = $transferFeePercentage;
                $result['calculation_method'] = 'fixed_value';
                break;

            case 2: // Fixed Percentage
                $result['transfer_fee'] = $subtotal * ($transferFeePercentage / 100);
                $result['calculation_method'] = 'fixed_percentage';
                break;

            case 3: // Volume Tier
                // Volume tier calculation needs full order context
                // Will be calculated properly in recalculateOrderTransferFees()
                $result['transfer_fee'] = 0;
                $result['calculation_method'] = 'volume_tier_pending';
                $result['tier_info'] = 'Will be calculated after all items are created';
                break;
                
            default:
                // Fallback
                $result['transfer_fee'] = $transferFeePercentage;
                $result['calculation_method'] = 'fixed_value_fallback';
        }
    } catch (Exception $e) {
        // Error handling
    }

    return $result;
}
```

### Step 2: Implement Order-Level Recalculation

**Add this method to your OrdersController:**

```php
public function recalculateOrderTransferFees($orderId)
{
    App::uses('RepaymentCalculator', 'Lib');
    
    // Get all order items with supplier information
    $orderItems = $this->OrderItem->find('all', [
        'contain' => [
            'CustomerUserItinerary' => [
                'Benefit' => [
                    'Supplier'
                ]
            ]
        ],
        'conditions' => [
            'OrderItem.order_id' => $orderId,
            'OrderItem.data_cancel' => '1901-01-01 00:00:00'
        ]
    ]);

    if (empty($orderItems)) {
        return;
    }

    // Group items by supplier
    $supplierGroups = [];
    foreach ($orderItems as $item) {
        $supplierId = $item['CustomerUserItinerary']['Benefit']['Supplier']['id'];
        $supplierGroups[$supplierId][] = $item;
    }

    // Calculate fees per supplier using RepaymentCalculator
    foreach ($supplierGroups as $supplierId => $items) {
        $supplier = $items[0]['CustomerUserItinerary']['Benefit']['Supplier'];
        $this->calculateTransferFeesForSupplier($orderId, $supplierId, $supplier, $items);
    }
}

private function calculateTransferFeesForSupplier($orderId, $supplierId, $supplier, $items)
{
    if (empty($items)) {
        return;
    }

    $tipoCobranca = isset($supplier['tipo_cobranca']) ? $supplier['tipo_cobranca'] : 'pedido';

    // Calculate total subtotal for this supplier
    $totalSupplierSubtotal = 0;
    foreach ($items as $item) {
        $subtotalValue = isset($item['OrderItem']['subtotal_not_formated']) 
            ? $item['OrderItem']['subtotal_not_formated'] 
            : $this->parseFormattedNumber($item['OrderItem']['subtotal']);
        $totalSupplierSubtotal += $subtotalValue;
    }

    if ($totalSupplierSubtotal == 0) {
        return;
    }

    // Determine quantity for calculation based on billing type
    if ($tipoCobranca == 'cpf') {
        $quantity = $this->countCustomerUsersForSupplier($supplierId);
    } else {
        $quantity = $this->getTotalAmountForSupplier($supplierId);
    }

    // Use RepaymentCalculator for all calculations
    try {
        App::uses('RepaymentCalculator', 'Lib');
        $calculationResult = RepaymentCalculator::calculateRepayment(
            $supplierId, 
            $quantity, 
            $totalSupplierSubtotal
        );
        
        $totalTransferFee = $this->parseFormattedNumber($calculationResult['repayment_value']);
        $calculationMethod = $calculationResult['calculation_method'];
        $tierUsed = $calculationResult['tier_used'];

        // Distribute fees among items based on calculation method
        if ($calculationMethod === 'volume_tier_percentage' && $calculationResult['billing_type'] == 'item') {
            // Individual percentage application
            $repaymentPercentage = $this->parseFormattedNumber($calculationResult['repayment_percentage']);
            $this->applyIndividualPercentageFees($items, $repaymentPercentage, $calculationMethod, $tierUsed);
        } else {
            // Proportional distribution (for volume tiers, fixed values, etc.)
            $this->distributeFeesProportionally($items, $totalTransferFee, $totalSupplierSubtotal, $calculationMethod, $tierUsed, $tipoCobranca);
        }

    } catch (Exception $e) {
        // Silent error handling
    }
}

private function applyIndividualPercentageFees($items, $percentage, $calculationMethod, $tierUsed)
{
    foreach ($items as $item) {
        $itemSubtotal = isset($item['OrderItem']['subtotal_not_formated']) 
            ? $item['OrderItem']['subtotal_not_formated'] 
            : $this->parseFormattedNumber($item['OrderItem']['subtotal']);
            
        $itemCommissionFee = isset($item['OrderItem']['commission_fee_not_formated']) 
            ? $item['OrderItem']['commission_fee_not_formated'] 
            : $this->parseFormattedNumber($item['OrderItem']['commission_fee']);

        $itemTransferFee = ($itemSubtotal * $percentage) / 100;

        $calculationLog = json_encode([
            'type' => $calculationMethod,
            'percentage' => $percentage,
            'tier_used' => $tierUsed,
            'item_subtotal' => $itemSubtotal,
            'calculated_fee' => $itemTransferFee
        ]);

        $this->updateOrderItemFees($item['OrderItem']['id'], $itemTransferFee, $itemSubtotal, $itemCommissionFee, $calculationLog);
    }
}

private function distributeFeesProportionally($items, $totalTransferFee, $totalSupplierSubtotal, $calculationMethod, $tierUsed, $tipoCobranca)
{
    foreach ($items as $item) {
        $itemSubtotal = isset($item['OrderItem']['subtotal_not_formated']) 
            ? $item['OrderItem']['subtotal_not_formated'] 
            : $this->parseFormattedNumber($item['OrderItem']['subtotal']);
            
        $itemCommissionFee = isset($item['OrderItem']['commission_fee_not_formated']) 
            ? $item['OrderItem']['commission_fee_not_formated'] 
            : $this->parseFormattedNumber($item['OrderItem']['commission_fee']);
            
        $proportion = $totalSupplierSubtotal > 0 ? ($itemSubtotal / $totalSupplierSubtotal) : 0;
        $itemTransferFee = $totalTransferFee * $proportion;

        $calculationLog = json_encode([
            'type' => $calculationMethod,
            'billing_type' => $tipoCobranca,
            'tier_used' => $tierUsed,
            'total_fee' => $totalTransferFee,
            'proportion' => $proportion,
            'calculated_fee' => $itemTransferFee
        ]);

        $this->updateOrderItemFees($item['OrderItem']['id'], $itemTransferFee, $itemSubtotal, $itemCommissionFee, $calculationLog);
    }
}

private function updateOrderItemFees($itemId, $transferFee, $subtotal, $commissionFee, $calculationLog)
{
    $updateData = [
        'OrderItem' => [
            'id' => $itemId,
            'transfer_fee' => $transferFee,
            'total' => $subtotal + $transferFee + $commissionFee,
            'calculation_details_log' => $calculationLog
        ]
    ];
    
    $this->OrderItem->save($updateData, ['callbacks' => false, 'validate' => false]);
}

private function parseFormattedNumber($formattedValue)
{
    if (is_numeric($formattedValue)) {
        return floatval($formattedValue);
    }
    
    // Handle Brazilian format: 1.234,56 -> 1234.56
    $value = trim($formattedValue);
    
    // If there's both dot and comma, remove dots (thousands separator) and replace comma with dot
    if (strpos($value, '.') !== false && strpos($value, ',') !== false) {
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);
    } elseif (strpos($value, ',') !== false) {
        // If there's only comma, replace it with dot
        $value = str_replace(',', '.', $value);
    }
    
    return floatval($value);
}
```

### Step 3: Update Order Processing Workflows

**Call recalculation in all methods that create/modify order items:**

1. **processItineraries() method:**
```php
// At the end of the method, after all items are created:
$this->recalculateOrderTransferFees($orderId);
```

2. **updateWorkingDays() method:**
```php
public function updateWorkingDays()
{
    // ... existing logic ...
    
    // Set transfer fee to 0 initially (will be recalculated properly)
    $orderItem['OrderItem']['transfer_fee'] = 0;
    $orderItem['OrderItem']['calculation_details_log'] = json_encode(['note' => 'Will be calculated with full order context']);

    // ... save orderItem ...

    // Recalculate transfer fees for all supplier types
    $this->recalculateOrderTransferFees($orderItem['OrderItem']['order_id']);

    // ... reprocess amounts and return response ...
    
    // Get updated order item data after recalculation
    $updatedOrderItem = $this->OrderItem->findById($itemId, null, null, -1);
    
    echo json_encode([
        'success' => true,
        'subtotal' => $updatedOrderItem['OrderItem']['subtotal'],
        'transfer_fee' => $updatedOrderItem['OrderItem']['transfer_fee'],
        'commission_fee' => $updatedOrderItem['OrderItem']['commission_fee'],
        'total' => $updatedOrderItem['OrderItem']['total'],
        'calculation_details_log' => $updatedOrderItem['OrderItem']['calculation_details_log'],
        // ... other response data ...
    ]);
}
```

3. **upload_user_csv() and similar bulk operations:**
```php
// After calling processItineraries():
$this->processItineraries($customerItineraries, $orderId, /* ... */);
// processItineraries already calls recalculateOrderTransferFees()
```

### Step 4: Update Order Model for Consolidated Calculations

**If you need to remove duplication from Order model, replace with:**

```php
// In Order.php
public function recalculateVolumeTransferFees()
{
    // Get all order items with supplier information
    $orderItems = $this->OrderItem->find('all', [
        'contain' => [
            'CustomerUserItinerary' => [
                'Benefit' => [
                    'Supplier'
                ]
            ]
        ],
        'conditions' => [
            'OrderItem.order_id' => $this->id,
            'OrderItem.data_cancel' => '1901-01-01 00:00:00'
        ]
    ]);

    if (empty($orderItems)) {
        return;
    }

    // Group items by supplier
    $supplierGroups = [];
    foreach ($orderItems as $item) {
        $supplierId = $item['CustomerUserItinerary']['Benefit']['Supplier']['id'];
        $supplierGroups[$supplierId][] = $item;
    }

    // Process each supplier group using RepaymentCalculator
    foreach ($supplierGroups as $supplierId => $items) {
        $this->calculateSupplierFeesUsingRepaymentCalculator($supplierId, $items);
    }
}

private function calculateSupplierFeesUsingRepaymentCalculator($supplierId, $items)
{
    // ... implementation similar to controller method ...
}
```

### Step 5: Update Display Logic in Views

**In your orders view (add.ctp or equivalent):**

```php
<td class="fw-bold fs-7 ps-4 transfer_fee_line" data-valor="<?php echo $items[$i]["OrderItem"]["transfer_fee_not_formated"]; ?>">
    <?php 
    // Check if this item uses volume tier fixed calculation
    $calculationDetails = json_decode($items[$i]["OrderItem"]["calculation_details_log"], true);
    $isVolumeFixedByOrder = isset($calculationDetails['type']) && $calculationDetails['type'] === 'volume_tier_fixed' && 
                           isset($calculationDetails['billing_type']) && $calculationDetails['billing_type'] === 'pedido';
    
    if ($isVolumeFixedByOrder) {
        // Show R$ 0,00 with tooltip for volume tier fixed items
        echo '<span data-bs-toggle="tooltip" data-bs-placement="top" title="Taxa aplicada por pedido devido à presença deste fornecedor">R$ 0,00 <i class="fas fa-info-circle text-info"></i></span>';
    } else {
        // Show normal transfer fee for other calculation types
        echo 'R$' . $items[$i]["OrderItem"]["transfer_fee"];
    }
    ?>
</td>
```

**For AJAX updates (JavaScript):**

```javascript
// In working days or similar AJAX success functions:
success: function(response) {
    // ... update other fields ...
    
    // Check if this should show consolidated fee display
    var calculationDetails = response.calculation_details_log ? JSON.parse(response.calculation_details_log) : {};
    var isVolumeFixedByOrder = calculationDetails.type === 'volume_tier_fixed' && calculationDetails.billing_type === 'pedido';
    
    if (isVolumeFixedByOrder) {
        // Show R$ 0,00 with tooltip for volume tier fixed items
        line.find('.transfer_fee_line').html('<span data-bs-toggle="tooltip" data-bs-placement="top" title="Taxa aplicada por pedido devido à presença deste fornecedor">R$ 0,00 <i class="fas fa-info-circle text-info"></i></span>');
    } else {
        line.find('.transfer_fee_line').html('R$' + response.transfer_fee);
    }
    
    // ... update totals ...
}
```

## Helper Methods You May Need

### Count Customer Users for Supplier
```php
private function countCustomerUsersForSupplier($supplierId, $orderId = null)
{
    $conditions = [
        'CustomerUserItinerary.status_id' => 1,
        'Benefit.supplier_id' => $supplierId,
        'OrderItem.data_cancel' => '1901-01-01 00:00:00'
    ];
    
    if ($orderId) {
        $conditions['OrderItem.order_id'] = $orderId;
    }
    
    return $this->OrderItem->find('count', [
        'joins' => [
            // Add appropriate joins for your schema
        ],
        'conditions' => $conditions,
        'group' => 'OrderItem.customer_user_id'
    ]);
}
```

### Get Total Amount for Supplier
```php
private function getTotalAmountForSupplierInOrder($supplierId, $orderId)
{
    $result = $this->OrderItem->find('first', [
        'joins' => [
            // Add appropriate joins for your schema
        ],
        'conditions' => [
            'OrderItem.order_id' => $orderId,
            'Benefit.supplier_id' => $supplierId,
            'OrderItem.data_cancel' => '1901-01-01 00:00:00'
        ],
        'fields' => ['SUM(OrderItem.subtotal) as total_amount']
    ]);
    
    return $result[0]['total_amount'] ?? 0;
}
```

## Key Points to Remember

1. **RepaymentCalculator is shared** - no need to copy it
2. **Always call `parseFormattedNumber()`** on values from RepaymentCalculator
3. **Two-phase approach is crucial** for volume tier accuracy
4. **Display logic distinguishes** between `volume_tier_fixed` + `pedido` and other types
5. **AJAX responses must include** `calculation_details_log` for proper display updates
6. **Error handling should be silent** to avoid breaking order processing
7. **Test with different billing types** (`pedido` vs `cpf`) and transfer fee types

## Testing Checklist

- [ ] Fixed value fees (type 1) applied immediately
- [ ] Percentage fees (type 2) applied immediately  
- [ ] Volume tier fees (type 3) show R$ 0,00 during creation, correct amount after recalculation
- [ ] `pedido` billing shows tooltips for volume tier fixed
- [ ] `cpf` billing calculates correctly
- [ ] AJAX updates maintain proper display logic
- [ ] Order totals are accurate after recalculation
- [ ] Brazilian number formats parsed correctly
- [ ] Multiple suppliers in same order calculated independently

This implementation ensures accurate transfer fee calculations while providing clear user feedback about consolidated fees.