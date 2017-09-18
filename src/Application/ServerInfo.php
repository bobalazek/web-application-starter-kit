<?php

namespace Application;

use Silex\Application;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class ServerInfo
{
    /** @var Application */
    protected $app;

    /** @var array */
    protected $data;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @return array
     */
    public function calculateData()
    {
        $data = [];

        // Uptime
        $uptimeFile = '/proc/uptime';
        $uptimeSeconds = 0;

        if (file_exists($uptimeFile)) {
            $fh = fopen($uptimeFile, 'r');

            $uptimeSeconds = fgets($fh);

            fclose($fh);

            $uptimeSeconds = current(explode('.', $uptimeSeconds));
        }

        $data['uptimeSeconds'] = $uptimeSeconds;

        // Uptime
        $time = $uptimeSeconds;
        $days = floor($time / (60 * 60 * 24));
        $time -= $days * (60 * 60 * 24);
        $hours = floor($time / (60 * 60));
        $time -= $hours * (60 * 60);
        $minutes = floor($time / 60);
        $time -= $minutes * 60;
        $seconds = floor($time);
        $time -= $seconds;

        $data['uptime'] = "{$days}d {$hours}h {$minutes}m {$seconds}s";

        // Memory
        $memoryTotal = ((int) ini_get('memory_limit')) * 1024 * 1024;
        $memoryUsed = memory_get_usage();
        $memoryFree = $memoryTotal - $memoryUsed;
        $memoryUsedPercentage = $memoryUsed / $memoryTotal * 100;

        $data['memoryTotal'] = $memoryTotal;
        $data['memoryFree'] = $memoryFree;
        $data['memoryUsed'] = $memoryUsed;
        $data['memoryUsedPercentage'] = $memoryUsedPercentage;

        // HDD
        $hddTotal = disk_total_space('/');
        $hddFree = disk_free_space('/');
        $hddUsed = $hddTotal - $hddFree;
        $hddUsedPercentage = $hddUsed / $hddTotal * 100;

        $data['hddTotal'] = $hddTotal;
        $data['hddFree'] = $hddFree;
        $data['hddUsed'] = $hddUsed;
        $data['hddUsedPercentage'] = $hddUsedPercentage;

        // Load
        $loadPercentage = sys_getloadavg();
        $loadPercentage = $loadPercentage[0];

        $data['loadPercentage'] = $loadPercentage;

        return $this->setData($data);
    }

    /**
     * @return array
     */
    public function getData()
    {
        $this->calculateData();

        return $this->data;
    }

    /**
     * @param $data
     *
     * @return array
     */
    public function setData($data)
    {
        $this->data = $data;
    }
}
