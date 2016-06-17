<?php

namespace Electrotiti\SwaggerTools;

use Electrotiti\OpenApi\Exceptions\ParsingException;
use Symfony\Component\Yaml\Yaml;

/**
 * Class OpenApiParser
 * This class extract a full API definition from a Yaml file and his children and does the reverse
 * @package Electrotiti\OpenApi
 */
class SwaggerParser
{
    /**
     * @var string
     */
    private $basePath = null;

    /**
     * @var array
     */
    private $nameSpaces = [];

    /**
     * @var bool
     */
    private $resolveLocalReference = false;

    /**
     * OpenApiParser constructor.
     */
    public function __construct()
    {
    }

    /**
     * Set list of NameSpaces to resolve children reference
     * @param array $nameSpaces Associative array of namespace
     */
    public function setNameSpaces($nameSpaces)
    {
        $this->nameSpaces = $nameSpaces;
    }

    /**
     * @param boolean $resolveLocalReference
     */
    public function setResolveLocalReference($resolveLocalReference)
    {
        $this->resolveLocalReference = $resolveLocalReference;
    }

    /**
     * @param $basePath
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
        if (null === $basePath) {
            $this->basePath = __DIR__ . '/';
        }

        if ('/' !== substr($this->basePath, -1, 1)) {
            $this->basePath .= '/';
        }
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
        $this->setBasePath($basePath);
        $output = Yaml::parse($input, true);

        foreach ($output as $key => $sub) {
            if (is_array($sub)) {
                $output[$key] = $this->subParse($sub);
            } else {
                $output[$key] = $sub;
            }
        }

        return $output;
    }

    /**
     * Parses OpenApi YAML using a file
     * @param string $filePath
     * @return array
     */
    public function parseFromFile($filePath)
    {
        // Returns directory name component of file
        $basePath = dirname($filePath);

        $input = file_get_contents($filePath);
        return $this->parse($input, $basePath);
    }

    /**
     * @param $subPart
     */
    public function subParse($subPart)
    {
        foreach ($subPart as $key => $sub) {
            if ('$ref' === $key) {
                $subSubPart = $this->resolvePath($sub);
                if (is_array($subSubPart)) {
                    $output[$key] = array_merge($sub, $subSubPart);
                }
            } elseif (is_array($sub)) {
                $subSubPart = $this->subParse($sub);
                $output[$key] = $subSubPart;
            } else {
                $output[$key] = $sub;
            }
        }
        
        return $output;
    }

    public function resolveExternalReference($reference)
    {
        if (false === is_string($reference)) {
            throw new ParsingException('External reference $ref should be a string');
        }

        if ('@' === substr($reference, 0, 1)) {
            $filePath = $this->resolvePathWithNameSpace($reference);
        } elseif ('./' === substr($reference, 0, 2)) {
            $reference = substr($reference, 2);
            $filePath = $this->basePath . $reference;
        } elseif ('#' === substr($reference, 0, 1)) {
            if ($this->resolveLocalReference){
                // TODO Parse local ref definitions before other parts...
            } else {
                return $reference;
            }
        }

        if (false === file_exists($filePath)) {
            throw new ParsingException('File ' . $filePath . ' does not exist');
        }

        $rawContent = file_get_contents($filePath);
        return Yaml::parse($rawContent);
    }

    public function resolvePathWithNameSpace($filePath)
    {
        //TODO Resolve path with namespace
        return $filePath;
    }

}
