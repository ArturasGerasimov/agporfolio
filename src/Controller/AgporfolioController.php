<?php


namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function mainPage(Request $request)
    {
        $userOnline = $this->getUser();
        dump($userOnline);

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
            'form' => $form->createView(),
            'user' => $userOnline
        ]);
    }

    /**
     * @Route("/resume", name="resume")
     */
    public function resume()
    {
        return $this->render('resume/resume.html.twig');
    }


}