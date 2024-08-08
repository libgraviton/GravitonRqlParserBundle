<?php

namespace Graviton\RqlParserBundle\Component;

use Graviton\RqlParser\Lexer;
use Graviton\RqlParser\Parser;
use Symfony\Component\HttpFoundation\Request;

class RequestParser
{

    private Lexer $lexer;
    private Parser $parser;
    private string $headerName;

    public function __construct(Lexer $lexer, Parser $parser, string $headerName)
    {
        $this->lexer = $lexer;
        $this->parser = $parser;
        $this->headerName = $headerName;
    }

    public function parse(Request $request) : RequestParserResult
    {
        // grab rql query either from header or query string
        $filter = $request->headers->get(
            $this->headerName,
            $request->server->get('QUERY_STRING', '')
        );

        $hasRql = !empty($filter);

        if (!$hasRql) {
            return new RequestParserResult(false, '', null);
        }

        return new RequestParserResult(
            true,
            $filter,
            $this->parser->parse($this->lexer->tokenize($filter))
        );
    }
}
