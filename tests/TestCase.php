<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function deleteTestRecords(string $modelClassName, string $fieldName, string $keyword, $operator = 'like', $suffix = '%'): int
    {
        return $modelClassName::query()
            ->where($fieldName, $operator, $keyword.$suffix)
            ->delete();
    }
}
