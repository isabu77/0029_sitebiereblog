<?php

namespace Tests\Core\Controller\Helpers;

use PHPUnit\Framework\TestCase;
use \Core\Controller\FormController;

class FormControllerTest extends TestCase
{
    public function testFormNoPost()
    {
        $form = new FormController();
        $errors["post"] = "no-data";
        
        $this->assertEquals(
            $errors,
            $form->hasErrors()
        );
    }
    public function testFormMailEmpty()
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
    public function testFormMailVerifyEmpty()
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
    public function testFormPasswordTooShort()
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
    public function testFormMailDifferent()
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

    public function testFormPwdDifferent()
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

    public function testFormGetDatas()
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
}
