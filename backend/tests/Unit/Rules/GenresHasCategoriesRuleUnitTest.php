<?php


namespace Tests\Unit\Rules;


use App\Rules\GenresHasCategoriesRules;
use Mockery\MockInterface;
use Tests\TestCase;

class GenresHasCategoriesRuleUnitTest extends TestCase
{

    public function testCategoriesIdField()
    {
        $rule = new GenresHasCategoriesRules([1,1,2,2]);

        $reflectionClass = new \ReflectionClass(GenresHasCategoriesRules::class);
        $reflectionProperty = $reflectionClass->getProperty('categoriesId');
        $reflectionProperty->setAccessible(true);

        $categoriesId = $reflectionProperty->getValue($rule);
        $this->assertEqualsCanonicalizing([1,2], $categoriesId);
    }

    public function testGenresIdField()
    {
        $rule = new GenresHasCategoriesRules([]);
        $rule->passes('', [1,1,2,2]);

        $reflectionClass = new \ReflectionClass(GenresHasCategoriesRules::class);
        $reflectionProperty = $reflectionClass->getProperty('genresId');
        $reflectionProperty->setAccessible(true);

        $genresId = $reflectionProperty->getValue($rule);
        $this->assertEqualsCanonicalizing([1,2], $genresId);
    }


    public function testGenresIdValue()
    {
        $rule = $this->createRuleMock([]);

        $rule->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturnNull();

        $rule->passes('', [1,1,2,2]);

        $reflectionClass = new \ReflectionClass(GenresHasCategoriesRules::class);
        $reflectionProperty = $reflectionClass->getProperty('genresId');
        $reflectionProperty->setAccessible(true);

        $genresId = $reflectionProperty->getValue($rule);
        $this->assertEqualsCanonicalizing([1,2], $genresId);
    }



    public function testParseReturnFalseWhenCategoriesOrGenresIsArrayEmpty()
    {
        $rule = $this->createRuleMock([1]);
        $this->assertFalse($rule->passes('', []));

        $rule = $this->createRuleMock([]);
        $this->assertFalse($rule->passes('', [1]));


    }

    public function testParseReturnFalseWhenGetRowsIsEmpty()
    {
        $rule = $this->createRuleMock([1]);

        $rule->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturn(collect());

        $this->assertFalse($rule->passes('', [1]));
    }

    public function testParseReturnFalseWhenHasCategoriesWithoutGenres()
    {
        $rule = $this->createRuleMock([1,2]);

        $rule->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturn(collect(['category_id' => 1]));

        $this->assertFalse($rule->passes('', [1]));
    }

    public function testPassesIsValid()
    {
        $rule = $this->createRuleMock([1,2]);
        $rule->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturn(collect([
                ['category_id' => 1],
                ['category_id' => 2],
            ]));

        $this->assertTrue($rule->passes('', [1]));

        $rule = $this->createRuleMock([1,2]);
        $rule->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturn(collect([
                ['category_id' => 1],
                ['category_id' => 2],
                ['category_id' => 1],
                ['category_id' => 2],
            ]));

        $this->assertTrue($rule->passes('', [1]));

    }

    protected function createRuleMock(array $categoriesId): MockInterface
    {
        return \Mockery::mock(GenresHasCategoriesRules::class, [$categoriesId])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
    }



}