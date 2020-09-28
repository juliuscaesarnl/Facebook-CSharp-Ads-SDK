<?php

/**
 * Check if the application has been initialized
 */
if (defined("__SDK_CONVERTER__") == false) exit("Application not initialized");

/** Variable declarations for PhpStorm */
/** @var \SdkConverter\Values\ClassReader $class */
require("FacebookAds.Header.php"); ?>

namespace FacebookAds.Object.Values
{
    public class <?= $class->getClassName(); ?> : AbstractCrudObjectFields
    {
        <?php foreach($class->getConstants() as $constant => $value): ?>
        public const string <?= $constant; ?> = "<?= $value; ?>";
        <?php endforeach; ?>
    }
}
