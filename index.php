<?php

include "vendor/autoload.php";

use GraphQL\Executor\Executor;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\GraphQL;
use GraphQL\Utils\BuildSchema;

function setResolvers($resolvers)
{
    Executor::setDefaultFieldResolver(function ($source, $args, $context, ResolveInfo $info) use ($resolvers) {
        $fieldName = $info->fieldName;

        if (is_null($fieldName)) {
            throw new \Exception('Could not get $fieldName from ResolveInfo');
        }

        if (is_null($info->parentType)) {
            throw new \Exception('Could not get $parentType from ResolveInfo');
        }

        $parentTypeName = $info->parentType->name;

        if (isset($resolvers[$parentTypeName])) {
            $resolver = $resolvers[$parentTypeName];

            if (is_array($resolver)) {
                if (array_key_exists($fieldName, $resolver)) {
                    $value = $resolver[$fieldName];

                    return is_callable($value) ? $value($source, $args, $context, $info) : $value;
                }
            }

            if (is_object($resolver)) {
                if (isset($resolver->{$fieldName})) {
                    $value = $resolver->{$fieldName};

                    return is_callable($value) ? $value($source, $args, $context, $info) : $value;
                }
            }
        }

        return Executor::defaultFieldResolver($source, $args, $context, $info);
    });
}

try {

    setResolvers(include 'resolvers/resolver.php');

    $schemaContent = file_get_contents('schema/schema.graphql');
    $schema = BuildSchema::build($schemaContent);

    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);

    $query = $input['query'];
    $variableValues = isset($input['variables']) ? $input['variables'] : null;

    //$rootValue = ['prefix' => 'You said: '];
    $result = GraphQL::executeQuery($schema, $query, null, null, $variableValues);
    $output = $result->toArray();

} catch (\Exception $e) {
    $output = [
        'error' => [
            'message' => $e->getMessage()
        ]
    ];
}

header('Content-Type: application/json; charset=UTF-8');

echo json_encode($output);