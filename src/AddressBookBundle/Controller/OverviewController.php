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
        /** @var LoggerInterface $logger */
        $logger = $this->container->get('logger');
        $data = ['success' => false, 'result' => -1];

        if ($this->checkCsrfToken($request)) {
            $adrMdl = new Addresses();
            $form = $this->createFormBuilder($adrMdl)
                ->add('firstname', TextType::class)
                ->add('lastname', TextType::class)
                ->add('streetNo', TextType::class)
                ->add('zip', TextType::class)
                ->add('city', TextType::class)
                ->add('countryIsoAlpha2', CountryType::class)
                ->add('phone', TextType::class)
                ->add('birthday', TextType::class)
                ->add('email', EmailType::class)
                ->add('pictureUrl', FileType::class)
                ->getForm();
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

            if (isset($_FILES['pictureUrl'])) {
                /** @var UploadedFile $picFile */
                $picFile = new UploadedFile(
                    $_FILES['pictureUrl']['tmp_name'],
                    $_FILES['pictureUrl']['name'],
                    $_FILES['type'],
                    $_FILES['size'],
                    $_FILES['error']
                );
                $originFilename = pathinfo($picFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originFilename);
                $newFilename = $safeFilename.'-'.uniqid('', true).'.'.$picFile->guessExtension();
                // Move the file to the directory where brochures are stored
                try {
                    $picFile->move(
                        $this->getParameter('kernel.project_dir') . '/web/contact_pics',
//                        $this->getParameter('addresbook_picture_directory'),
                        $newFilename
                    );
                    $data['success'] = true;
                    $data['result'] = 0;
                } catch (\Exception $e) {
                    $data['success'] = false;
                    $data['result'] = $e->getMessage();
                    $logger->error(
                        'OverviewController::saveContact picture move Exception: ' . $e->getMessage(),
                        ['class' => __CLASS__, 'methood' => __METHOD__]
                    );
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
                    $logger->error(
                        'OverviewController::saveContact entity persist / flush Exception: ' . $ex->getMessage(),
                        ['class' => __CLASS__, 'method' => __METHOD__]
                    );
                }
            }
        }

//        $adrMdl = new Addresses();

//        $form->handleRequest($request);
//        var_dump($form->isSubmitted());
//        var_dump($form->isValid());
//        var_dump($form->isSubmitted() && $form->isValid());
//        die('test');

//        if ($form->isSubmitted() && $form->isValid()) {
//
//        }

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
        $em = $this->container->get('doctrine.orm.entity_manager');
        /** @var AddressesRepository $repo */
        $repo = $em->getRepository(Addresses::class);
        $pagesCnt = $repo->getContactListPageCount();
        var_dump($pagesCnt);die('test');

        if ($pagesCnt >= $page) {
            $countries = new ISO3166();
            $contacts = $repo->getContactListQuery($page)->getResult(AbstractQuery::HYDRATE_ARRAY);
            var_dump($contacts);die('test');

            foreach ($contacts as $key => $contact) {
                foreach ($countries as $country) {
                    if ($contact['countryIsoAlpha2'] === $country[ISO3166::KEY_ALPHA2]) {
                        $contacts[$key]['country'] = $country[ISO3166::KEY_NAME];
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
