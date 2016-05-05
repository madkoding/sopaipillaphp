<?php

/**
 * Parse fields
 */
foreach (self::$params['fields'] as $field) {
    $fieldName = $field['Field'];
    $type = ($field['Field'] == "id") ? "hidden" : $field['Type'];
    $schemas[] = "
        '$fieldName' => array(
            'type' => '$type',
            'label' => '$fieldName',
            'validation' => array()
        ),";
}

echo '<?php

namespace App\Model;

class '.self::$params['singular'].'Form extends \App\Model\\'.self::$params['singular'].'
{
    public $values;
    public $schema = array(
    '.implode("\n", $schemas).'
    
    );
}
';
