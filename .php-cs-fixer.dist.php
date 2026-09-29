<?php

$finder = PhpCsFixer\Finder::create()
    ->in([__DIR__.'/core', __DIR__.'/Modules'])
    ->exclude(['Resources', 'Lib'])
    ->name('*.php');

// Formatting and explicit control-flow braces; risky fixers stay disabled.
return (new PhpCsFixer\Config())
    ->setRiskyAllowed(false)
    ->setUsingCache(false)
    ->setRules([
        'encoding' => true,
        'indentation_type' => true,
        'line_ending' => true,
        'no_trailing_whitespace' => true,
        'no_whitespace_in_blank_line' => true,
        'single_blank_line_at_eof' => true,
        'no_multiple_statements_per_line' => true,
        'braces_position' => true,
        'statement_indentation' => true,
        'control_structure_continuation_position' => true,
        'control_structure_braces' => true,
        'ternary_operator_spaces' => true,
        'cast_spaces' => true,
        'spaces_inside_parentheses' => true,
        'no_spaces_after_function_name' => true,
        'function_declaration' => true,
        'method_argument_space' => ['on_multiline' => 'ensure_fully_multiline'],
        'type_declaration_spaces' => true,
        'return_type_declaration' => true,
        'binary_operator_spaces' => true,
        'concat_space' => ['spacing' => 'one'],
        'unary_operator_spaces' => true,
        'single_space_around_construct' => true,
        'array_indentation' => true,
        'trim_array_spaces' => true,
        'whitespace_after_comma_in_array' => true,
        'no_spaces_around_offset' => true,
        'class_attributes_separation' => ['elements' => ['method' => 'one']],
        'blank_line_after_namespace' => true,
        'single_line_after_imports' => true,
        'no_extra_blank_lines' => true,
    ])
    ->setFinder($finder);
