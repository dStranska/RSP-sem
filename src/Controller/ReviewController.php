<?php


namespace App\Controller;


use App\Entity\Review;
use App\Entity\User;
use App\Form\ArticleForm;
use App\Repository\ArticleRepository;
use App\Repository\ReviewRepository;
use Symfony\Component\Routing\Annotation\Route;

class ReviewController extends BaseController
{

    /** @var ArticleRepository */
    public $articleRepository;

    /** @var ReviewRepository */
    public $reviewsRepository;


    public function __construct(ArticleRepository $articleRepository,ReviewRepository $reviewRepository)
    {
        parent::__construct();
        $this->articleRepository=$articleRepository;
        $this->reviewsRepository=$reviewRepository;
    }


    /**
     * @Route("/recenze/{id}", name="recenze")
     */
    public function indexAction($id)
    {
        $article=$this->articleRepository->find($id);

        if($this->loggedUser->getRole()!=User::ROLE_RECENZENT && $article->getIdAutor()!= $this->loggedUser->getId()){

            $this->addFlash('error', 'Tento článek není tvůj');
            return $this->redirectToRoute('homepage');
        }
        $reviews=$this->reviewsRepository->findBy(['id_article'=>$id]);

        return $this->render('review/index.twig',
            [
                'reviews' => $reviews,
                'a'=>$article,
                'user'=>$this->loggedUser
            ]
        );
    }

    /**
     * @Route("/admin-recenze/{id}", name="admin-recenze")
     */
    public function recenzeAction($id)
    {
        $article=$this->articleRepository->find($id);
        if($this->loggedUser->getRole()!=User::ROLE_REDACTOR){
            $this->addFlash('error', 'Sem nemas přístup');
            return $this->redirectToRoute('homepage');
        }
        $reviews=$this->reviewsRepository->findBy(['article'=>$id,'status'=>Review::STATUS_DONE]);


        return $this->render('review/admin-review.twig',
            [
                'reviews' => $reviews,
                'article'=>$article,
                'user'=>$this->loggedUser
            ]
        );
    }
    /**
     * @Route("/author-recenze/{id}", name="author-recenze")
     */
    public function authorRecenzeAction($id)
    {
        $article=$this->articleRepository->find($id);
        if($this->loggedUser->getId()!=$article->getAuthor()->getId()){
            $this->addFlash('error', 'Sem nemas přístup');
            return $this->redirectToRoute('homepage');
        }
        $reviews=$this->reviewsRepository->findBy(['article'=>$id,'status'=>Review::STATUS_DONE]);


        return $this->render('review/author-review.twig',
            [
                'reviews' => $reviews,
                'article'=>$article,
                'user'=>$this->loggedUser
            ]
        );
    }
}