<?php

/**
 * Class HpsPayPlanScheduleService
 */
class HpsPayPlanScheduleService extends HpsRestGatewayService
{
    /**
     * @param \HpsPayPlanSchedule $schedule
     *
     * @return mixed
     */
    public function add(HpsPayPlanSchedule $schedule)
    {
        $data = $schedule->getEditableFieldsWithValues();
        $data['customerKey'] = $schedule->customerKey;
        $data['numberOfPayments'] = $schedule->numberOfPayments;
        $result = $this->doRequest($data, array(
            'verb'     => 'POST',
            'endpoint' => 'schedules',
        ));
        return $this->hydrateObject($result, 'HpsPayPlanSchedule');
    }
    /**
     * @param \HpsPayPlanSchedule $schedule
     *
     * @return mixed
     */
    public function edit(HpsPayPlanSchedule $schedule)
    {
        $data = $schedule->getEditableFieldsWithValues( $schedule );
        $result = $this->doRequest($data, array(
            'verb'     => 'PUT',
            'endpoint' => 'schedules/'.$schedule->scheduleKey,
        ));
        return $this->hydrateObject($result, 'HpsPayPlanSchedule');
    }
    /**
     * @param array $searchFields
     *
     * @return object
     */
    public function findAll($searchFields = array())
    {
        // Cannot have an array as the root object
        // in a JSON document
        $data = $searchFields === array() ? (object)array() : $searchFields;
        $results = $this
            ->doRequest($data, array(
                'verb'     => 'POST',
                'endpoint' => 'searchSchedules',
            ));
        return $this->hydrateSearchResults($results, 'HpsPayPlanSchedule');
    }
    /**
     * @param $schedule
     *
     * @return mixed
     */
    public function get($schedule)
    {
        $id = null;
        if ($schedule instanceof HpsPayPlanSchedule) {
            $id = $schedule->scheduleKey;
        } else {
            $id = $schedule;
        }
        $result = $this->doRequest(null, array(
            'verb'     => 'GET',
            'endpoint' => 'schedules/'.$id,
        ));
        return $this->hydrateObject($result, 'HpsPayPlanSchedule');
    }
    /**
     * @param      $schedule
     * @param bool $forceDelete
     *
     * @return mixed
     */
    public function delete($schedule, $forceDelete = false)
    {
        $id = null;
        if ($schedule instanceof HpsPayPlanSchedule) {
            $id = $schedule->scheduleKey;
        } else {
            $id = $schedule;
        }
        $data = array(
            'forceDelete' => $forceDelete,
        );
        return $this->doRequest($data, array(
            'verb'     => 'DELETE',
            'endpoint' => 'schedules/'.$id,
        ));
    }
}