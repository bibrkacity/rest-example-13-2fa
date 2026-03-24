<?php

use Tests\Details\UserTest;

test('Tests of the user\s index()', function () {
    $test = new UserTest('index');
    $test->index();
});

test('Tests of the user\s store()', function () {
    $test = new UserTest('store');
    $test->store();
});

test('Tests of the user\s show()', function () {
    $test = new UserTest('show');
    $test->show();
});

test('Tests of the user\s update()', function () {
    $test = new UserTest('update');
    $test->update();
});

test('Tests of the user\s destroy()', function () {
    $test = new UserTest('destroy');
    $test->destroy();
});
