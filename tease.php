<?php
/**
 * Created by PhpStorm.
 * User: Snyper_
 * Date: 1/13/14
 * Time: 5:09 PM
 */
//MD5, SHA1 and SHA256
//echo sha256("Manyanga#5");
//CykKVH8/32im7vPFg2Q.C
$salt = '$2y$07$CykKVH8/32im7vPFg2Q.C';

echo "<br />";

    $blowfish = crypt('test', $salt);
    echo 'Blowfish:     ' . $blowfish . "<br>";
    echo 'Salt:     ' . $salt . "<br>";


if(crypt("manyanga", $salt) == $blowfish) {
    //valid password
    echo "Kawa <br>";
} else {
    echo "No plot <br> ";
    echo crypt("manyanga", $blowfish);
}