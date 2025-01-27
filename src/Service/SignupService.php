<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SignupService
{
    public function __construct(private EntityManagerInterface $entityManager, private ValidatorInterface $validator){}

    public final function signUpWithCSV(string $csv): array | false
    {
        $lines = explode("\n", $csv);
        $lines = array_map(function ($line){return explode(',', $line);}, $lines);

        $createdAccounts = [];
        $invalidLines = [];

        for($i = 0; $i < count($lines); $i++){
            $line = $lines[$i];

            $email = $line[0] ?? null;
            $password = $line[1] ?? null;
            $firstname = $line[2] ?? null;
            $lastname = $line[3] ?? null;
            $phone  = $line[4] ?? null;

            $user = new User();
            $user->setEmail($email);
            $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
            $user->setFirstname($firstname);
            $user->setLastname($lastname);
            $user->setPhone($phone);
            $user->setAdmin(false);
            $user->setRoles(['ROLE_USER']);
            $user->setActive(true);

            if($this->validator->validate($user)){
                $createdAccounts[] = $user;
                $this->entityManager->persist($user);
            }else{
                $invalidLines[] = $i;
            }
        }

        try{
            $this->entityManager->flush();
        }catch (\Exception $e){
            return false;
        }

        return ['createdAccounts' => $createdAccounts, 'invalidLines' => $invalidLines];
    }
}