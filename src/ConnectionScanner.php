<?php

namespace LJN;

/**
 * @author Linus Norton <linusnorton@gmail.com>
 */
class ConnectionScanner {

    const ORIGIN_INDEX = 0;

    /**
     * Stores the list of connections
     *
     * @var Array
     */
    private $timetable;

    /**
     * @param array $timetable
     */
    public function __construct(array $timetable) {
        $this->timetable = $timetable;
    }

    /**
     * Use the connection scan algorithm to find the fastest path from $origin to
     * $destination
     *
     * @param  string $origin
     * @param  string $destination
     * @param  string $departureTime
     * @return array
     */
    public function getRoute($origin, $destination, $departureTime) {
        $connections = $this->getConnections($origin, $departureTime);

        return $this->getRouteFromConnections($connections, $origin, $destination);
    }

    /**
     * Create a HashMap containing the best connections to each station. At present
     * the fastest connection is considered best.
     *
     * @param  string $startStation
     * @param  int $startTime
     * @return array
     */
    private function getConnections($startStation, $startTime) {
        $arrivals = [$startStation => $startTime];
        $connections = [];

        foreach ($this->timetable as $connection) {
            list($origin, $destination, $departureTime, $arrivalTime) = $connection;

            $canGetToThisConnection = array_key_exists($origin, $arrivals) && $departureTime >= $arrivals[$origin];
            $thisConnectionIsBetter = !array_key_exists($destination, $arrivals) || $arrivals[$destination] > $arrivalTime;

            if ($canGetToThisConnection && $thisConnectionIsBetter) {
                $arrivals[$destination] = $arrivalTime;
                $connections[$destination] = $connection;
            }
        }

        return $connections;
    }

    /**
     * Given a Hash Map of fastest connections trace back the route from the target
     * destination to the origin. If no route is found an empty array is returned
     *
     * @param  array  $connections
     * @param  strubg $origin
     * @param  string $destination
     * @return array
     */
    private function getRouteFromConnections(array $connections, $origin, $destination) {
        $route = [];

        while (array_key_exists($destination, $connections)) {
            $route[] = $connections[$destination];
            $destination = $connections[$destination][self::ORIGIN_INDEX];
        }

        // if we found a route back to the origin
        if ($origin === $destination) {
            return array_reverse($route);
        }
        else {
            return [];
        }

    }
}
