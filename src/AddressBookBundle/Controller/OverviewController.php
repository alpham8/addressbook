<?php

namespace AddressBookBundle\Controller;

use AddressBookBundle\Entity\Addresses;
use League\ISO3166\ISO3166;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OverviewController extends Controller
{
    const CSRF_TOKEN_KEY = '1';

    /**
     * @Route("/", name="addressbook.overview.index", methods={"GET"})
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('@AddressBook/overview/index.html.twig', [
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

    /**
     * @Route(
     *     "/overview/saveContact",
     *     name="addressbook.overview.save_contact",
     *     options={"seo"="false"},
     *     methods={"POST", "PUT"},
     *     defaults={"csrf_protected"=false, "XmlHttpRequest"=true}
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function saveContact(Request $request)
    {
        $data = ['success' => false, 'result' => -1];

//        if ($this->checkCsrfToken($request)) {
//            $adrMdl = new Addresses();
//            $adrMdl->setFirstname($request->get('tfFirstname', null));
//            $adrMdl->setLastname($request->get('tfLastname', null));
//            $adrMdl->setStreetNo($request->get('tfStreetNo', null));
//            $adrMdl->setZip($request->get('tfZip', null));
//            $adrMdl->setCity($request->get('tfCity', null));
//            $adrMdl->setCountryIsoAlpha2($request->get('pdCountry', null));
//            $adrMdl->setPhone($request->get('tfPhone', null));
//            if (($birthday = $request->get('tfBirthday', null)) !== null) {
//                $adrMdl->setBirthday(new \DateTimeImmutable($birthday));
//            } else {
//                throw new \RuntimeException('No birthday field given.');
//            }
//            $adrMdl->setEmail($request->get('tfEmail', null));
//        }

        $adrMdl = new Addresses();
        $form = $this->createForm(Addresses::class, $adrMdl);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $picFile */
            $picFile = $form->get('tfPicture')->getData();

            if ($picFile) {
                $originFilename = pathinfo($picFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originFilename);
                $newFilename = $safeFilename.'-'.uniqid('', true).'.'.$picFile->guessExtension();
                // Move the file to the directory where brochures are stored
                try {
                    $picFile->move(
                        $this->getParameter('addresbook_picture_directory'),
                        $newFilename
                    );
                } catch (\Exception $e) {
                    $data['success'] = false;
                    $data['result'] = $e->getMessage();
                } finally {
                    if ($data['success']) {
                        // updates the 'pictureUrl' property to store the image file name
                        // instead of its contents
                        $adrMdl->setPictureUrl($newFilename);
                    }
                }

                try {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($adrMdl);
                    $em->flush();
                } catch (\Exception $ex) {
                    // TODO: Loggen
                    $this->container->get('logger')->error(
                        'OverviewController::saveContact Exception: ' . $ex->getMessage(),
                        ['class' => __CLASS__, 'method' => __METHOD__]
                    );
                }
            }
        }

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     * @return bool
     */
    private function checkCsrfToken(Request $request)
    {
        $token = $request->request->get('token');

        return $this->isCsrfTokenValid(self::CSRF_TOKEN_KEY, $token);
    }
}
