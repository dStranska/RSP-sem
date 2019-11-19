<?php


namespace App\Controller;

use App\Entity\Article;
use App\Entity\Review;
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
        $this->reviewRepository=$reviewRepository;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {


        if ($this->loggedUser && $this->loggedUser->getRole() === Article::ROLE_AUTOR) {
            $articles = $this->articleRepository->findBy(['id_autor' => $this->loggedUser->getID()]);

            return $this->render('index/autor.twig',
                [
                    'user' => $this->loggedUser,
                    'articles' => $articles,
                ]
            );
        }

        if ($this->loggedUser && $this->loggedUser->getRole() === Article::ROLE_REDACTOR) {
            $form = $this->createForm(ReviewForm::class);
            $form->handleRequest($request);

            $articles = $this->articleRepository->findBy(['status' => Article::STATUS_NEW]);
            $waitingArticles = $this->articleRepository->findBy(['status' => Article::STATUS_IN_PROGRES]);
            $doneArticles=[];
            $dataReviews=[];
            foreach ($waitingArticles as $a){
                $dataReviews[$a->getId()]=$this->reviewRepository->findBy(['id_article'=>$a->getId()]);
            }

            if ($form->isSubmitted() && $form->isValid()) {
                $data=$form->getData();
                $entityManager = $this->getDoctrine()->getManager();

                $article= $this->articleRepository->find($data['id_article']);
                if(!$article){
                    $this->addFlash('error', 'Něco se pokazilo, zkuste to prosím později');
                    return $this->redirectToRoute('homepage');
                }
                $article->setStatus(Article::STATUS_IN_PROGRES);
                $entityManager->persist($article);

                foreach ($data['users'] as $user){
                    $review=new Review();
                    $review->setIdArticle($data['id_article']);
                    $review->setIdUser($user->getId());
                    $entityManager->persist($review);
                }
                $entityManager->flush();

                $this->addFlash('success', 'Recenzenti byli zadáni');
                return $this->redirectToRoute('homepage');
            }


            return $this->render('index/redactor.twig',
                [
                    'formObject'=>$form,
                    'user' => $this->loggedUser,
                    'articles' => $articles,
                    'waiting_articles'=>$waitingArticles,
                    'done_articles'=>$doneArticles,
                    'reviews'=>$dataReviews
                ]
            );

        }

        return $this->render('index/index.twig',
            [
                'user' => $this->loggedUser,
            ]
        );
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