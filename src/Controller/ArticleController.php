<?php


namespace App\Controller;

use App\Entity\Article;
use App\Entity\ArticleFile;
use App\Entity\User;
use App\Form\ArticleForm;
use App\Form\ReviewForm;
use App\Repository\ArticleFileRepository;
use App\Repository\ArticleRepository;
use App\Repository\ArticleThemeRepository;
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

    public function __construct(ArticleFileRepository $articleFileRepository, ArticleRepository $articleRepository, ArticleThemeRepository $articleThemeRepository)
    {
        parent::__construct();
        $this->articleFileRepository = $articleFileRepository;
        $this->articleRepository = $articleRepository;
        $this->articleThemeRepository = $articleThemeRepository;
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

            $article = new Article();
            $article->setName($data['name']);
            $article->setAutorsName($data['autors_name']);
            $article->setIdAutor($this->loggedUser->getId());
            $article->setIdTheme($data['theme']->getID());
            $article->setTheme($data['theme']->getName());
            $article->setComment($data['comment']);
            if (!isset($data['file'])) {
                $this->addFlash('error', 'Něco se pokazilo, zkuste to prosím později');
                return $this->redirectToRoute('homepage');
            }
            $file = $data['file'];

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            $folder = DIR_FILES . DS . $article->getId();
            $name = date('YmdHis') . '.' . $file->getClientOriginalExtension();

            if (!is_dir($folder)) {
                mkdir($folder);
            }

            if (!$file->move($folder, $name)) {
                $this->addFlash('error', 'Něco se pokazilo, zkuste to prosím později');
                return $this->redirectToRoute('homepage');
            }

            $articleFile = new ArticleFile();
            $articleFile->setIdArticle($article->getId());
            $articleFile->setFullPath(DS . 'files' . DS . $article->getId() . DS . $name);
            $entityManager->persist($articleFile);
            $entityManager->flush();

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
        if ($this->loggedUser->getRole() !== Article::ROLE_REDACTOR && $article->getIdAutor() !== $this->loggedUser->getId()) {
            $this->addFlash('error', 'Tento článek není tvůj');
            return $this->redirectToRoute('homepage');
        }
        $files = $this->articleFileRepository->findBy(['id_article' => $id]);

        return $this->render('article/files.twig', [
            'files' => $files, 'user' => $this->loggedUser, 'article' => $article
        ]);
    }

    /**
     * @Route("add-review/{id}",name="add-review")
     */
    public function addReviewAction($id, Request $request)
    {
        $form = $this->createForm(ReviewForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            echo 'd';
            $data = $form->getData();

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

        if ($this->loggedUser->getRole() != User::ROLE_RECENZENT && $article->getIdAutor() != $this->loggedUser->getId()) {

            $this->addFlash('error', 'Tento článek není tvůj');
            return $this->redirectToRoute('homepage');
        }
        $form = $this->createForm(ArticleForm::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dd($form->getData());
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

        $themes =$this->articleThemeRepository->findAll();
        $all=[];
        foreach ($themes as $theme){
            $t=[];
            $t['name']=$theme->getName();
            $t['done']=count($this->articleRepository->findBy(['id_theme'=>$theme->getId(),'status'=>Article::STATUS_SUCCESS]));
            $t['new']=count($this->articleRepository->findBy(['id_theme'=>$theme->getId(),'status'=>Article::STATUS_NEW]));
            $t['in_progress']=count($this->articleRepository->findBy(['id_theme'=>$theme->getId(),'status'=> [Article::STATUS_IN_PROGRES,Article::STATUS_REPAIR,Article::STATUS_RE_DONE] ]));
            $t['total']=count($this->articleRepository->findBy(['id_theme'=>$theme->getId()]));

            $all[]=$t;
        }

        return $this->render('article/themes.twig', [
            'themes'=>$all,
            'user' => $this->loggedUser
        ]);
    }

}