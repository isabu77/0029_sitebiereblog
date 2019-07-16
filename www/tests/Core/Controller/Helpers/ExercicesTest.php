<?php

namespace Tests\Core\Controller\Helpers;

use PHPUnit\Framework\TestCase;
use \Core\Controller\Helpers\Exercices;

class ExercicesTest extends TestCase
{
    public function testExercice1(): void
    {
        $exo = new Exercices();
        $this->assertEquals(true, $exo->exercice1() );

    }

    public function testExercice2(): void
    {
        $exo = new Exercices();

        $this->assertEquals("bonjour", $exo->exercice2("bonjour") );

    }

    public function testExercice3(): void
    {
        $exo = new Exercices();
        $this->assertEquals("Bonjour Madame", $exo->exercice3("Bonjour"," Madame") );

    }

    public function testExercice4(): void
    {
        $exo = new Exercices();

        $this->assertEquals("Le premier nombre est plus grand", $exo->exercice4(6,5) );
        $this->assertEquals("Le premier nombre est plus petit", $exo->exercice4(6,7) );
        $this->assertEquals("Les deux nombres sont identiques", $exo->exercice4(5,5) );
    }

    public function testExercice5(): void
    {
        $exo = new Exercices();

        $this->assertEquals("6chaine", $exo->exercice5(6,"chaine") );
    }

    public function testExercice6(): void
    {
        $exo = new Exercices();

        $this->assertEquals("Bonjour isa bulle, tu as 30 ans", $exo->exercice6("isa", "bulle", "30") );
    }

    public function testExercice7(): void
    {
        $exo = new Exercices();

        $this->assertEquals("Vous êtes un homme et vous êtes majeur", $exo->exercice7(30, "homme") );
        $this->assertEquals("Vous êtes un homme et vous êtes majeur", $exo->exercice7(18, "homme") );
        $this->assertEquals("Vous êtes un homme et vous êtes mineur", $exo->exercice7(17, "homme") );
        $this->assertEquals("Vous êtes une femme et vous êtes majeure", $exo->exercice7(30, "femme") );
        $this->assertEquals("Vous êtes une femme et vous êtes majeure", $exo->exercice7(18, "femme") );
        $this->assertEquals("Vous êtes une femme et vous êtes mineure", $exo->exercice7(17, "femme") );
        $this->assertEquals("merci de choisir entre 'homme' ou 'femme'", $exo->exercice7(17, "arbre") );
        $this->assertEquals("merci de choisir entre 'homme' ou 'femme'", $exo->exercice7(30, "arbre") );
    }

    public function testExercice8(): void
    {
        $exo = new Exercices();

        $this->assertEquals(0, $exo->exercice8() );
        $this->assertEquals(15, $exo->exercice8(6,5,4) );
    }

    public function testExercice9(): void
    {
        $exo = new Exercices();
        try{
            $this->assertEquals(0, $exo->exercice9("gfgs") );

        }catch( Exception $e){
            echo 'EXCEPTION : ' . $e->getMassage();
        }
        $this->assertEquals(15, $exo->exercice9(4, 5,6) );
        $this->assertEquals(15, $exo->exercice9(6,5,4) );
        $this->assertEquals(10, $exo->exercice9(1,1,1,1,1,1,1,1,1,1) );
    }


}