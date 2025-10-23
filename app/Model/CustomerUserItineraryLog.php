<?php

class CustomerUserItineraryLog extends AppModel
{
    public $name = 'CustomerUserItineraryLog';
    public $useTable = 'customer_user_itinerary_logs';
    public $primaryKey = 'id';

    public $belongsTo = array(
        'CustomerUser' => [
            'className' => 'CustomerUser',
            'foreignKey' => 'customer_user_id'
        ],
        'Benefit' => [
            'className' => 'Benefit',
            'foreignKey' => 'benefit_id'
        ],
        'CustomerUserItinerary' => [
            'className' => 'CustomerUserItinerary',
            'foreignKey' => 'customer_user_itinerary_id'
        ],
        'User' => [
            'className' => 'User',
            'foreignKey' => 'user_id'
        ],
    );

    /**
     * Log an action on a customer user itinerary
     *
     * @param int $customerUserId
     * @param int $benefitId
     * @param int|null $customerUserItineraryId
     * @param string $action (activate, inactivate, delete)
     * @param array $user Current logged in user
     * @param array|null $beforeData Data before the action
     * @param array|null $afterData Data after the action
     * @return bool
     */
    public function logAction($customerUserId, $benefitId, $customerUserItineraryId, $action, $user, $beforeData = null, $afterData = null)
    {
        $logData = [
            'CustomerUserItineraryLog' => [
                'customer_user_id' => $customerUserId,
                'benefit_id' => $benefitId,
                'customer_user_itinerary_id' => $customerUserItineraryId,
                'action' => $action,
                'user_id' => $user['id'],
                'user_name' => $user['name'],
                'before_data' => $beforeData ? json_encode($beforeData) : null,
                'after_data' => $afterData ? json_encode($afterData) : null,
            ]
        ];

        $this->create();
        return $this->save($logData);
    }
}
