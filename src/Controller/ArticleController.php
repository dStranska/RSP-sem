<?php


namespace App\Controller;

use App\Entity\Article;
use App\Entity\ArticleFile;
use App\Entity\Review;
use App\Entity\User;
use App\Form\ArticleForm;
use App\Form\ReviewForm;
use App\Form\WriteReviewForm;
use App\Repository\ArticleFileRepository;
use App\Repository\ArticleRepository;
use App\Repository\ArticleThemeRepository;
use App\Repository\ReviewRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use http\Env\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends BaseController
{

    /** @var ArticleRepository */
    public $articleRepository;

    /** @var ArticleFileRepository */
    public $articleFileRepository;

    /** @var ArticleThemeRepository */
    public $articleThemeRepository;

    /** @var UserRepository */
    public $userRepository;

    /** @var ReviewRepository */
    public $reviewRepository;

    public function __construct(ArticleFileRepository $articleFileRepository,
                                ArticleRepository $articleRepository,
                                ArticleThemeRepository $articleThemeRepository,
                                UserRepository $userRepository,
                                ReviewRepository $reviewRepository)
    {
        parent::__construct();
        $this->articleFileRepository = $articleFileRepository;
        $this->articleRepository = $articleRepository;
        $this->articleThemeRepository = $articleThemeRepository;
        $this->userRepository = $userRepository;
        $this->reviewRepository = $reviewRepository;
    }

    /**
     * @Route("/add-article",name="add-article")
     */
    public function addAction(Request $request)
    {
        $form = $this->createForm(ArticleForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                //save article
                $article = new Article();
                $article->setName($data['name']);
                $article->setAuthorsName($data['authors_name']);
                $article->setTheme($data['theme']);
                $article->setAuthor($this->userRepository->find($this->loggedUser->getId()));

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($article);
                $entityManager->flush();

                $file = $data['file'];
                $folder = DIR_FILES . DS . $article->getId();
                $name = date('YmdHis') . '.' . $file->getClientOriginalExtension();


                if (!isset($data['file'])) {
                    throw new \Exception('File was not sent');
                }

                if (!is_dir($folder)) {
                    mkdir($folder);
                }

                if (!$file->move($folder, $name)) {
                    throw new \Exception('Cant save file');
                }

                $articleFile = new ArticleFile();
                $articleFile->setFullPath(DS . 'files' . DS . $article->getId() . DS . $name);
                $articleFile->setArticle($article);


                $entityManager->merge($articleFile);
                $entityManager->flush();

            } catch (\Exception $e) {
                $this->addFlash('error', 'Něco se pokazilo: ' . $e->getMessage());
            }

            $this->addFlash('success', 'Článek byl uložen');
            return $this->redirectToRoute('homepage');


        }
        return $this->render('article/add.twig', [
            'form' => $form->createView(),
            'user' => $this->loggedUser,
            'text' => 'Přidání nového článku'
        ]);

    }


    /**
     * @Route("article-files/{id}",name="files")
     * @param $id
     */
    public function filesAction($id)
    {
        /** @var Article $article */
        $article = $this->articleRepository->find($id);
        $article->getActualStatus();
        if (!$article) {
            $this->addFlash('error', 'Tento článek neexistuje');
            return $this->redirectToRoute('homepage');
        }
        if ($this->loggedUser->getRole() !== User::ROLE_REDACTOR && $article->getIdAutor() !== $this->loggedUser->getId()) {
            $this->addFlash('error', 'Tento článek není tvůj');
            return $this->redirectToRoute('homepage');
        }
        $files = $this->articleFileRepository->findBy(['article' => $id]);

        return $this->render('article/files.twig', [
            'files' => $files, 'user' => $this->loggedUser, 'article' => $article
        ]);
    }

    /**
     * @Route("add-review/{id}",name="add-review")
     */
    public function addReviewAction($id, Request $request)
    {
        $form = $this->createForm(WriteReviewForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $data = $form->getData();
                $review = $this->reviewRepository->find($id);
                if (!$review) {
                    throw new \Exception('Cant find review');
                }
                $review->setComment($data['comment']);
                $review->setScore($data['value']);
                $review->setApproved($data['approved']);
                $review->setStatus(Review::STATUS_DONE);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($review);
                $entityManager->flush();
                $this->articleRepository->setReviewDone($review->getArticle()->getId());

                $this->addFlash('success', 'Recenze úspěšně uložena');

            } catch (\Exception $e) {
                $this->addFlash('error', 'Neco se pokazilo: ' . $e->getMessage());
            }
            return $this->redirectToRoute('homepage');

        }
        return $this->render('article/add-review.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("clanek/{id}",name="detail-clanku")
     */
    public function detailAction($id, Request $request)
    {
        $article = $this->articleRepository->find($id);

        if ($this->loggedUser->getRole() != User::ROLE_REDACTOR && $this->loggedUser->getId()!= $article->getAuthor()->getId()) {

            $this->addFlash('error', 'Tento článek není tvůj');
            return $this->redirectToRoute('homepage');
        }
        $form = $this->createForm(ArticleForm::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try{
                /** @var Article $article */
                $article= $form->getData();
                $entityManager = $this->getDoctrine()->getManager();

                if($article->file){
                    $file = $article->file;
                    $folder = DIR_FILES . DS . $article->getId();
                    $name = date('YmdHis') . '.' . $file->getClientOriginalExtension();

                    if (!is_dir($folder)) {
                        mkdir($folder);
                    }

                    if (!$file->move($folder, $name)) {
                        throw new \Exception('Cant save file');
                    }

                    $articleFile = new ArticleFile();
                    $articleFile->setFullPath(DS . 'files' . DS . $article->getId() . DS . $name);
                    $articleFile->setArticle($article);

                    $entityManager->merge($articleFile);
                    $entityManager->flush();

                }
                $article->setStatus(Article::STATUS_REPAIR_DONE);
                $entityManager->persist($article);
                $entityManager->flush();
                $this->addFlash('success', 'Uspěšně upravený');
            }catch (\Exception $e){
                $this->addFlash('error', 'Něco se pokazilo: '.$e->getMessage());

            }
            return $this->redirectToRoute('homepage');


        }

        return $this->render('article/detail.twig', [
            'form' => $form->createView(),
            'text' => 'Úprava článku: ' . $article->getName(),
            'user' => $this->loggedUser
        ]);
    }

    /**
     *
     * @Route("themes",name="themes")
     */
    public function themesAction()
    {

        $themes = $this->articleThemeRepository->findAll();
        $all = [];
        foreach ($themes as $theme) {
            $t = [];
            $t['name'] = $theme->getName();
            $t['done'] = count($this->articleRepository->findBy(['theme' => $theme->getId(), 'status' => Article::STATUS_DONE]));
            $t['new'] = count($this->articleRepository->findBy(['theme' => $theme->getId(), 'status' => Article::STATUS_NEW]));
            $t['in_progress'] = count($this->articleRepository->findBy(['theme' => $theme->getId(), 'status' => [Article::STATUS_IN_PROGRESS, Article::STATUS_REPAIR, Article::STATUS_REPAIR_DONE,Article::STATUS_REVIEW_DONE]]));
            $t['total'] = count($this->articleRepository->findBy(['theme' => $theme->getId()]));

            $all[] = $t;
        }

        return $this->render('article/themes.twig', [
            'themes' => $all,
            'user' => $this->loggedUser
        ]);
    }

    /**
     * @param int $id
     * @Route("approve-article/{id}", name="approve-article")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function approveAction(int $id)
    {
        try{
            /** @var Article $article */
            $article=$this->articleRepository->find($id);
            if(!$article){
                throw new \Exception('Cant find article');
            }
            if($article->getStatus()==Article::STATUS_DONE){
                throw new \Exception('Article is already done');
            }

            $article->setStatus(Article::STATUS_DONE);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();
            $this->addFlash('success', 'Článek označen jako "schválený"');
        }catch (\Exception $e){
            $this->addFlash('error', 'Neco se pokazilo: ' . $e->getMessage());
        }
        return $this->redirectToRoute('homepage');

    }

    /**
     * @param int $id
     * @Route("decline-article/{id}", name="decline-article")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function declineAction(int $id)
    {
        try{
            /** @var Article $article */
            $article=$this->articleRepository->find($id);
            if(!$article){
                throw new \Exception('Cant find article');
            }
            if($article->getStatus()==Article::STATUS_DECLINE){
                throw new \Exception('Article is already decline');
            }

            $article->setStatus(Article::STATUS_DECLINE);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();
            $this->addFlash('success', 'Článek označen jako "zamítnutý"');
        }catch (\Exception $e){
            $this->addFlash('error', 'Neco se pokazilo: ' . $e->getMessage());
        }
        return $this->redirectToRoute('homepage');

    }

    /**
     * @param int $id
     * @Route("set-to-edit/{id}", name="set-to-edit")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function toEditAction(int $id)
    {
        try{
            /** @var Article $article */
            $article=$this->articleRepository->find($id);
            if(!$article){
                throw new \Exception('Cant find article');
            }
            if($article->getStatus()==Article::STATUS_REPAIR){
                throw new \Exception('Article is already in repair');
            }

            $article->setStatus(Article::STATUS_REPAIR);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();
            $this->addFlash('success', 'Článek označen jako "na opravu"');
        }catch (\Exception $e){
            $this->addFlash('error', 'Neco se pokazilo: ' . $e->getMessage());
        }
        return $this->redirectToRoute('homepage');

    }
    /**
     * @param int $id
     * @Route("archive/{id}", name="archive")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function archiveAction(int $id)
    {
        try{
            /** @var Article $article */
            $article=$this->articleRepository->find($id);
            if(!$article){
                throw new \Exception('Cant find article');
            }
            if($article->getStatus()==Article::STATUS_ARCHIVED){
                throw new \Exception('Article is already archive');
            }

            $article->setStatus(Article::STATUS_ARCHIVED);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();
            $this->addFlash('success', 'Článek označen jako "archivovaný"');
        }catch (\Exception $e){
            $this->addFlash('error', 'Neco se pokazilo: ' . $e->getMessage());
        }
        return $this->redirectToRoute('homepage');

    }


}