<?php
require __DIR__ . '/vendor/autoload.php';

use Opis\JsonSchema\Validator;
use Opis\JsonSchema\ValidationResult;
use Opis\JsonSchema\ValidationError;
use Opis\JsonSchema\Schema;

try {
    /*?sort=last_name&sort_dir=asc&filter=%7B%22blocks%22%3A%5B%7B%22subject%22%3A%22%60first_name%60%22%2C%22operator%22%3A%22like%22%2C%22comparators%22%3A%5B%22mario%22%5D%7D%2C%7B%22subject%22%3A%22%60last_name%60%22%2C%22operator%22%3A%22like%22%2C%22comparators%22%3A%5B%22mario%22%5D%7D%2C%7B%22subject%22%3A%22%60email%60%22%2C%22operator%22%3A%22like%22%2C%22comparators%22%3A%5B%22mario%22%5D%7D%2C%7B%22subject%22%3A%22%60academy%60%22%2C%22operator%22%3A%22%3D%22%2C%22comparators%22%3A%5B%22sss%22%5D%7D%2C%7B%22subject%22%3A%22%60service_area%60%22%2C%22operator%22%3A%22%3D%22%2C%22comparators%22%3A%5B%22ex%22%5D%7D%2C%7B%22subject%22%3A%22%60position%60%22%2C%22operator%22%3A%22%3D%22%2C%22comparators%22%3A%5B%22sl%22%5D%7D%2C%7B%22subject%22%3A%22%60job_title%60%22%2C%22operator%22%3A%22like%22%2C%22comparators%22%3A%5B%22teacher%22%5D%7D%2C%7B%22subject%22%3A%22%60video%60%22%2C%22operator%22%3A%22w%22%2C%22comparators%22%3A%5B%223zc1wu15wph%22%5D%7D%2C%7B%22subject%22%3A%22%60unit%60%22%2C%22operator%22%3A%22c%22%2C%22comparators%22%3A%5B%225t0fzbae2rf%22%5D%7D%5D%2C%22global_logic%22%3A%22and%22%7D&export=false*/
    //get the filters from the url
    $filter = $_GET['filter'];

    $rules = [
        'type' => 'object',
        'properties' => [
            'blocks' => [
                'type' => 'array',
                'maxItems' => 20,
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
                                            ]
                                    ],
                            ],
                        'required' => ['subject', 'operator', 'comparators'],
                        'if' =>
                            [
                                'properties' =>
                                    [
                                        'subject' =>
                                            [
                                                'enum' => ['`unit`', '`video`'],
                                            ],
                                    ],
                            ],
                        'then' =>
                            [

                                'properties' =>
                                    [
                                        'comparators' =>
                                            [
                                                'items' =>
                                                    [
                                                        'type' => 'string',
                                                        'pattern' => '^[\w_]{11}$',
                                                    ],
                                            ],
                                    ],
                            ],
                    ],
            ],
        ],
    ];

    //decode the filter
    $decoded_filter = json_decode($filter);

    $schema = Schema::fromJsonString(json_encode($rules));
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
