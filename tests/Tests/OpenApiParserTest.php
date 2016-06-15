<?php

namespace Tests;

class OpenApiParserTest extends \PHPUnit_Framework_TestCase
{

    public function testParse()
    {
        $rawYaml = <<<'EOF'
swagger: '2.0'
info:
  version: "0.0.0"
  title: <enter your title>
paths:
  /persons:
    get:
      description: |
        Gets `Person` objects.
        Optional query param of **size** determines
        size of returned array
      parameters:
        -
          name: size
          in: query
          description: Size of array
          required: true
          type: number
          format: double
      responses:
        200:
          description: Successful response
          schema:
            title: ArrayOfPersons
            type: array
            items:
              title: Person
              type: object
              properties:
                name:
                  type: string
                single:
                  type: boolean

EOF;


        $parser = new \Electrotiti\OpenApi\OpenApiParser();
        $expected = $parser->parse($rawYaml);

        var_dump($expected);
    }

}
