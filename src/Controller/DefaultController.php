<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use App\Services\GiftsService;

class DefaultController extends AbstractController
{
    public function __construct(private ManagerRegistry $doctrine, GiftsService $giftsService) {
        $giftsService->gifts = ['a','b','c','d'];
    }

    #[Route('/default/', name: 'default')]
    public function index(GiftsService $giftsService): Response
    {
        $names = ['Jan','Paweł','Maria','Zuzanna','Maciej','Karolina','Edyta'];
        $surnames = ['Nowak','Kowalski','Wiśniewski','Wójcik','Kowalczyk','Kamiński','Lewandowski'];

        $user = new User;
        $user->setName($names[array_rand($names)] . ' ' . $surnames[array_rand($surnames)]);

        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        $users = $this->doctrine->getRepository(User::class)->findAll();

        return $this->render('default/index.html.twig',
            [
                'users'=>$users,
                'controller_name'=>'Default Controller',
                'random_gift' => $giftsService->gifts
            ]
        );
    }

    // "?" przy parametrze 'id' oznacza, że jest opcjonalny
    #[Route(
        '/users/{id?}',
        name: 'show_user',
        requirements: ['id'=>'\d+'],
        defaults: ['id' => 5]
    )]
    public function showUser($id): Response
    {
        if($id)
        {
            $user = $this->doctrine->getRepository(User::class)->find($id);
            dump($user);
            return $this->render('users/show.html.twig',['user'=>$user]);
        }
        $users = $this->doctrine->getRepository(User::class)->findAll();
        return dump($users);
        return $this->render('users/index.html.twig',['users'=>$users]);
    }
}
