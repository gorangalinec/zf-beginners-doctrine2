<?php
// Script to add an user, i.e., an administrator. 
use Square\Entity\User;

// 'batch-config.php' parses application.ini and instantiates Bisna\Doctrine\Container as $container.
include "batch-config.php";

$em = $container->getEntityManager();

try {
    
    $user = new User();
    $user->setUsername('kurt');
    $user->setPassword('password');
    
    $em->persist($user);
    $em->flush();

} catch(Exception $e) {

    echo $e->getMessage();
    return;
}
?>
