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
            $customerUserId = $pieces[1];
            $supplierId = $pieces[2];

            $this->PaymentImportLog->id = $paymentLog['PaymentImportLog']['id'];

            $payment = [
                'PaymentImportLog' => [
                    'id' => $paymentLog['PaymentImportLog']['id'],
                    'order_id' => $orderId,
                    'customer_user_id' => $customerUserId,
                    'supplier_id' => $supplierId,
                    'processed' => true,
                ] 
            ];
            if ($this->PaymentImportLog->save($payment)) {
                $this->out('Processed payment ID: ' . $payment['PaymentImportLog']['id']);
            } else {
                $this->err('Failed to process payment.');
            }
        }

        $this->out('Processing completed.');
    }
}