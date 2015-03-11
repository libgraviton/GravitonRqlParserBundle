<?php

namespace Graviton\RqlParserBundle\Tests\Fixtures;

use Graviton\Rql\AST\OperationInterface;
use Graviton\Rql\Visitor\VisitorInterface;

class NoopVisitor implements VisitorInterface
{
    public function visit(OperationInterface $operation)
    {
        return;
    }
}
