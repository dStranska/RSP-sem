<?php


namespace App\Controller;




use Symfony\Component\Routing\Annotation\Route;

class MagazineController extends BaseController
{
    /**
     * @Route("magazine", name="magazine")
     */
    public function archiveAction()
    {
        return $this->render('magazine/archive.twig',
            [
                'user' => $this->loggedUser,
            ]);
    }
}