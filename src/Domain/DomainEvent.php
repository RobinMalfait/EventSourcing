<?php namespace EventSourcing\Domain;

interface DomainEvent
{
    public function getAggregateId();
}
