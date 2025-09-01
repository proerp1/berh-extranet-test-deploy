<?php
/**
 * Unit tests for Order Transfer Fee Calculations
 * Tests all combinations of transfer_fee_type and tipo_cobranca
 */

App::uses('OrdersController', 'Controller');
App::uses('RepaymentCalculator', 'Lib');

class OrdersTransferFeeCalculationTest extends CakeTestCase
{
    public $fixtures = [
        'app.order_item',
        'app.supplier', 
        'app.supplier_volume_tier'
    ];

    public function setUp()
    {
        parent::setUp();
        $this->OrdersController = new OrdersController();
        $this->OrdersController->constructClasses();
    }

    public function tearDown()
    {
        unset($this->OrdersController);
        parent::tearDown();
    }

    /**
     * Test Fixed Value by CPF calculation
     * Should apply fixed amount to ALL items for each unique customer user
     */
    public function testFixedValueByCpf()
    {
        // Setup test data
        $supplier = [
            'id' => 1,
            'transfer_fee_type' => 1, // Fixed
            'tipo_cobranca' => 'cpf',
            'transfer_fee_fixed' => 10.00
        ];

        $items = [
            // Customer User 1 - 2 items
            [
                'OrderItem' => [
                    'id' => 1,
                    'customer_user_id' => 100,
                    'subtotal_not_formated' => 100.00,
                    'commission_fee_not_formated' => 5.00
                ]
            ],
            [
                'OrderItem' => [
                    'id' => 2,
                    'customer_user_id' => 100, // Same customer
                    'subtotal_not_formated' => 150.00,
                    'commission_fee_not_formated' => 7.50
                ]
            ],
            // Customer User 2 - 1 item
            [
                'OrderItem' => [
                    'id' => 3,
                    'customer_user_id' => 200, // Different customer
                    'subtotal_not_formated' => 200.00,
                    'commission_fee_not_formated' => 10.00
                ]
            ]
        ];

        // Mock the OrderItem save method to capture the data
        $savedData = [];
        $this->OrdersController->OrderItem = $this->getMock('OrderItem', ['save']);
        $this->OrdersController->OrderItem->expects($this->exactly(3))
            ->method('save')
            ->will($this->returnCallback(function($data) use (&$savedData) {
                $savedData[] = $data;
                return true;
            }));

        // Execute the calculation
        $reflection = new ReflectionClass($this->OrdersController);
        $method = $reflection->getMethod('calculateFixedValueFees');
        $method->setAccessible(true);
        $method->invoke($this->OrdersController, 1, 1, $supplier, $items);

        // Assertions
        $this->assertEquals(3, count($savedData), 'Should save 3 items');
        
        // All items should have $10 transfer fee (fixed by CPF)
        foreach ($savedData as $data) {
            $this->assertEquals(10.00, $data['OrderItem']['transfer_fee'], 'Each item should have $10 transfer fee');
            $this->assertEquals('fixed_by_cpf', json_decode($data['OrderItem']['calculation_details_log'], true)['type']);
        }
    }

    /**
     * Test Fixed Value by Order calculation
     * Should divide single fixed fee equally among all items
     */
    public function testFixedValueByOrder()
    {
        $supplier = [
            'id' => 1,
            'transfer_fee_type' => 1, // Fixed
            'tipo_cobranca' => 'pedido',
            'transfer_fee_fixed' => 30.00
        ];

        $items = [
            [
                'OrderItem' => [
                    'id' => 1,
                    'customer_user_id' => 100,
                    'subtotal_not_formated' => 100.00,
                    'commission_fee_not_formated' => 5.00
                ]
            ],
            [
                'OrderItem' => [
                    'id' => 2,
                    'customer_user_id' => 200,
                    'subtotal_not_formated' => 150.00,
                    'commission_fee_not_formated' => 7.50
                ]
            ],
            [
                'OrderItem' => [
                    'id' => 3,
                    'customer_user_id' => 300,
                    'subtotal_not_formated' => 200.00,
                    'commission_fee_not_formated' => 10.00
                ]
            ]
        ];

        $savedData = [];
        $this->OrdersController->OrderItem = $this->getMock('OrderItem', ['save']);
        $this->OrdersController->OrderItem->expects($this->exactly(3))
            ->method('save')
            ->will($this->returnCallback(function($data) use (&$savedData) {
                $savedData[] = $data;
                return true;
            }));

        $reflection = new ReflectionClass($this->OrdersController);
        $method = $reflection->getMethod('calculateFixedValueFees');
        $method->setAccessible(true);
        $method->invoke($this->OrdersController, 1, 1, $supplier, $items);

        // Assertions
        $this->assertEquals(3, count($savedData));
        
        // Each item should have $10 transfer fee ($30 / 3 items)
        foreach ($savedData as $data) {
            $this->assertEquals(10.00, $data['OrderItem']['transfer_fee'], 'Each item should have $10 transfer fee');
            $this->assertEquals('volume_tier_fixed', json_decode($data['OrderItem']['calculation_details_log'], true)['type']);
        }
    }

    /**
     * Test Percentage calculation (individual application)
     * Should apply percentage to each item's subtotal individually
     */
    public function testPercentageIndividual()
    {
        $supplier = [
            'id' => 1,
            'transfer_fee_type' => 2, // Percentage
            'tipo_cobranca' => 'pedido',
            'transfer_fee_percentage' => '5.00' // 5%
        ];

        $items = [
            [
                'OrderItem' => [
                    'id' => 1,
                    'customer_user_id' => 100,
                    'subtotal_not_formated' => 100.00, // 5% = $5.00
                    'commission_fee_not_formated' => 5.00
                ]
            ],
            [
                'OrderItem' => [
                    'id' => 2,
                    'customer_user_id' => 200,
                    'subtotal_not_formated' => 150.00, // 5% = $7.50
                    'commission_fee_not_formated' => 7.50
                ]
            ],
            [
                'OrderItem' => [
                    'id' => 3,
                    'customer_user_id' => 300,
                    'subtotal_not_formated' => 200.00, // 5% = $10.00
                    'commission_fee_not_formated' => 10.00
                ]
            ]
        ];

        $expectedFees = [5.00, 7.50, 10.00];
        $savedData = [];
        
        $this->OrdersController->OrderItem = $this->getMock('OrderItem', ['save']);
        $this->OrdersController->OrderItem->expects($this->exactly(3))
            ->method('save')
            ->will($this->returnCallback(function($data) use (&$savedData) {
                $savedData[] = $data;
                return true;
            }));

        $reflection = new ReflectionClass($this->OrdersController);
        $method = $reflection->getMethod('calculateIndividualPercentageFees');
        $method->setAccessible(true);
        $method->invoke($this->OrdersController, 1, 1, $supplier, $items);

        // Assertions
        $this->assertEquals(3, count($savedData));
        
        for ($i = 0; $i < 3; $i++) {
            $this->assertEquals($expectedFees[$i], $savedData[$i]['OrderItem']['transfer_fee'], 
                "Item " . ($i + 1) . " should have transfer fee of $" . $expectedFees[$i]);
            
            $log = json_decode($savedData[$i]['OrderItem']['calculation_details_log'], true);
            $this->assertEquals('individual_percentage', $log['type']);
            $this->assertEquals(5.0, $log['percentage']);
        }
    }

    /**
     * Test Volume Tier calculation (proportional distribution)
     * Should calculate based on tier and distribute proportionally
     */
    public function testVolumeTierProportional()
    {
        $supplier = [
            'id' => 1,
            'transfer_fee_type' => 3, // Volume Tier
            'tipo_cobranca' => 'pedido'
        ];

        $items = [
            [
                'OrderItem' => [
                    'id' => 1,
                    'customer_user_id' => 100,
                    'subtotal_not_formated' => 100.00, // 25% of total
                    'commission_fee_not_formated' => 5.00
                ]
            ],
            [
                'OrderItem' => [
                    'id' => 2,
                    'customer_user_id' => 200,
                    'subtotal_not_formated' => 150.00, // 37.5% of total
                    'commission_fee_not_formated' => 7.50
                ]
            ],
            [
                'OrderItem' => [
                    'id' => 3,
                    'customer_user_id' => 300,
                    'subtotal_not_formated' => 150.00, // 37.5% of total  
                    'commission_fee_not_formated' => 7.50
                ]
            ]
        ];
        // Total: $400

        // Mock RepaymentCalculator to return a known value
        $mockCalculator = $this->getMock('RepaymentCalculator', ['calculateRepayment']);
        $mockCalculator::staticExpects($this->once())
            ->method('calculateRepayment')
            ->with(1, 400, 400) // supplierId, quantity (total amount), subtotal
            ->will($this->returnValue([
                'repayment_value' => 20.00, // $20 total fee
                'repayment_percentage' => 5.0,
                'tier_used' => ['de_qtd' => 1, 'ate_qtd' => 500, 'percentual_repasse_nao_formatado' => 5.0]
            ]));

        $savedData = [];
        $this->OrdersController->OrderItem = $this->getMock('OrderItem', ['save']);
        $this->OrdersController->OrderItem->expects($this->exactly(3))
            ->method('save')
            ->will($this->returnCallback(function($data) use (&$savedData) {
                $savedData[] = $data;
                return true;
            }));

        $reflection = new ReflectionClass($this->OrdersController);
        $method = $reflection->getMethod('calculateAndDistributeVolumeTierFees');
        $method->setAccessible(true);
        $method->invoke($this->OrdersController, 1, 1, $supplier, $items);

        // Expected proportional distribution:
        // Item 1: $100/$400 * $20 = $5.00
        // Item 2: $150/$400 * $20 = $7.50  
        // Item 3: $150/$400 * $20 = $7.50
        $expectedFees = [5.00, 7.50, 7.50];

        // Assertions
        $this->assertEquals(3, count($savedData));
        
        for ($i = 0; $i < 3; $i++) {
            $this->assertEquals($expectedFees[$i], $savedData[$i]['OrderItem']['transfer_fee'], 
                "Item " . ($i + 1) . " should have proportional transfer fee of $" . $expectedFees[$i]);
        }
    }

    /**
     * Test Brazilian number format parsing
     */
    public function testBrazilianNumberParsing()
    {
        $reflection = new ReflectionClass($this->OrdersController);
        $method = $reflection->getMethod('parseFormattedNumber');
        $method->setAccessible(true);

        // Test various Brazilian formats
        $this->assertEquals(1234.56, $method->invoke($this->OrdersController, '1.234,56'));
        $this->assertEquals(123.45, $method->invoke($this->OrdersController, '123,45'));
        $this->assertEquals(1234.0, $method->invoke($this->OrdersController, '1.234'));
        $this->assertEquals(123.0, $method->invoke($this->OrdersController, '123'));
        $this->assertEquals(5.5, $method->invoke($this->OrdersController, '5,5'));
        $this->assertEquals(1000.0, $method->invoke($this->OrdersController, '1.000'));
        
        // Test already numeric values
        $this->assertEquals(123.45, $method->invoke($this->OrdersController, 123.45));
        $this->assertEquals(100.0, $method->invoke($this->OrdersController, 100));
    }

    /**
     * Test edge cases
     */
    public function testEdgeCases()
    {
        // Test with zero values
        $supplier = [
            'id' => 1,
            'transfer_fee_type' => 1,
            'tipo_cobranca' => 'pedido',
            'transfer_fee_fixed' => 0
        ];

        $items = [[
            'OrderItem' => [
                'id' => 1,
                'customer_user_id' => 100,
                'subtotal_not_formated' => 100.00,
                'commission_fee_not_formated' => 5.00
            ]
        ]];

        $this->OrdersController->OrderItem = $this->getMock('OrderItem', ['save']);
        $this->OrdersController->OrderItem->expects($this->never())->method('save');

        $reflection = new ReflectionClass($this->OrdersController);
        $method = $reflection->getMethod('calculateFixedValueFees');
        $method->setAccessible(true);
        $method->invoke($this->OrdersController, 1, 1, $supplier, $items);

        // Should not save anything when fixed value is 0
    }

    /**
     * Test percentage with formatted values from database
     */
    public function testPercentageWithFormattedValues()
    {
        $supplier = [
            'id' => 1,
            'transfer_fee_type' => 2,
            'tipo_cobranca' => 'pedido',
            'transfer_fee_percentage' => '7,50' // Brazilian format: 7.5%
        ];

        $items = [[
            'OrderItem' => [
                'id' => 1,
                'customer_user_id' => 100,
                'subtotal' => '1.000,00', // Brazilian format: 1000.00
                'commission_fee' => '50,00' // Brazilian format: 50.00
            ]
        ]];

        $savedData = [];
        $this->OrdersController->OrderItem = $this->getMock('OrderItem', ['save']);
        $this->OrdersController->OrderItem->expects($this->once())
            ->method('save')
            ->will($this->returnCallback(function($data) use (&$savedData) {
                $savedData[] = $data;
                return true;
            }));

        $reflection = new ReflectionClass($this->OrdersController);
        $method = $reflection->getMethod('calculateIndividualPercentageFees');
        $method->setAccessible(true);
        $method->invoke($this->OrdersController, 1, 1, $supplier, $items);

        // 7.5% of 1000.00 = 75.00
        $this->assertEquals(75.00, $savedData[0]['OrderItem']['transfer_fee']);
        $this->assertEquals(1125.00, $savedData[0]['OrderItem']['total']); // 1000 + 75 + 50
    }
}