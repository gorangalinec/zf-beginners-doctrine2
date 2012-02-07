<?php
// Script to add an administrator with userid of 'yuser' and password of 'password'. 
use Square\Entity\User;

// 'batch-config.php' parses application.ini and instantiates Bisna\Doctrine\Container as $container.
include "batch-config.php";

$em = $container->getEntityManager();

try {
    
    $user = new User();
    $user->setUsername('user');
    $user->setPassword('password');
    
    $em->persist($user);
    $em->flush();

} catch(Exception $e) {

    echo $e->getMessage();
    return;
}
?>
