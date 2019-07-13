<?php
namespace Tests\Core\Model;

use PHPUnit\Framework\TestCase;

class TableTest extends TestCase
{
    public function testExtractTableName()
    {
        $table = new \Tests\Core\Model\ClassTest\MotMotTable();

        $this->assertEquals(
            "mot_mot",
            $table->extractTableName()
        );
    }
    public function testExtractTableName2()
    {
        $table = new \Tests\Core\Model\ClassTest\ClassNameTable();

        $this->assertEquals(
            "class_name",
            $table->extractTableName()
        );
    }
}
