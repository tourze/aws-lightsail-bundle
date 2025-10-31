<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in(__DIR__)
    ->exclude(['vendor', 'var'])
    ->name('*.php');

return (new Config())
    ->setRules([
        // 基础规则
        '@PSR12' => true,
        '@PHP81Migration' => true,

        // 严格类型
        'declare_strict_types' => true,

        // 数组语法
        'array_syntax' => ['syntax' => 'short'],

        // 类型声明
        'no_short_bool_cast' => true,
        'strict_comparison' => true,
        'strict_param' => true,

        // 现代PHP特性
        'native_function_invocation' => [
            'include' => ['@compiler_optimized', '@internal'],
        ],
        'no_unreachable_default_argument_value' => true,
        'no_useless_return' => true,

        // 代码格式
        'concat_space' => ['spacing' => 'one'],
        'binary_operator_spaces' => [
            'default' => 'align_single_space_minimal',
            'operators' => [
                '=' => 'align',
                '=>' => 'align',
                '??' => 'align',
            ],
        ],
        'blank_line_before_statement' => [
            'statements' => ['break', 'continue', 'declare', 'return', 'throw', 'try'],
        ],

        // 导入排序
        'ordered_imports' => [
            'sort_algorithm' => 'alpha',
            'imports_order' => ['class', 'function', 'const'],
        ],

        // 命名规范
        'no_blank_lines_after_class_opening' => true,
        'single_class_element_per_statement' => true,

        // 注释和文档
        'no_empty_comment' => true,
        'no_empty_phpdoc' => true,
        'phpdoc_indent' => true,
        'phpdoc_trim' => true,

        // 安全相关
        'blank_line_after_opening_tag' => true,
        'declare_equal_normalize' => [
            'space' => 'none',
        ],
        'single_line_after_imports' => true,

        // 性能相关
        'dir_constant' => true,
        'function_typehint_space' => true,
        'no_space_around_double_colon' => true,
        'return_type_declaration' => [
            'space_before' => 'none',
        ],
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder);