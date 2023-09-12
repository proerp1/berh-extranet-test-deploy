<?php
// app/Console/Command/ProcessNewPaymentShell.php
App::uses('AppShell', 'Console/Command');

class ProcessNewPaymentShell extends AppShell {

    public $uses = array('PaymentImportLog');

    public function main() {
        $this->out('Processing new payments...');

        // Fetch unprocessed payments from PaymentImportLog
        $unprocessedPayments = $this->PaymentImportLog->find('all', array(
            'conditions' => array('processed' => false)
        ));

        if (empty($unprocessedPayments)) {
            $this->out('No new payments to process.');
            return;
        }

        foreach ($unprocessedPayments as $paymentLog) {
            // Access the JSON data and process it
            $orderData = json_decode($paymentLog['PaymentImportLog']['order_data'], true);
            $orderCode = $orderData['pedido']['id'];
            $orderCode = str_replace('5803-', '', $orderCode);
            $pieces = explode('-', $orderCode);
            $orderId = $pieces[0];
            $customerId = $pieces[1];
            $supplierId = $pieces[2];

            // TODO - Add your payment processing logic here...

            // Your payment processing logic here...
            // Example: Save the payment data to another table
            $payment = $orderData;
            if ($this->PaymentImportLog->save($payment)) {
                // Mark the payment log as processed
                $this->PaymentImportLog->id = $paymentLog['PaymentImportLog']['id'];
                $this->PaymentImportLog->saveField('processed', true);
                $this->out('Processed payment ID: ' . $payment['id']);
            } else {
                $this->err('Failed to process payment.');
            }
        }

        $this->out('Processing completed.');
    }
}