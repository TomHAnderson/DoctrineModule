<?php

namespace DoctrineModule\Paginator\Adapter;

use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Laminas\Paginator\Adapter\AdapterInterface;

/**
 * Base module for Doctrine ORM.
 */
class Collection implements AdapterInterface
{
    /**
     * @var DoctrineCollection
     */
    protected $collection;

    /**
     * @param DoctrineCollection $collection
     */
    public function __construct(DoctrineCollection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function getItems($offset, $itemCountPerPage)
    {
        return array_values($this->collection->slice($offset, $itemCountPerPage));
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return count($this->collection);
    }
}
