<?php

class HpsPayPlanScheduleService extends HpsRestGatewayService
{
    public function add(HpsPayPlanSchedule $schedule)
    {
        $data = $schedule->getEditableFieldsWithValues();
        $data['customerKey'] = $schedule->customerKey;
        $data['numberOfPayments'] = $schedule->numberOfPayments;
        $result = $this->doRequest('POST', 'schedules', $data);
        return $this->hydrateObject($result, 'HpsPayPlanSchedule');
    }

    public function edit(HpsPayPlanSchedule $schedule)
    {
        $data = $schedule->getEditableFieldsWithValues();
        $result = $this->doRequest('PUT', 'schedules/'.$schedule->scheduleKey, $data);
        return $this->hydrateObject($result, 'HpsPayPlanSchedule');
    }

    public function findAll($searchFields = array())
    {
        // Cannot have an array as the root object
        // in a JSON document
        $data = $searchFields === array() ? (object)array() : $searchFields;
        $results = $this
            ->doRequest('POST', 'searchSchedules', $data);

        return $this->hydrateSearchResults($results, 'HpsPayPlanSchedule');
    }

    public function get($schedule)
    {
        $id = null;
        if ($schedule instanceof HpsPayPlanSchedule) {
            $id = $schedule->scheduleKey;
        } else {
            $id = $schedule;
        }
        $result = $this->doRequest('GET', 'schedules/'.$id);
        return $this->hydrateObject($result, 'HpsPayPlanSchedule');
    }

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
        return $this->doRequest('DELETE', 'schedules/'.$id, $data);
    }
}
