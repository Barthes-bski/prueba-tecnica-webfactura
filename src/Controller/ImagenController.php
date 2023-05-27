<?php

namespace App\Controller;

use App\Entity\Imagen;
use App\Form\ImagenType;
use App\Form\ImportFormType;
use App\Repository\ImagenRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/imagen")
 */
class ImagenController extends AbstractController
{
    /**
     * @Route("/", name="app_imagen_index", methods={"GET"})
     */
    public function index(ImagenRepository $imagenRepository): Response
    {
        return $this->render('imagen/index.html.twig', [
            'imagens' => $imagenRepository->imagenesHabilitadas(),
            'csv' => $imagenRepository->leerArchivoCsv()
        ]);
    }

    /**
     * @Route("/no-habilitadas", name="app_imagen_no_habilitadas", methods={"GET"})
     */
    public function imagenNoHabilitadas(ImagenRepository $imagenRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('imagen/no_habilitadas.html.twig', [
            'imagens' => $imagenRepository->imagenesNoHabilitadas(),
        ]);
    }

    /**
     * @Route("/new", name="app_imagen_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ImagenRepository $imagenRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $imagen = new Imagen();
        $form = $this->createForm(ImagenType::class, $imagen);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imagenRepository->add($imagen, true);

            return $this->redirectToRoute('app_imagen_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('imagen/new.html.twig', [
            'imagen' => $imagen,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/import", name="app_imagen_import", methods={"GET", "POST"})
     */
    public function import(Request $request, ImagenRepository $imagenRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $form = $this->createForm(ImportFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('archive')->getData();
            if ($file) {
                $csvFilePath = $file->getPathname();

                // Lógica para leer el archivo CSV y procesar los datos
                $imagenRepository->importarArchivoCsv($csvFilePath);

                return $this->redirectToRoute('app_imagen_index', [], Response::HTTP_SEE_OTHER);
            }

        }

        return $this->renderForm('imagen/importar_archivo.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_imagen_show", methods={"GET"})
     */
    public function show(Imagen $imagen): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('imagen/show.html.twig', [
            'imagen' => $imagen,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_imagen_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Imagen $imagen, ImagenRepository $imagenRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $form = $this->createForm(ImagenType::class, $imagen);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imagenRepository->add($imagen, true);

            return $this->redirectToRoute('app_imagen_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('imagen/edit.html.twig', [
            'imagen' => $imagen,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/todos-aprobados", name="app_imagen_aprobados", methods={"POST"})
     */
    public function aprobarTodos(Request $request, ImagenRepository $imagenRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($this->isCsrfTokenValid('aprobar', $request->request->get('_token'))) {
            $imagenes = $imagenRepository->imagenesNoHabilitadas();
            foreach ($imagenes as $item) {
                $item->setStatus(true);
                $imagenRepository->add($item, true);
            }

        }

        return $this->redirectToRoute('app_imagen_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}", name="app_imagen_delete", methods={"POST"})
     */
    public function delete(Request $request, Imagen $imagen, ImagenRepository $imagenRepository, Security $security): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($this->isCsrfTokenValid('delete'.$imagen->getId(), $request->request->get('_token'))) {
            $imagenRepository->remove($imagen, true, $security);
        }

        return $this->redirectToRoute('app_imagen_index', [], Response::HTTP_SEE_OTHER);
    }
}
