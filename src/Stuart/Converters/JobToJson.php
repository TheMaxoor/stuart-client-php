<?php

namespace Stuart\Converters;

use Stuart\Job;
use Stuart\Location;

class JobToJson
{
    /**
     * Converts a Job into a Stuart Job as JSON.
     *
     * @param Job $job
     * @return string
     */
    public static function convert($job)
    {
        $result = array(
            'job' => array()
        );

        if (count($job->getPickups()) === 1 && $job->getPickups()[0]->getPickupAt() !== null) {
            $result['job']['pickup_at'] = $job->getPickups()[0]->getPickupAt()->format(JsonToJob::$STUART_DATE_FORMAT);
        }

        if (count($job->getDropOffs()) === 1 && $job->getDropOffs()[0]->getDropOffAt() !== null) {
            $result['job']['dropoff_at'] = $job->getDropOffs()[0]->getDropOffAt()->format(JsonToJob::$STUART_DATE_FORMAT);
        }

        $pickups = array();
        foreach ($job->getPickups() as $pickup) {
            $pickups[] = JobToJson::locationAsArray($pickup);
        }

        $dropOffs = array();
        foreach ($job->getDropOffs() as $dropOff) {
            $dropOffs[] = array_merge(JobToJson::locationAsArray($dropOff), array(
                'package_type' => $dropOff->getPackageType(),
                'package_description' => $dropOff->getPackageDescription(),
                'client_reference' => $dropOff->getClientReference()
            ));
        }

        $result['job']['pickups'] = $pickups;

        $result['job']['dropoffs'] = $dropOffs;

        return json_encode($result);
    }


    /**
     * @param Location $location
     * @return array
     */
    private static function locationAsArray($location)
    {
        return array(
            'address' => $location->getAddress(),
            'comment' => $location->getComment(),
            'contact' => array(
                'firstname' => $location->getFirstName(),
                'lastname' => $location->getLastName(),
                'phone' => $location->getPhone(),
                'company' => $location->getCompany()
            )
        );
    }
}
