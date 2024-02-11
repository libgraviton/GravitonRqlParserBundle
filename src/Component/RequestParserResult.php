<?php

namespace Graviton\RqlParserBundle\Component;

class RequestParserResult
{

    private bool $hasRql;
    private string $rawRql;
    private $rqlQuery;

    public function __construct(bool $hasRql, string $rawRql, $rqlQuery)
    {
        $this->hasRql = $hasRql;
        $this->rawRql = $rawRql;
        $this->rqlQuery = $rqlQuery;
    }

    public function isHasRql(): bool
    {
        return $this->hasRql;
    }

    public function getRawRql(): string
    {
        return $this->rawRql;
    }

    /**
     * @return mixed
     */
    public function getRqlQuery()
    {
        return $this->rqlQuery;
    }
}
