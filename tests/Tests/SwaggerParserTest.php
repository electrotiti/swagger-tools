<?php

namespace Tests;

use Electrotiti\SwaggerTools\SwaggerParser;
use Symfony\Component\Yaml\Yaml;

class SwaggerParserTest extends \PHPUnit_Framework_TestCase
{

    public function testParse()
    {
        $rawYaml = <<<'EOF'
swagger: '2.0'
info:
  version: "0.0.0"
  title: "Test of definition"
paths:
  /users:
    get:
      description: "Get the list of users"
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
            title: ArrayOfUsers
            type: array
            items:
              title: User
              type: object
              properties:
                name:
                  type: string
                single:
                  type: boolean

EOF;

        $expected = [
            'swagger' => '2.0',
            'info' =>
                [
                    'version' => '0.0.0',
                    'title' => 'Test of definition'
                ],
            'paths' =>
                [
                    '/users' =>
                        [
                            'get' =>
                                [
                                    'description' => "Get the list of users",
                                    'parameters' =>
                                        [
                                            [
                                                'name' => 'size',
                                                'in' => 'query',
                                                'description' => 'Size of array',
                                                'required' => true,
                                                'type' => 'number',
                                                'format' => 'double'
                                            ]
                                        ],
                                    'responses' =>
                                        [
                                            '200' =>
                                                [
                                                    'description' => 'Successful response',
                                                    'schema' =>
                                                        [
                                                            'title' => 'ArrayOfUsers',
                                                            'type' => 'array',
                                                            'items' =>
                                                                [
                                                                    'title' => 'User',
                                                                    'type' => 'object',
                                                                    'properties' =>
                                                                        [
                                                                            'name' =>
                                                                                ['type' => 'string'],
                                                                            'single' =>
                                                                                ['type' => 'boolean']

                                                                        ]
                                                                ]
                                                        ]
                                                ]
                                        ]
                                ]
                        ]
                ]
        ];

        $parser = new SwaggerParser();
        $actual = $parser->parse($rawYaml);        
        $this->assertEquals($expected, $actual);
    }
    
    public function testParseFromFile()
    {
        $fileName = __DIR__.'/../Sample/api.yml';
        $parser = new SwaggerParser();
        $parser->setResolveLocalReference(false);
        $actual = $parser->parseFromFile($fileName);
        $rawData = Yaml::dump($actual, 10);
        file_put_contents(__DIR__.'/../dump.yml', $rawData);
    }

}
