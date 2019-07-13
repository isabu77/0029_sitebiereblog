<?php
namespace Tests\Core\Model;

use PHPUnit\Framework\TestCase;

class TableTest extends TestCase
{
    public function testExtractTableName()
    {
        require '.'. DIRECTORY_SEPARATOR. 'src'. DIRECTORY_SEPARATOR . 'config.php';
        $prefix = $env['TABLE_PREFIX'];

        $table = new \Tests\Core\Model\ClassTest\MotMotTable();

        $this->assertEquals(
            $prefix."mot_mot",
            $table->extractTableName()
        );
    }
    public function testExtractTableName2()
    {
        require '.'. DIRECTORY_SEPARATOR. 'src'. DIRECTORY_SEPARATOR . 'config.php';
        $prefix = $env['TABLE_PREFIX'];

        $table = new \Tests\Core\Model\ClassTest\ClassNameTable();

        $this->assertEquals(
            $prefix."class_name",
            $table->extractTableName()
        );
    }
}
