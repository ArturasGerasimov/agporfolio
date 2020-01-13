<?php


namespace App\Controller;

use App\Entity\Comments;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\LoginType;
use App\Form\RegisterType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AgporfolioController extends AbstractController
{

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/", name="main-page")
     */
    public function mainPage(Request $request, AuthenticationUtils $utils)
    {

        // login part
        $error = $utils->getLastAuthenticationError();
        $lastUsername = $utils->getLastUsername();
        $loginForm = $this->createForm(LoginType::class);
        $loginForm->handleRequest($request);

        $userOnline = $this->getUser();

        return $this->render('main-page/main-index.html.twig', [
            'loginForm'    => $loginForm->createView(),
            'user'         => $userOnline,
            'error'        => $error,
            'lastUsername' => $lastUsername
        ]);
    }
/**
     * @Route("/register", name="register")
     */
    public function register(Request $request, AuthenticationUtils $utils)
    {

        $error = $utils->getLastAuthenticationError();
        $lastUsername = $utils->getLastUsername();

        $userOnline = $this->getUser();
        $user = new User();
        $registerForm = $this->createForm(RegisterType::class, $user);
        $registerForm->handleRequest($request);

            if ($registerForm->isSubmitted() && $registerForm->isValid()) {
                $user->setPassword($this->passwordEncoder->encodePassword(
                    $user, $registerForm->get('plainPassword')->getData()
                ));

                $user->setRole("ROLE_USER");
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirectToRoute('main-page');
            }


        return $this->render('security/register.html.twig', [
            'registerForm' => $registerForm->createView(),
            'user'         => $userOnline,
            'error'        => $error,
            'lastUsername' => $lastUsername
        ]);
    }

    /**
     * @Route("/resume", name="resume")
     */
    public function resume(AuthenticationUtils $utils)
    {
        $userOnline = $this->getUser();
        $error = $utils->getLastAuthenticationError();
        $lastUsername = $utils->getLastUsername();

        return $this->render('resume/resume.html.twig', [
            'user'         => $userOnline,
            'error'        => $error,
            'lastUsername' => $lastUsername
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {}

    /**
     * @Route("/comments", name="comments")
     */
    public function comments(Request $request, AuthenticationUtils $utils)
    {
        $userOnline = $this->getUser();
        $error = $utils->getLastAuthenticationError();
        $lastUsername = $utils->getLastUsername();

        $comments = new Comments();
        $form = $this->createForm(CommentType::class, $comments);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comments->setUser( $this->getUser());
            $comments->setDate(new \DateTime());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comments);
            $entityManager->flush();

            return $this->redirectToRoute('comments');
        }

        $comments = $this->getDoctrine()->getRepository(Comments::class)->findAll();

        return $this->render("comments/comments.html.twig", [
            'form' => $form->createView(),
            'comments' => $comments,
            'user' => $userOnline,
            'error'        => $error,
            'lastUsername' => $lastUsername
        ]);
    }

    /**
     * @Route("/comm/edit/{userComments}", name="comment-edit")
     */
    public function updateUserComments(Comments $userComments, Request $request, AuthenticationUtils $utils)
    {
        $userOnline = $this->getUser();
        $error = $utils->getLastAuthenticationError();
        $lastUsername = $utils->getLastUsername();

        $form = $this->createForm(CommentType::class, $userComments);
        $form->handleRequest($request);

        $entityManager = $this->getDoctrine()->getManager();
        $userComments = $entityManager->getRepository(Comments::class)->find($userComments);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return  $this->redirectToRoute('comments');
        }

        return $this->render(
            'comments/comment-edit.html.twig',
            [
                'form' => $form->createView(),
                'userComments' => $userComments,
                'user' => $userOnline,
                'error'        => $error,
                'lastUsername' => $lastUsername
            ]
        );
    }

    /**
     * @Route("/comm/delete/{userComments}", name="comment-delete")
     */
    public function deleteUsersComment(Comments $userComments)
    {
        $this->getDoctrine()->getManager()->remove($userComments);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('comments');
    }


    /**
     * @Route("/rgb-game", name="rbg-game")
     */
    public function rbgGame()
    {
        return $this->render("projects/rbg-game.html.twig");
    }

    /**
     * @Route("/cube", name="cube")
     */
    public function cube()
    {
        return $this->render("projects/cube.html.twig");
    }
}