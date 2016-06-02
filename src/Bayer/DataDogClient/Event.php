<?php

namespace Bayer\DataDogClient;

use Bayer\DataDogClient\Event\InvalidAlertTypeException;
use Bayer\DataDogClient\Event\InvalidSourceTypeException;
use Bayer\DataDogClient\Event\InvalidPriorityException;

/**
 * Class Event
 *
 * An event is shown in the datadog timeline and consists of
 * at least an event text.
 *
 * @package Bayer\DataDogClient
 */
class Event extends AbstractDataObject {

    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_LOW    = 'low';

    const TYPE_INFO    = 'info';
    const TYPE_WARNING = 'warning';
    const TYPE_ERROR   = 'error';
    const TYPE_SUCCESS = 'success';

    const SOURCE_NAGIOS     = 'nagios';
    const SOURCE_HUDSON     = 'hudson';
    const SOURCE_JENKINS    = 'jenkins';
    const SOURCE_USER       = 'user';
    const SOURCE_MYAPPS     = 'my apps';
    const SOURCE_FEED       = 'feed';
    const SOURCE_CHEF       = 'chef';
    const SOURCE_PUPPET     = 'puppet';
    const SOURCE_GIT        = 'git';
    const SOURCE_BITBUCKET  = 'gitbucket';
    const SOURCE_FABRIC     = 'fabric';
    const SOURCE_CAPISTRANO = 'capistrano';

    /**
     * Title of the event
     *
     * @var string
     */
    protected $title;

    /**
     * The event message
     *
     * @var string
     */
    protected $text;

    /**
     * Timestamp when the event occured
     *
     * @var int
     */
    protected $dateHappened;

    /**
     * Event priority
     *
     * Datadog supports low and normal
     *
     * @var string
     */
    protected $priority;

    /**
     * Event alert type
     *
     * Datadog supports info, warning, error and success
     *
     * @var string
     */
    protected $alertType;

    /**
     * Arbitary string used to group events
     *
     * @var string
     */
    protected $aggregationKey;

    /**
     * Type of the event source
     *
     * This indicated which source fired the event.
     * Datadog supports:
     * nagios, hudson, jenkins, user, my apps, feed,
     * chef, puppet, git, bitbucket, fabric, capistrano
     *
     * @var string
     */
    protected $sourceTypeName;

    /**
     * @param string $text
     * @param string $title
     */
    public function __construct($text, $title = '') {
        $this->setText($text)
            ->setTitle($title)
            ->setDateHappened(time())
            ->setPriority(self::PRIORITY_NORMAL)
            ->setAlertType(self::TYPE_INFO);
    }

    /**
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Event
     */
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getText() {
        return $this->text;
    }

    /**
     * @param string $text
     *
     * @return Event
     */
    public function setText($text) {
        $this->text = $text;

        return $this;
    }

    /**
     * @return int
     */
    public function getDateHappened() {
        return $this->dateHappened;
    }

    /**
     * @param int $timestamp
     *
     * @return Event
     */
    public function setDateHappened($timestamp) {
        $this->dateHappened = $timestamp;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPriority() {
        return $this->priority;
    }

    /**
     * @param mixed $priority
     * @throws Event\InvalidPriorityException
     *
     * @return Event
     */
    public function setPriority($priority) {
        if (!$this->isValidPriority($priority)) {
            throw new InvalidPriorityException('Priority must be on of Event::PRIORITY_*');
        }
        $this->priority = $priority;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAlertType() {
        return $this->alertType;
    }

    /**
     * @param mixed $type
     * @throws InvalidAlertTypeException
     *
     * @return Event
     */
    public function setAlertType($type) {
        if (!$this->isValidType($type)) {
            throw new InvalidAlertTypeException('Type must be one of Event::TYPE_*');
        }
        $this->alertType = $type;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAggregationKey() {
        return $this->aggregationKey;
    }

    /**
     * @param mixed $aggregationKey
     *
     * @return Event
     */
    public function setAggregationKey($aggregationKey) {
        $this->aggregationKey = $aggregationKey;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSourceTypeName() {
        return $this->sourceTypeName;
    }

    /**
     * @param mixed $sourceType
     * @throws Event\InvalidSourceTypeException
     *
     * @return Event
     */
    public function setSourceTypeName($sourceType) {
        if (!$this->isValidSourceType($sourceType)) {
            throw new InvalidSourceTypeException('SourceTyoe must be on of Event::SOURCE_*');
        }
        $this->sourceTypeName = $sourceType;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray() {
        $data = array(
            'title'         => $this->getTitle(),
            'text'          => $this->getText(),
            'date_happened' => $this->getDateHappened(),
            'priority'      => $this->getPriority(),
            'alert_type'    => $this->getAlertType(),
        );

        if ($tags = $this->getTags()) {
            $data['tags'] = array();
            foreach ($tags as $tag => $value) {
                $data['tags'][] = is_string($tag) ? "$tag:$value" : $value;
            }
        }

        if ($this->getAggregationKey()) {
            $data['aggregation_key'] = $this->getAggregationKey();
        }

        if ($this->getSourceTypeName()) {
            $data['source_type_name'] = $this->getSourceTypeName();
        }

        return $data;
    }

    protected function isValidType($type) {
        return in_array(
            $type,
            array(
                self::TYPE_ERROR,
                self::TYPE_INFO,
                self::TYPE_SUCCESS,
                self::TYPE_WARNING,
            )
        );
    }

    protected function isValidSourceType($sourceType) {
        return in_array(
            $sourceType,
            array(
                self::SOURCE_NAGIOS,
                self::SOURCE_HUDSON,
                self::SOURCE_JENKINS,
                self::SOURCE_USER,
                self::SOURCE_MYAPPS,
                self::SOURCE_FEED,
                self::SOURCE_CHEF,
                self::SOURCE_PUPPET,
                self::SOURCE_GIT,
                self::SOURCE_BITBUCKET,
                self::SOURCE_FABRIC,
                self::SOURCE_CAPISTRANO,
            )
        );
    }

    protected function isValidPriority($priority) {
        return in_array(
            $priority,
            array(
                self::PRIORITY_NORMAL,
                self::PRIORITY_LOW,
            )
        );
    }

}
