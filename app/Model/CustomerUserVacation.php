<?php
class CustomerUserVacation extends AppModel
{
    public $name = 'CustomerUserVacation';
    public $useTable = 'customer_user_vacations';
    public $primaryKey = 'id';

    public $belongsTo = array(
        'CustomerUser' => array(
            'className' => 'CustomerUser',
            'foreignKey' => 'customer_user_id'
        )
    );

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['CustomerUserVacation.data_cancel' => '1901-01-01 00:00:00'];

        return $queryData;
    }

    public function calculateWorkingDays($userId, $period_from, $period_to)
    {
        $period_from_raw = $this->dateFormatBeforeSave($period_from);
        $period_to_raw = $this->dateFormatBeforeSave($period_to);
        $periodInit = date('Y-m-d', strtotime($period_from_raw));
        $periodEnd = date('Y-m-d', strtotime($period_to_raw));

        $query = "SELECT
                    SUM(
                        CASE
                            WHEN DAYOFWEEK(vacation_date) BETWEEN 2 AND 6 THEN 1
                            ELSE 0
                        END
                    ) AS totalWorkingDays
                FROM
                    (SELECT
                        DATE_ADD('".$periodInit."', INTERVAL (a.a + (10 * b.a) + (100 * c.a)) DAY) AS vacation_date
                    FROM
                        (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS a
                        CROSS JOIN
                        (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS b
                        CROSS JOIN
                        (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS c
                    ) AS all_dates
                WHERE
                    vacation_date BETWEEN '".$periodInit."' AND '".$periodEnd."'
                        AND NOT EXISTS( SELECT
                            1
                        FROM
                            customer_user_vacations v
                        WHERE
                            v.customer_user_id = ".$userId."
                                AND v.start_date <= vacation_date
                                AND v.end_date >= vacation_date);
                ";

        $result = $this->query($query);
        return $result[0][0]['totalWorkingDays'];
    }

    public function dateFormatBeforeSave($dateString)
	{
		return date('Y-m-d', strtotime($this->date_converter($dateString)));
	}

	public function date_converter($_date = null)
	{
		$format = '/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/';
		if ($_date != null && preg_match($format, $_date, $partes)) {
			return $partes[3] . '-' . $partes[2] . '-' . $partes[1];
		}

		return false;
	}
}
