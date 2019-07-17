<?php

namespace Tests\Core\Controller\Helpers;

use PHPUnit\Framework\TestCase;
use \Core\Controller\FormController;

class FormControllerTest extends TestCase
{
    public function testNewWithPost(): void
    {
        $post = ["name" => "toto"];
        $form = new FormController($post);
        $this->assertEquals(
            [],
            $form->hasErrors()
        );
    }

    public function testNewWithoutPost(): void
    {
        $form = new FormController();
        $errors["post"] = "no-data";

        $err = $form->hasErrors();

        $this->assertArrayHasKey("post", $err);

        $this->assertEquals($errors, $err);
    }

    public function testGetDatasWithoutField(): void
    {
        $post = ["name" => "toto"];
        $form = new FormController($post);
        $this->assertEquals([], $form->getDatas());
    }

    public function testFieldsChain(): void
    {
        $form = new FormController();

        $this->assertEquals(
            $form,
            $form->field('firstname')
        );
    }

    public function testGetDatasWithFields(): void
    {
        $post = ["firstname" => "toto", "lastname" => "titi"];
        $form = new FormController($post);
        $form->field('firstname')
            ->field('lastname');
        $form->hasErrors();

        $this->assertEquals(
            ["firstname" => "toto", "lastname" =>  "titi"],
            $form->getDatas()
        );
    }

    public function testFormGetDatas(): void
    {
        $post = [
            "mail" => "admin@admin.fr",
            "mailVerify" => "admin@admin.fr",
            "password" => "adminadmin",
            "passwordVerify" => "adminadmin"
        ];

        $form = new FormController($post);
        $form->field('mail', ["require", "verify"]);
        $form->field('password', ["require", "verify", "length" => 8]);
        $form->hasErrors();

        $datas = [
            "mail" => htmlspecialchars("admin@admin.fr"),
            "password" => htmlspecialchars("adminadmin"),
        ];

        $this->assertEquals(
            $datas,
            $form->getDatas()
        );
    }

    public function testFormMailEmpty(): void
    {
        $post = [
            "mail" => "",
            "mailVerify" => "admin@admin.com"
        ];

        $form = new FormController($post);
        $form->field('mail', ["require"]);

        $errors["mail"] = "Le champ mail ne peut pas être vide";
        $this->assertEquals(
            $errors,
            $form->hasErrors()
        );
    }
    public function testFormMailVerifyEmpty(): void
    {
        $post = [
            "mail" => "admin@admin.fr",
            "mailVerify" => ""
        ];

        $form = new FormController($post);
        $form->field('mail', ["require", "verify"]);

        $errors["mail"] = "Le champ mail de vérification ne peut pas être vide";
        $this->assertEquals(
            $errors,
            $form->hasErrors()
        );
    }
    public function testFormPasswordTooShort(): void
    {
        $post = [
            "password" => "admin",
            "passwordVerify" => "admin"
        ];

        $form = new FormController($post);
        $form->field('password', ["require", "verify", "length" => 8]);

        //$datas = $form->getDatas();

        $errors["password"] = "Le champ password doit avoir au minimum 8 caractères";
        $this->assertEquals(
            $errors,
            $form->hasErrors()
        );
    }
    public function testFormMailDifferent(): void
    {
        $post = [
            "mail" => "admin@admin.fr",
            "mailVerify" => "admin@admin.com"
        ];

        $form = new FormController($post);
        $form->field('mail', ["require", "verify"]);

        $errors["mail"] = "Les champs mail doivent correspondre";
        $this->assertEquals(
            $errors,
            $form->hasErrors()
        );
    }

    public function testFormPwdDifferent(): void
    {
        $post = [
            "password" => "admin",
            "passwordVerify" => "admin123"
        ];

        $form = new FormController($post);
        $form->field('password', ["require", "verify"]);

        $errors["password"] = "Les champs password doivent correspondre";
        $this->assertEquals(
            $errors,
            $form->hasErrors()
        );
    }
}
