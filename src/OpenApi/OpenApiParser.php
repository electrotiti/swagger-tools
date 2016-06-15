<?php

namespace Electrotiti\OpenApi;

use Symfony\Component\Yaml\Yaml;

/**
 * Class OpenApiParser
 * This class extract a full API definition from a Yaml file and his children and does the reverse
 * @package Electrotiti\OpenApi
 */
class OpenApiParser
{
    private $basePath = null;

    /**
     * OpenApiParser constructor.
     */
    public function __construct()
    {
    }

    /**
     * Parses OpenApi YAML into a PHP array and resolve children references
     * 
     * @param string $input Yaml plain text
     * @param string $basePath BasePath to resolve children references
     * @return array
     */
    public function parse($input, $basePath = null)
    {
        $this->basePath = $basePath;
        if (null === $basePath) {
            $this->basePath = __DIR__;
        }

        $output = Yaml::parse($input, true);
        return $output;
    }

    /**
     * Parses OpenApi YAML using a file
     * @param string $filePath
     * @return array
     */
    public function parseFromFile($filePath)
    {
        $basePath = dirname($filePath);

        $input = file_get_contents($filePath);
        
        return $this->parse($input, $basePath);
    }
}
