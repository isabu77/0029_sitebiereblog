<?php
// Exercices

function exercice1()
{
    return true;
} 

function exercice2(string $str): string
{
    return $str;
} 

function exercice3(string $str1, string $str2)
{
    return $str1 . $str2;
} 

function exercice4(int $num1, int $num2)
{
    if ($num1 > $num2){
        return ("Le premier nombre est plus grand");
    }
    elseif ($num1 < $num2){
        return ("Le premier nombre est plus petit");
    }
    elseif ($num1 === $num2){
        return ("Les deux nombres sont identiques");
    }
} 

function exercice5(int $num1, string $str2)
{
    return $num1 . $str2;

} 

function exercice6(string $nom, string $prenom, string $age)
{
    return "Bonjour " . $nom . " " . $prenom . ", tu as " . $age . " ans";
} 

function exercice7(int $age, string $genre)
{
    if ($genre == "homme" && $age >= 18){
        return "Vous êtes un homme et vous êtes majeur <br />"; 
    }elseif ($genre == "homme" && $age < 18){
        return "Vous êtes un homme et vous êtes majeur <br />"; 
    }elseif ($genre == "femme" && $age >= 18){
        return "Vous êtes une femme et vous êtes majeure <br />"; 
    }elseif($genre == "femme" && $age < 18){
        return "Vous êtes une femme et vous êtes mineure <br />"; 
    }else{
        return "merci de choisir entre 'homme' ou 'femme' <br />"; 
    }
    
} 
function exercice8(int $num1 = 0, int $num2 = 0, int $num3 = 0)
{
    return $num1 + $num2 + $num3;
} 

function exercice9()
{
    return array_sum(func_get_args());
}
//$mois = array("Janvier","Février","mars","avril","mai","juin","juillet","aout","septembre","octobre","novembre","décembre");

function tableau1()
{
    $mois = array("Janvier","Février","mars","avril","mai","juin","juillet","aout","septembre","octobre","novembre","décembre");
    return $mois;
} 

function tableau2(array $mois)
{
    echo $mois[2];
} 
function tableau3(array $mois)
{
    echo $mois[5];

} 
function tableau4(array $mois)
{
    $mois[7] = "août";
} 
function tableau5()
{
    $HautsFrance = array("02" => "Aisne","59" => "Nord","60" => "Oise","62" => "Pas-de-Calais","80" => "Somme");
    return $HautsFrance;
} 
function tableau6(array $HautsFrance)
{
    echo $HautsFrance["59"];
} 
function tableau7(array $HautsFrance)
{
	$HautsFrance["51"] = "Marne";
	echo $HautsFrance["51"] . "<br>";

} 
function tableau8(array $mois)
{
	for($i=0; $i < count($mois) ; $i++){
		echo "mois " . $i . ": " . $mois[$i] . "<br>";
	}

} 
function tableau9(array $HautsFrance)
{
	foreach ($HautsFrance as $key => $value) {
		echo "Département : " . $value . "<br>";
	}

} 
function tableau10(array $HautsFrance)
{
	foreach ($HautsFrance as $key => $value) {
		echo "Le département " . $value . " a le numéro  " . $key . "<br>";
	}

} 

echo "<br/>" . exercice1();
echo "<br/>" . exercice2("bonjour");
echo "<br/>" . exercice3("Bonjour", "Madame");
echo "<br/>" . exercice4(5,6);
echo "<br/>" . exercice4(6,5);
echo "<br/>" . exercice4(5,5);
echo "<br/>" . exercice5(5," ans");
echo "<br/>" . exercice6("isa", "bulle", "30");
echo "<br/>" . exercice7(30, "homme");
echo "<br/>" . exercice7(17, "homme");
echo "<br/>" . exercice7(30, "femme");
echo "<br/>" . exercice7(17, "femme");
echo "<br/>" . exercice8(5,6,7);

echo "<br/> somme: " . exercice9(1,1,1,1,1,1,1,1);
echo "<br/> somme: " . exercice9();
echo "<br/> somme: " . exercice9(5,5,5);

echo "<br/>";


$mois = tableau1();
echo "<br/>" . tableau2($mois);
echo "<br/>" . tableau3($mois);
echo "<br/>" . tableau4($mois);
$HautsFrance =  tableau5();
echo "<br/>" . tableau7($HautsFrance);
echo "<br/>" . tableau8($HautsFrance);
echo "<br/>" . tableau9($HautsFrance);
echo "<br/>" . tableau10($HautsFrance);




