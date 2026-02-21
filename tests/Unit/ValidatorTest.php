<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Sopaipilla\Validation\Validator;

class ValidatorTest extends TestCase
{
    // --- required ---

    public function testRequiredFailsOnEmptyString(): void
    {
        $v = new Validator(['name' => ''], ['name' => ['required' => true]]);
        $this->assertFalse($v->validate());
        $this->assertArrayHasKey('name', $v->getErrors());
    }

    public function testRequiredFailsOnMissingField(): void
    {
        $v = new Validator([], ['name' => ['required' => true]]);
        $this->assertFalse($v->validate());
    }

    public function testRequiredPassesOnNonEmptyValue(): void
    {
        $v = new Validator(['name' => 'John'], ['name' => ['required' => true]]);
        $this->assertTrue($v->validate());
        $this->assertEmpty($v->getErrors());
    }

    // --- email ---

    public function testEmailFailsOnInvalidFormat(): void
    {
        $v = new Validator(['email' => 'not-an-email'], ['email' => ['email' => true]]);
        $this->assertFalse($v->validate());
    }

    public function testEmailPassesOnValidAddress(): void
    {
        $v = new Validator(['email' => 'user@example.com'], ['email' => ['email' => true]]);
        $this->assertTrue($v->validate());
    }

    public function testEmailPassesWhenFieldIsEmpty(): void
    {
        // optional email field — empty is allowed unless 'required' is also set
        $v = new Validator(['email' => ''], ['email' => ['email' => true]]);
        $this->assertTrue($v->validate());
    }

    // --- min ---

    public function testMinFailsBelowMinimumLength(): void
    {
        $v = new Validator(['pass' => 'abc'], ['pass' => ['min' => 8]]);
        $this->assertFalse($v->validate());
    }

    public function testMinPassesAtExactMinimum(): void
    {
        $v = new Validator(['pass' => 'abcdefgh'], ['pass' => ['min' => 8]]);
        $this->assertTrue($v->validate());
    }

    public function testMinPassesAboveMinimum(): void
    {
        $v = new Validator(['pass' => 'abcdefghi'], ['pass' => ['min' => 8]]);
        $this->assertTrue($v->validate());
    }

    // --- max ---

    public function testMaxFailsAboveLimit(): void
    {
        $v = new Validator(['bio' => str_repeat('x', 201)], ['bio' => ['max' => 200]]);
        $this->assertFalse($v->validate());
    }

    public function testMaxPassesAtExactLimit(): void
    {
        $v = new Validator(['bio' => str_repeat('x', 200)], ['bio' => ['max' => 200]]);
        $this->assertTrue($v->validate());
    }

    // --- numeric ---

    public function testNumericFailsOnAlphaString(): void
    {
        $v = new Validator(['age' => 'abc'], ['age' => ['numeric' => true]]);
        $this->assertFalse($v->validate());
    }

    public function testNumericPassesOnNumericString(): void
    {
        $v = new Validator(['age' => '25'], ['age' => ['numeric' => true]]);
        $this->assertTrue($v->validate());
    }

    public function testNumericPassesOnIntegerValue(): void
    {
        $v = new Validator(['age' => 25], ['age' => ['numeric' => true]]);
        $this->assertTrue($v->validate());
    }

    // --- in ---

    public function testInFailsOnDisallowedValue(): void
    {
        $v = new Validator(['role' => 'superadmin'], ['role' => ['in' => ['admin', 'user']]]);
        $this->assertFalse($v->validate());
    }

    public function testInPassesOnAllowedValue(): void
    {
        $v = new Validator(['role' => 'admin'], ['role' => ['in' => ['admin', 'user']]]);
        $this->assertTrue($v->validate());
    }

    // --- regex ---

    public function testRegexPassesOnMatchingValue(): void
    {
        $v = new Validator(['code' => 'ABC123'], ['code' => ['regex' => '/^[A-Z]{3}\d{3}$/']]);
        $this->assertTrue($v->validate());
    }

    public function testRegexFailsOnNonMatchingValue(): void
    {
        $v = new Validator(['code' => 'abc'], ['code' => ['regex' => '/^[A-Z]{3}\d{3}$/']]);
        $this->assertFalse($v->validate());
    }

    // --- helpers ---

    public function testHasErrorsIsFalseOnValidData(): void
    {
        $v = new Validator(['email' => 'a@b.com'], ['email' => ['email' => true]]);
        $v->validate();
        $this->assertFalse($v->hasErrors());
    }

    public function testMultipleFieldsCanFailSimultaneously(): void
    {
        $v = new Validator(
            ['name' => '', 'email' => 'bad'],
            ['name' => ['required' => true], 'email' => ['email' => true]]
        );
        $this->assertFalse($v->validate());
        $this->assertCount(2, $v->getErrors());
    }

    public function testGetErrorsIsEmptyBeforeValidation(): void
    {
        $v = new Validator(['name' => ''], ['name' => ['required' => true]]);
        $this->assertEmpty($v->getErrors());
    }
}
