<?php

namespace Bayer\DataDogClient;

use Bayer\DataDogClient\Factory\InvalidPropertyException;
use Bayer\DataDogClient\Series\Metric;

/**
 * Class Factory
 *
 * Simple factory to build metric or event objects dynamically
 *
 * @package Bayer\DataDogClient
 */
class Factory {
    /**
     * @param string $name
     * @param array  $points
     * @param array  $options
     *
     * @return Metric
     */
    public static function buildMetric($name, array $points, array $options) {
        $metric = new  Metric($name, $points);

        foreach ($options as $property => $value) {
            self::setProperty($metric, $property, $value);
        }

        return $metric;
    }

    /**
     * @param string $text
     * @param string $title
     * @param array  $options
     *
     * @return Event
     */
    public static function buildEvent($text, $title = '', array $options) {
        $event = new Event($text, $title);

        foreach ($options as $property => $value) {
            self::setProperty($event, $property, $value);
        }

        return $event;
    }

    /**
     * @param $object
     * @param $property
     * @param $value
     * @throws InvalidPropertyException
     */
    protected static function setProperty($object, $property, $value) {
        $method = 'set' . ucfirst($property);
        if (!method_exists($object, $method)) {
            throw new InvalidPropertyException('Unable to call ' . get_class(
                $object
            ) . '::' . $method . '(' . $value . ')');
        }
        $object->$method($value);
    }
}