<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
//use App\Service\JWTService;
//use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
//    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher,
//                             userAuthenticatorInterface $userAuthenticator, UsersAuthenticator $authenticator,
//                             EntityManagerInterface $entityManager, SendMailService $mail, JWTService $jwt): Response
//    {
        public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher,
                                 userAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator,
                                 EntityManagerInterface $entityManager): Response
    {

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            //on génére le JWT de l'ultilisateur (token)
            // service créé avec fichier JWTService
            // on crée le Header d'aprés données sur jwt.io
//            $header = [
//                'typ' => 'JWT',
//                'alg' => 'HS256'
//            ];
//            //on crée le payload
//            $payload = [
//                'user_id' => $user->getId()
//            ];
//            // on genere le token
//            $token = $jwt->generate($header, $payload,
//                $this->getParameter('app.jwtsecret'));
//
//
//            //on envoie un mail
//            $mail->send(
//                'no-reply@monsite.net',
//                $user->getEmail(),
//                'Activation de votre compte',
//                'register',
//                compact('user', 'token')
//            );

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

//    #[Route('/verif/{token}', name: 'verify_user')]
//    public function verifyUser($token, JWTService $jwt,
//                               UserRepository $usersRepository, EntityManagerInterface $em): Response
//    {
//        //on verifie si le token est valide, n'est pas expiré et n'a pas été modifié
//        if($jwt->isValid($token) && !$jwt->isExpired($token) &&
//            $jwt->check($token, $this->getParameter('app.jwtsecret'))){
//            // On récupère le payload
//            $payload = $jwt->getPayload($token);
//
//            //on récupere le user du token
//            $user = $usersRepository->find($payload['user_id']);
//
//            //on verifie que l'utilisateur existe et n'a pas encore activé son compte
//            if($user && !$user->getIsVerified()){
//                $user->setIsVerified(true);
//                $em->flush($user);
//                $this->addFlash('success', 'Utilisateur activé');
//                return $this->redirectToRoute('profile_index');
//            }
//
//        }
//        //ici probleme dans le token (expiration ou invalide)
//        $this->addFlash('danger', 'Le token est invalide ou a expiré');
//        return $this->redirectToRoute('app_login');
//    }
//
//    #[Route('/renvoiverif', name: 'resend_verif')]
//    public function resendVerif(JWTService $jwt, SendMailService $mail,
//                                UserRepository $usersRepository): Response
//    {
//        $user = $this->getUser();
//
//        if(!$user) {
//            $this->addFlash('danger', 'Vous devez être connecté pour accéder à cette page');
//            return $this->redirectToRoute('app_login');
//        }
//
//        if($user->getIsVerified()){
//            $this->addFlash('warning', 'Cet utilisateur est déja activé');
//            return $this->redirectToRoute('profile_index');
//        }
//
//        //on génére le JWT de l'ultilisateur (token)
//        // service créé avec fichier JWTService
//        // on crée le Header d'aprés données sur jwt.io
//        $header = [
//            'typ' => 'JWT',
//            'alg' => 'HS256'
//        ];
//        //on crée le payload
//        $payload = [
//            'user_id' => $user->getId()
//        ];
//        // on genere le token
//        $token = $jwt->generate($header, $payload,
//            $this->getParameter('app.jwtsecret'));
//
//
//        //on envoie un mail
//        $mail->send(
//            'no-reply@monsite.net',
//            $user->getEmail(),
//            'Activation de votre compte',
//            'register',
//            compact('user', 'token')
//        );
//        $this->addFlash('success', 'Email de vérification envoyé');
//        return $this->redirectToRoute('profile_index');
//
//    }


}