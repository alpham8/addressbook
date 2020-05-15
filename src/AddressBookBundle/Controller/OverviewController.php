<?php

namespace AddressBookBundle\Controller;

use AddressBookBundle\Entity\Addresses;
use AddressBookBundle\Repository\AddressesRepository;
use Doctrine\ORM\AbstractQuery;
use League\ISO3166\ISO3166;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
    public function getCountriesAction(Request $request)
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
    public function saveContactAction(Request $request)
    {
        /** @var LoggerInterface $logger */
        $logger = $this->container->get('logger');
        $data = ['success' => false, 'result' => ''];

        if ($this->checkCsrfToken($request)) {
            $em = $this->getDoctrine()->getManager();
            $repo = $em->getRepository(Addresses::class);
            $id = intval($request->get('contactId', -1));

            if ($id !== -1) {
                $adrMdl = $repo->findOneBy(['id' => $id]);
            }

            $adrMdl = new Addresses();
            $adrMdl->setFirstname($request->get('firstname', null));
            $adrMdl->setLastname($request->get('lastname', null));
            $adrMdl->setStreetNo($request->get('streetNo', null));
            $adrMdl->setZip($request->get('zip', null));
            $adrMdl->setCity($request->get('city', null));
            $adrMdl->setCountryIsoAlpha2($request->get('country', null));
            $adrMdl->setPhone($request->get('phone', null));

            if (($birthday = $request->get('birthday', null)) !== null) {
                $adrMdl->setBirthday(new \DateTimeImmutable($birthday));
            } else {
                throw new \RuntimeException('No birthday field given.');
            }
            $adrMdl->setEmail($request->get('email', null));

            // check if file is set, have a size and is an image
            if (isset(
                $_FILES['pictureUrl']) &&
                $_FILES['pictureUrl']['size'] > 0
                && getimagesize($_FILES["fileToUpload"]["tmp_name"]) !== false
            ) {
                // delete old picture on override
                if ($id !== -1 && !empty($adrMdl->getPictureUrl())) {
                    unlink($adrMdl->getPictureUrl());
                }
                /** @var UploadedFile $picFile */
                $picFile = new UploadedFile(
                    $_FILES['pictureUrl']['tmp_name'],
                    $_FILES['pictureUrl']['name'],
                    $_FILES['pictureUrl']['type'],
                    $_FILES['pictureUrl']['size'],
                    $_FILES['pictureUrl']['error']
                );
                $originFilename = pathinfo($picFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate(
                    'Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()',
                    $originFilename
                );
                $newFilename = $safeFilename . '-' . uniqid('', true) . '.' . $picFile->guessExtension();
                // Move the file to the directory where brochures are stored
                try {
                    $picFile->move(
                        $this->getParameter('kernel.project_dir') . '/web/contact_pics',
                        $newFilename
                    );
                } catch (\Exception $e) {
                    $data['success'] = false;
                    $data['result'] = $e->getMessage();
                    $logger->error(
                        'OverviewController::saveContact picture move Exception: ' . $e->getMessage(),
                        ['class' => __CLASS__, 'methood' => __METHOD__]
                    );
                } finally {
                    $router = $this->container->get('router');
                    if ($data['success']) {
                        // updates the 'pictureUrl' property to store the image file name
                        // instead of its contents
                        $adrMdl->setPictureUrl(
                            $router->generate('addressbook.overview.index', [], UrlGeneratorInterface::ABSOLUTE_URL)
                            . 'contact_pics/' . $newFilename
                        );
                    }
                }
            }

            try {
                $em->persist($adrMdl);
                $em->flush();
                if (empty($data['result'])) {
                    $data['success'] = true;
                    $data['result'] = '';
                }
            } catch (\Exception $ex) {
                $logger->error(
                    'OverviewController::saveContact entity persist / flush Exception: ' . $ex->getMessage(),
                    ['class' => __CLASS__, 'method' => __METHOD__]
                );
                $data['success'] = false;
                $data['result'] = $ex->getMessage();
            }
        }

        return new JsonResponse($data);
    }

    /**
     * @Route(
     *     "/overview/getList",
     *     name="addressbook.overview.get_list",
     *     options={"seo"="false"},
     *     methods={"GET"},
     *     defaults={"csrf_protected"=false, "XmlHttpRequest"=true}
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getListAction(Request $request)
    {
        $contacts = [];
        $page = intval($request->get('page', 1));
        $em = $this->container->get('doctrine.orm.entity_manager');
        /** @var AddressesRepository $repo */
        $repo = $em->getRepository(Addresses::class);
        $pagesCnt = $repo->getContactListPageCount();

        if ($pagesCnt >= $page) {
            $countries = new ISO3166();
            $contacts = $repo->getContactListQuery($page)->getResult(AbstractQuery::HYDRATE_ARRAY);

            foreach ($contacts as $key => $contact) {
                foreach ($countries as $country) {
                    if ($contact['countryIsoAlpha2'] === $country[ISO3166::KEY_ALPHA2]) {
                        $contacts[$key]['country'] = $country[ISO3166::KEY_NAME];
                        $contacts[$key]['birthday'] = $contact['birthday']->format('Y-m-d');
                        break;
                    }
                }
            }
        }

        return new JsonResponse(
            [
                'contacts' => $contacts
            ]
        );
    }

    /**
     * @Route(
     *     "/overview/deleteContact",
     *     name="addressbook.overview.delete_contact",
     *     options={"seo"="false"},
     *     methods={"POST", "PUT"},
     *     defaults={"csrf_protected"=false, "XmlHttpRequest"=true}
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function deleteContactAction(Request $request)
    {
        $data = ['success' => false, 'result' => ''];

        $logger = $this->container->get('logger');
        $em = $this->container->get('doctrine.orm.entity_manager');
        /** @var AddressesRepository $repo */
        $repo = $em->getRepository(Addresses::class);
        try {
            $repo->deleteContact(intval($request->get('id', 0)));
            $data['success'] = true;
        } catch (\Exception $ex) {
            $data['success'] = false;
            $data['result'] = $ex->getMessage();
            $logger->error(
                'OverviewController::saveContact picture move Exception: ' . $ex->getMessage(),
                ['class' => __CLASS__, 'methood' => __METHOD__]
            );
        }

        return new JsonResponse($data);
    }

    /**
     * @Route(
     *     "/overview/getPageCount",
     *     name="addressbook.overview.get_page_count",
     *     options={"seo"="false"},
     *     methods={"GET"},
     *     defaults={"csrf_protected"=false, "XmlHttpRequest"=true}
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getPageCountAction(Request $request)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        /** @var AddressesRepository $repo */
        $repo = $em->getRepository(Addresses::class);

        return new JsonResponse(
            ['pages' => $repo->getContactListPageCount()]
        );
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
