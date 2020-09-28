<?php

/**
 * Namespace
 * @copyright (c) 2016 | Searchresult Performancemarketing
 */

namespace SdkConverter\Fields;

/**
 * Check if the application has been initialized
 */
if (defined("__SDK_CONVERTER__") == false) exit("Application not initialized");

/**
 * Use classes
 */

use SdkConverter\AbstractClassReader;
use SdkConverter\Converter;

/**
 * Class ClassReader
 * @package SdkConverter\Fields
 * @author  Paradoxis <luke@paradoxis.nl>
 */
class ClassReader extends AbstractClassReader
{
    /**
     * Get the output directory path
     * @return string
     */
    public function getOutputLocation()
    {
        return realpath(__DIR__ . '/../..') . Converter::OUTPUT_DIR_FIELDS;
    }

    /**
     * Get the output directory path
     * @return string
     */
    public function getOutputFileLocation()
    {
        return realpath(__DIR__ . '/../..') . Converter::OUTPUT_DIR_FIELDS . "{$this->getClassName()}.cs";
    }

    /**
     * Get the template file path
     * @return string
     */
    public function getTemplateLocation()
    {
        return __DIR__ . '/../Templates/FacebookAds.Object.Fields.php';
    }

    /**
     * Get all methods as a MethodReader array
     * @return array
     */
    public function getConstants()
    {
        return $constants = $this->reflection->getConstants();
    }
}