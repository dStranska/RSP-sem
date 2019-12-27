<?php


namespace App\Controller;

use App\Entity\Article;
use App\Entity\Review;
use App\Entity\User;
use App\Form\ContactForm;
use App\Form\ReviewForm;
use App\Form\UserForm;
use App\Repository\ArticleRepository;
use App\Repository\ReviewRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends BaseController
{

    /** @var UserRepository */
    public $userRepository;

    /** @var ArticleRepository */
    public $articleRepository;

    /** @var ReviewRepository */
    public $reviewRepository;


    public function __construct(UserRepository $userRepository, ArticleRepository $articleRepository, ReviewRepository $reviewRepository)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->articleRepository = $articleRepository;
        $this->reviewRepository = $reviewRepository;
    }


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {

        if ($this->loggedUser && $this->loggedUser->getRole() === User::ROLE_AUTHOR) {
            $articles = $this->articleRepository->getArticlesByAuthor($this->loggedUser->getId());

            return $this->render('index/author.twig',
                [
                    'user' => $this->loggedUser,
                    'articles' => $articles,
                ]
            );
        } elseif ($this->loggedUser && $this->loggedUser->getRole() === User::ROLE_REDACTOR) {

            $form = $this->createForm(ReviewForm::class);
            $form->handleRequest($request);

            $newArticles = $this->articleRepository->findBy(['status' => Article::STATUS_NEW]);
            $waitingArticles = $this->articleRepository->getAllWaiting();
            $reviewDoneArticles = $this->articleRepository->findBy(['status' => Article::STATUS_REVIEW_DONE]);
            $repairedArticles = $this->articleRepository->findBy(['status' => Article::STATUS_REPAIR_DONE]);
            $readyTorepaireArticles = $this->articleRepository->findBy(['status' => Article::STATUS_REPAIR]);
            $endeArticles = $this->articleRepository->findBy(['status' => [Article::STATUS_DONE, Article::STATUS_DECLINE]]);


            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                try {
                    $data = $form->getData();
                    $article = $this->articleRepository->find($data['id_article']);
                    if (!$article) {
                        throw new \Exception('Article not found');
                    }
                    $article->setStatus(Article::STATUS_IN_PROGRESS);
                    $entityManager->persist($article);

                    foreach ($data['users'] as $user) {
                        $review = new Review();
                        $review->setArticle($article);
                        $review->setUser($user);

                        $entityManager->persist($review);
                    }
                    $entityManager->flush();
                    $this->addFlash('success', 'Recenzenti byli zadáni');
                    return $this->redirectToRoute('homepage');
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Neco se pokazilo: ' . $e->getMessage());
                    return $this->redirectToRoute('homepage');
                }
            }

            return $this->render('index/redactor.twig',
                [
                    'formObject' => $form,
                    'user' => $this->loggedUser,
                    'newArticles' => $newArticles,
                    'waitingArticles' => $waitingArticles,
                    'reviewDone' => $reviewDoneArticles,
                    'toRepair' => $readyTorepaireArticles,
                    'repaired' => $repairedArticles,
                    'endArticles' => $endeArticles
                ]
            );

        } elseif ($this->loggedUser && $this->loggedUser->getRole() === User::ROLE_RECENZENT) {

            $new = $this->reviewRepository->getMyNew($this->loggedUser->getId());
            $done = $this->reviewRepository->getMyDone($this->loggedUser->getId());

            return $this->render('index/recenzent.twig',
                [
                    'user' => $this->loggedUser,
                    'new' => $new,
                    'done' => $done

                ]
            );
        } else {
            return $this->render('index/lotLogged.twig',
                [
                    'user' => $this->loggedUser,
                ]
            );
        }

    }

    /**
     * @Route("/about", name="about")
     */
    public function about()
    {

        return $this->render('index/about_us.twig',
            [
                'user' => $this->loggedUser,
            ]);
    }

    /**
     * @Route("/autors", name="autors")
     */
    public function autors()
    {
        return $this->render('index/for_authors.twig',
            [
                'user' => $this->loggedUser,
            ]);
    }

    /**
     * @Route("login", name="login")
     */
    public function login(Request $request)
    {

        $form = $this->createForm(UserForm::class);

        $userAuthors = $this->userRepository->findBy(['role' => User::ROLE_AUTHOR]);
        $userRecenzent = $this->userRepository->findBy(['role' => User::ROLE_RECENZENT]);
        $userRedaktor = $this->userRepository->findBy(['role' => User::ROLE_REDACTOR]);

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

        return $this->render('index/login.twig',
            [
                'form' => $form->createView(),
                'authors' => $userAuthors,
                'redactors' => $userRedaktor,
                'recenzents' => $userRecenzent

            ]);
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


    /**
     * @Route("contact", name="contact")
     */
    public function contactAction(Request $request, \Swift_Mailer $mailer)
    {

        $form = $this->createForm(ContactForm::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();


            $message = (new \Swift_Message('New message from contact form'))
                ->setFrom('send@example.com')
                ->setTo([
                    'strans05@student.vspj.cz',
                    'skalic03@student.vspj.cz',
                    'kaplan01@student.vspj.cz',
                    'bulice01@student.vspj.cz',
                    'khul@student.vspj.cz',
                ])


                ->setSubject('RSP - zpráva')
                ->setBody($this->renderView(
                    'email/info.twig',
                    [
                        'name' => $data['email'],
                        'subject' => $data['subject'],
                        'message' => $data['message']

                    ]
                ));

            if (!$mailer->send($message)) {
                $this->addFlash('error', 'Něco se pokazilo');

            };
            $this->addFlash('success', 'Email byl odeslán');
            return $this->redirectToRoute('homepage');

        }
        return $this->render('index/contact.twig', [
            'form' => $form->createView(),
            'user' => $this->loggedUser
        ]);
    }
}