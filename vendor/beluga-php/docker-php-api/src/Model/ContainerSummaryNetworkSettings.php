<?php

declare(strict_types=1);

namespace Docker\API\Model;

class ContainerSummaryNetworkSettings extends \ArrayObject
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
     * @var array<string, EndpointSettings>|null
     */
    protected $networks;

    /**
     * @return array<string, EndpointSettings>|null
     */
    public function getNetworks(): ?iterable
    {
        return $this->networks;
    }

    /**
     * @param array<string, EndpointSettings>|null $networks
     */
    public function setNetworks(?iterable $networks): self
    {
        $this->initialized['networks'] = true;
        $this->networks = $networks;

        return $this;
    }
}
