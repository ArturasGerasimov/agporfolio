<?php


namespace App\Controller;

use App\Entity\Comments;
use App\Entity\User;
use App\Form\CommentType;
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

        $userOnline = $this->getUser();
        dump($userOnline);

        //user registration
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword($this->passwordEncoder->encodePassword(
                $user, $form->get('plainPassword')->getData()
            ));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('main-page');
        }

        return $this->render('main-page/main-index.html.twig', [
            'form'         => $form->createView(),
            'user'         => $userOnline,
            'error'        => $error,
            'lastUsername' => $lastUsername
        ]);
    }

    /**
     * @Route("/resume", name="resume")
     */
    public function resume()
    {
        return $this->render('resume/resume.html.twig');
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {}

    /**
     * @Route("/comments", name="comments")
     */
    public function comments(Request $request)
    {

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
            'comments' => $comments
        ]);
    }
}