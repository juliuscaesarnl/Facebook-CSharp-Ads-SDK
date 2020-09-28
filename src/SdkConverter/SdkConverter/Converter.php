<?php

/**
 * Namespace
 * @copyright (c) 2016 | Searchresult Performancemarketing
 */

namespace SdkConverter;

/**
 * Check if the application has been initialized
 */
if (defined("__SDK_CONVERTER__") == false) exit("Application not initialized");

/**
 * Use classes
 */

use Exception;
use SdkConverter\Fields\ClassReader as FieldsClassReader;
use SdkConverter\Object\ClassReader as ObjectClassReader;
use SdkConverter\Values\ClassReader as ValuesClassReader;

/**
 * Class Converter
 * @package SdkConverter
 * @author  Paradoxis <luke@paradoxis.nl>
 */
class Converter
{
    /**
     * The class directories we want to read from
     * @var string
     */
    const INPUT_DIR_OBJECT = "/../vendor/facebook/php-business-sdk/src/FacebookAds/Object/";
    const INPUT_DIR_FIELDS = "/../vendor/facebook/php-business-sdk/src/FacebookAds/Object/Fields/";
    const INPUT_DIR_VALUES = "/../vendor/facebook/php-business-sdk/src/FacebookAds/Object/Values/";

    /**
     * Output directories where we want our classes to go
     * @var string
     */
    const OUTPUT_DIR_OBJECT = "/Output/";
    const OUTPUT_DIR_FIELDS = "/Output/Fields/";
    const OUTPUT_DIR_VALUES = "/Output/Values/";

    /**
     * Class namespaces
     * @var string
     */
    const CLASS_NAMESPACE_OBJECT = "\\FacebookAds\\Object\\";
    const CLASS_NAMESPACE_FIELDS = "\\FacebookAds\\Object\\Fields\\";
    const CLASS_NAMESPACE_VALUES = "\\FacebookAds\\Object\\Values\\";

    /**
     * Every available file in the directory
     * @var \SdkConverter\AbstractClassReader[]
     */
    public $classes = [];

    /**
     * Blacklist of files we DON'T want to convert
     * These are helper classes or interfaces which don't serve as API objects
     * @var array
     */
    private $blacklist = [
        "AbstractArchivableCrudObjectFields.php",
        "AbstractArchivableCrudObject.php",
        "AbstractAsyncJobObject.php",
        "AbstractCrudObject.php",
        "AbstractObject.php",
        "CanRedownloadInterface.php"
    ];

    /**
     * Class constructor
     * Loads all files into the $file array
     * @throws Exception
     */
    public function __construct()
    {
        $this->loadClasses(__DIR__ . self::INPUT_DIR_OBJECT, self::CLASS_NAMESPACE_OBJECT, ObjectClassReader::className());
        $this->loadClasses(__DIR__ . self::INPUT_DIR_FIELDS, self::CLASS_NAMESPACE_FIELDS, FieldsClassReader::className());
        $this->loadClasses(__DIR__ . self::INPUT_DIR_VALUES, self::CLASS_NAMESPACE_VALUES, ValuesClassReader::className());
    }

    /**
     * Loads classes by scanning a directory
     * @param string $dir
     * @param string $namespace
     * @param string $class
     * @throws Exception
     */
    private function loadClasses($dir, $namespace, $class)
    {
        // Check if directory exists
        if (file_exists($dir) == false || is_dir($dir) == false) {
            throw new Exception("Directory '$dir' not found or is not a valid directory!");
        }

        // Scan the directory and load the classes in
        foreach (scandir($dir) as $file) {
            if (
                is_file($dir . $file) &&
                in_array($file, $this->blacklist) == false
            ) {

                // Generate a new instance of
                $instance = new $class(
                    $namespace . explode(".", $file)[0],
                    explode(".", $file)[0],
                    $file,
                    $dir . $file
                );

                // Load instance into the classes array
                if ($instance instanceof AbstractClassReader) {
                    $this->classes[] = $instance;
                } else {
                    throw new Exception("Class $class must be an instance of SdkConverter\\AbstractClassReader!");
                }

                // Give debug information back
                if (__DEBUG__) {
                    echo "Found class file: " . $dir . $file . "\n";
                }
            }
        }
    }

    /**
     * Compiles all PHP classes to their respected C# variant
     * @return void
     */
    public function compile()
    {
        // Display header information
        $start = microtime(true);
        echo "SdkConverter | Facebook Ads SDK to C# converter \n";
        echo "Copyright (c) " . date('Y') . " | Searchresult \n";
        echo "\n";
        echo "Starting compiler at " . date('Y-m-d H:i:s') . "\n";
        echo "---------------------------------------- \n";

        // Compile each class
        foreach ($this->classes as $class) {
            $this->compileFile($class);
        }

        // Display duration
        $duration = round(microtime(true) - $start, 4);
        echo "---------------------------------------- \n";
        echo "Compiler finished in $duration seconds and generated {$this->getClassCount()} classes.";
    }

    /**
     * Compiles a single file using an Abstract Class Reader object
     * Class variable used in the templates
     * @param AbstractClassReader $class
     * @return void
     */
    private function compileFile(AbstractClassReader $class)
    {
        // Start output buffering and include the template class
        $start = microtime(true);
        ob_start();
        include($class->getTemplateLocation());

        // Check if directory exists
        if (file_exists($class->getOutputLocation())) {
            if (is_dir($class->getOutputLocation()) == false) {
                mkdir($class->getOutputLocation());
            }
        } else {
            mkdir($class->getOutputLocation());
        }

        // Put output into the correct directory
        file_put_contents($class->getOutputFileLocation(), ob_get_contents());
        ob_end_clean();

        // Display that the class has been created
        echo "File saved to: {$class->getOutputFileLocation()} \n";

        // Display debug information per class
        if (__DEBUG__) {
            echo "\t| Class type:\t\t\\" . $class::className() . "\n";
            echo "\t| Class namespace:\t" . $class->getClassNamespace() . "\n";
            echo "\t| Duration:\t\t\t" . $duration = (microtime(true) - $start) . " seconds\n";
            echo "\n";
        }
    }

    /**
     * Returns the amount of classes that have been loaded into the compiler
     * @return int
     */
    public function getClassCount()
    {
        return count($this->classes);
    }
}