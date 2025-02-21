<?php

declare(strict_types=1);

namespace Docker\API\Model;

class TaskSpecResources extends \ArrayObject
{
    /**
     * @var array
     */
    protected $initialized = [];

    public function isInitialized($property): bool
    {
        return \array_key_exists($property, $this->initialized);
    }
    /**
     * An object describing a limit on resources which can be requested by a task.
     *
     * @var Limit|null
     */
    protected $limits;
    /**
     * An object describing the resources which can be advertised by a node and
     * requested by a task.
     *
     * @var ResourceObject|null
     */
    protected $reservations;

    /**
     * An object describing a limit on resources which can be requested by a task.
     */
    public function getLimits(): ?Limit
    {
        return $this->limits;
    }

    /**
     * An object describing a limit on resources which can be requested by a task.
     */
    public function setLimits(?Limit $limits): self
    {
        $this->initialized['limits'] = true;
        $this->limits = $limits;

        return $this;
    }

    /**
     * An object describing the resources which can be advertised by a node and
     * requested by a task.
     */
    public function getReservations(): ?ResourceObject
    {
        return $this->reservations;
    }

    /**
     * An object describing the resources which can be advertised by a node and
     * requested by a task.
     */
    public function setReservations(?ResourceObject $reservations): self
    {
        $this->initialized['reservations'] = true;
        $this->reservations = $reservations;

        return $this;
    }
}
