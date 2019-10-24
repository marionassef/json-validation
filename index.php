<?php
require __DIR__ . '/vendor/autoload.php';

use Opis\JsonSchema\Validator;
use Opis\JsonSchema\ValidationResult;
use Opis\JsonSchema\ValidationError;
use Opis\JsonSchema\Schema;

try {
    //get the filters from the url
    $filter = $_GET['filter'];

    //array of rules
    $rules1 = [
        '$schema' => 'http://json-schema.org/draft-07/schema#',
        '$id' => 'http://api.example.com/profile.json#',
        'type' => 'object',
        'properties' => [
            'blocks' => [
                'type' => 'array',
                'maxItems' => 100,
                'uniqueItems' => true,
                'items' =>
                    [
                        'type' => 'object',
                        'properties' =>
                            [
                                'subject' => [
                                    'enum' => [
                                        '`first_name`', '`last_name`', '`email`', '`academy`', '`service_area`',
                                        '`position`', '`job_title`', '`video`', '`unit`'
                                    ],
                                ],
                                'operator' => [
                                    'enum' => [
                                        'like', 'not like', '=', '!=', 'c', 'ip', 'ns', 'ef', 'w', 'hw'
                                    ],
                                ],
                                'comparators' =>
                                    [
                                        'type' => 'array',
                                        "uniqueItems" => true,
                                        'items' =>
                                            [
                                                'type' => 'string',
                                                "if" => [
                                                    "value-of-subject" => '`video`' || '`unit`',
                                                ],
                                                "then" => [
                                                    'required' => ['wqww']
                                                    ],
                                                "else" => [
                                                    "minLength" => 1,
                                                    "maxLength" => 20,
                                                ],
                                            ],
                                    ],

                            ],
                        'required' => ['subject', 'operator', 'comparators'],
                    ]
            ]
        ]
    ];

    //decode the filter
    $decoded_filter = json_decode($filter);
//    var_export($decoded_filter);
//    die();
    $schema = Schema::fromJsonString(json_encode($rules1));
    $validator = new Validator();

    /** @var ValidationResult $result */
    $result = $validator->schemaValidation($decoded_filter, $schema);

    if ($result->isValid()) {
        echo '$data is valid', PHP_EOL;
    } else {
        /** @var ValidationError $error */
        $error = $result->getFirstError();
        echo '$data is invalid', PHP_EOL;
        echo "Error: ", $error->keyword(), PHP_EOL;
        echo json_encode($error->keywordArgs(), JSON_PRETTY_PRINT), PHP_EOL;
    }
} catch (Exception $exception) {
    var_dump($exception);
    die();
}
