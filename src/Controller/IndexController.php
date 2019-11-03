<?php


namespace App\Controller;

use App\Form\UserForm;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends BaseController
{

    /** @var UserRepository */
    public $userRepository;

    /** @var Session */
    public $session;

    public function __construct(UserRepository $userRepository)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->session = new Session();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {

        return $this->render('index/index.twig',
            [
                'user' => $this->session->get('user')
            ]
        );
    }

    /**
     * @Route("login", name="login")
     */
    public function login(Request $request)
    {

        $form = $this->createForm(UserForm::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();


            $user = $this->userRepository->findOneBy([
                'email' => $data['email'],
                'password' => $data['password'],
            ]);

            if ($user) {
                $this->session->set('user', $user);
                $this->addFlash('success', 'Super, jsi přihlášen');
                return $this->redirectToRoute('homepage');
            } else {
                $this->addFlash('error', 'Špatný Email nebo heslo');
                return $this->redirectToRoute('login');
            }
        }

        return $this->render('index/login.twig', ['form' => $form->createView()]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("logout", name="logout")
     */
    public function logout()
    {
        if (!$this->session->get('user')) {
            $this->addFlash('error', 'Musíte se přihlásít');
        }

        $this->session->set('user', false);
        $this->addFlash('success', 'Byl jste úspěšně odhlášen');
        return $this->redirectToRoute('homepage');
    }
}