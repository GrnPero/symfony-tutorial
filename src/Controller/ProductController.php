<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\ProductType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

final class ProductController extends AbstractController
{
    public function __construct(
        private ProductRepository $repository,
        private EntityManagerInterface $manager,
    ) {}

    #[Route('/products', name: 'product_index')]
    public function index(): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $this->repository->findAll(),
        ]);
    }

    #[Route('/products/{id<\d+>}', 'product_show')]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/products/new', name: 'product_new')]
    public function new(Request $request): Response
    {
        $product = new Product;

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($product);

            $this->manager->flush();

            $this->addFlash('notice', 'Product created successfully');

            return $this->redirectToRoute('product_show', [
                'id' => $product->getId(),
            ]);
        }

        return $this->render('product/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/product/{id<\d+>}/edit', name: 'product_edit')]
    public function edit(Product $product, Request $request): Response
    {
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->flush();

            $this->addFlash('notice', 'Product updated successfully');

            return $this->redirectToRoute('product_show', [
                'id' => $product->getId(),
            ]);
        }

        return $this->render('product/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/product/{id<\d+>}/delete', name: 'product_delete')]
    public function delete(Request $request, Product $product): Response
    {
        if ($request->isMethod('POST')) {
            $this->manager->remove($product);

            $this->manager->flush();

            $this->addFlash(
                'notice',
                'Product deleted successfully!'
            );

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/delete.html.twig', [
            'id' => $product->getId(),
        ]);
    }
}
