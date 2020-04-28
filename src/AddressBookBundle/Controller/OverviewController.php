<?php

namespace AddressBookBundle\Controller;

use League\ISO3166\ISO3166;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OverviewController extends Controller
{
    /**
     * @Route("/", name="addressbook.overview.index", methods={"GET"})
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('overview/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route(
     *     "/overview/getCountries",
     *     name="addressbook.overview.get_countries",
     *     options={"seo"="false"},
     *     methods={"GET"},
     *     defaults={"csrf_protected"=false, "XmlHttpRequest"=true}
     * )
     *
     * @param Request $request
     */
    public function getCountries(Request $request)
    {
        $data = [];
        $countries = new ISO3166();

        foreach ($countries as $country) {
            $data[] = [
                'isoAlpha2' => $country[ISO3166::KEY_ALPHA2],
                'name' => $country[ISO3166::KEY_NAME]
            ];
        }

        return new JsonResponse($data);
    }
}
