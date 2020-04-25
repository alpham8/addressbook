<?php declare(strict_types=1);

namespace AddressBookBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OverviewController extends Controller
{
    /**
     * @Route("/", name="overview.index")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('overview/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }
}
