<?php

namespace CustoMood\Bundle\AppBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

class MoodParameters
{
    const AGGREGATE_PERIODS_IN_DAYS = [
        1 => 'Day',
        7 => 'Week',
        31 => 'Month',
        93 => '3 Months',
        186 => '6 Months',
        365 => 'Year'
    ];

    /**
     * @var \DateTime
     * @Assert\Date()
     */
    protected $moodFrom;

    /**
     * @var \DateTime
     * @Assert\Date()
     */
    protected $moodTo;

    /**
     * @var int
     */
    protected $aggregatePeriod;

    /**
     * MoodParameters constructor.
     */
    public function __construct()
    {
        $this->aggregatePeriod = array_keys(self::AGGREGATE_PERIODS_IN_DAYS)[0];
        $this->moodFrom = new \DateTime('first day of previous month', new \DateTimeZone('UTC'));
        $this->moodTo = new \DateTime('now', new \DateTimeZone('UTC'));
    }

    /**
     * @param \DateTime $moodFrom
     * @return MoodParameters
     */
    public function setMoodFrom(\DateTime $moodFrom): MoodParameters
    {
        $this->moodFrom = $moodFrom;
        return $this;
    }

    /**
     * @param \DateTime $moodTo
     * @return MoodParameters
     */
    public function setMoodTo(\DateTime $moodTo): MoodParameters
    {
        $this->moodTo = $moodTo;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getMoodFrom(): \DateTime
    {
        return $this->moodFrom;
    }

    /**
     * @return \DateTime
     */
    public function getMoodTo(): \DateTime
    {
        return $this->moodTo;
    }

    /**
     * @param int $aggregatePeriod
     * @return MoodParameters
     */
    public function setAggregatePeriod(int $aggregatePeriod): MoodParameters
    {
        $this->aggregatePeriod = $aggregatePeriod;
        return $this;
    }

    /**
     * @return int
     */
    public function getAggregatePeriod(): int
    {
        return $this->aggregatePeriod;
    }

}