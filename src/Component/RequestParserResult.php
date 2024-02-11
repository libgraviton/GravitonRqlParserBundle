<?php

namespace Graviton\RqlParserBundle\Component;

use Graviton\RqlParser\Query;

class RequestParserResult
{

    private bool $hasRql;
    private string $rawRql;
    private ?Query $rqlQuery;

    public function __construct(bool $hasRql, string $rawRql, ?Query $rqlQuery)
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
    public function getRqlQuery() : ?Query
    {
        return $this->rqlQuery;
    }
}
