<?php

namespace App\Services\AdminDashboardOverview;

use Carbon\Carbon;

class BaseOverview
{
    /**
     * Return an array of dates between given from_date and to_date
     *
     * @param  string  $from_date
     * @param  string  $to_date
     * @return array
     */
    public function getDays($from_date, $to_date)
    {
        $from_date = Carbon::parse($from_date);
        $to_date = Carbon::parse($to_date);
        $days = [];
        while ($from_date <= $to_date) {
            $days[] = $from_date->format('Y-m-d');
            $from_date = $from_date->addDay();
        }

        return $days;
    }

    /**
     * Returns an array of months as strings (e.g. January, February, ...).
     *
     * @return string[]
     */
    public function getMonths()
    {
        return ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    }

    /**
     * Returns an array of the last 5 years including the current year.
     *
     * @return int[]
     */
    public function getYears()
    {
        $years = [];
        for ($i = Carbon::now()->subtract('years', 5)->year; $i <= date('Y'); $i++) {
            $years[] = $i;
        }

        return $years;
    }

    /**
     * Given an overview_by (month or year) and a value and an array of data,
     * return the index of the value in the array.
     *
     * This is used to position the value in the array correctly when fetching data.
     *
     * @param  string  $overview_by
     * @param  string|int  $value
     * @param  array  $data
     * @return int
     */
    public function getArrayIndexByOverviewBy($overview_by, $value, $data)
    {
        if ($overview_by == 'month') {
            return $value - 1;
        } elseif ($overview_by == 'year') {
            return array_search($value, $data);
        } else {
            return array_search($value, $data);
        }
    }
}
