<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\FileInput;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class DropShippingController extends AbstractController
{
    /**
     * @Route("/test", name="test")
     */
    public function index(Request $request)
    {
        $form = $this->createForm(FileInput::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            /** @var UploadedFile|null $brochureFile */
            $brochureFile = $form->get('brochure')->getData();

            if ($brochureFile) {
                try {
                    $spreadsheet = IOFactory::load($brochureFile);
                    $spreadsheet->getActiveSheet()->removeRow(1); // Remove header
                    $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

                    $em = $this->getDoctrine()->getManager();
                    $products = $em->getRepository(Product::class)->findAll();

                    $updatedCount = 0;
                    $skipped = [];

                    foreach ($products as $product) {
                        // Make sure we find an exact match in column A
                        $key = array_search($product->getProvider(), array_column($sheetData, 'A'));

                        if ($key !== false && isset($sheetData[$key])) {
                            $newData = $sheetData[$key];

                            $price = (float) $newData['D'];
                            $margin = (float) $newData['E'];

                            $newPrice = $price + ($price * $margin / 100);
                            $newPrice += $newPrice * 0.20;
                            $newPrice = number_format($newPrice, 3, '.', '');

                            $product->setPrice($newPrice);
                            $product->setQty((int)$newData['C']);
                            $updatedCount++;

                            $em->persist($product);
                        } else {
                            $skipped[] = $product->getProvider();
                        }
                    }

                    $em->flush();

                    $this->addFlash('success', "$updatedCount products updated successfully.");
                    if (count($skipped) > 0) {
                        $this->addFlash('warning', "Skipped products (no matching provider in Excel): " . implode(', ', $skipped));
                    }
                } catch (\Throwable $e) {
                    $this->addFlash('danger', "Error reading Excel or updating DB: " . $e->getMessage());
                }
            } else {
                $this->addFlash('warning', "Please upload an Excel file.");
            }

            return $this->redirectToRoute('test'); // Prevent resubmission on refresh
        }

        return $this->renderForm('admin/test.html.twig', [
            'form' => $form,
        ]);
    }
}
