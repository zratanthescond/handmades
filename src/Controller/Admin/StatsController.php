<?php

namespace App\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

class StatsController extends AbstractController
{
    /**
     * @Route("/stats", name="stats")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(ChartBuilderInterface $chartBuilder, EntityManagerInterface $em): Response
    {
        $orders = $em->getRepository("App\Entity\Order")->findBy(array(), array('createdAt' => 'DESC'));
        $stats = array();
        foreach ($orders as $key => $value) {
            $prod = $value->getProducts();
            foreach ($prod as $k => $v) {
                $index = array_search($v->getProduct()->getId(), array_column($stats, 'id'));
                if ($index > -1) {

                    $stats[$index]['qty'] += $v->getQty();
                } else {

                    $stats[] = array("id" => $v->getProduct()->getId(), 'qty' => $v->getQty());
                }
            }
        }
        usort(
            $stats,
            fn (array $a, array $b): int => $b['qty'] <=> $a['qty']
        );

        $labels = array();
        $data = array();
        $product = $em->getRepository("App\Entity\Product");
        for ($i = 0; $i < 10; $i++) {
            $labels[] = $product->find($stats[$i]['id'])->getTitle();
            $data[] = $stats[$i]['qty'];
        }
        $chart = $chartBuilder->createChart(Chart::TYPE_DOUGHNUT);

        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'My First dataset',
                    'backgroundColor' => [
                        'rgb(127, 188, 210)',
                        'rgb(165, 241, 233)',
                        'rgb(225, 255, 238)',
                        'rgb(255, 238, 175)'
                    ],
                    'borderColor' => '#03cecb',
                    'data' => $data,
                ],
            ],
        ]);

        $chart->setOptions([
            'plugins' => [
                'title' => [
                    'display' => true,
                    'text' => 'Commandes par produits'
                ],
            ]
        ]);
        $ClientWoth = $this->clientsByBuys($orders, $em, $chartBuilder);

        return $this->render('Stats/index.html.twig', [
            'chart' => $chart,
            'productByMoney' => $this->productByMoney($stats, $chartBuilder, $em),
            'clientWorth' => $ClientWoth
        ]);
    }
    public function productByMoney(
        $dataset,
        $chartBuilder,
        $em
    ) {
        $product = $em->getRepository("App\Entity\Product");
        foreach ($dataset as $key => $value) {
            $dataset[$key]['money'] = $value['qty'] * $product->find($value['id'])->getPrice();
        }
        // dd($dataset);
        usort(
            $dataset,
            fn (array $a, array $b): int => $b['money'] <=> $a['money']
        );

        $labels = array();
        $data = array();

        for ($i = 0; $i < 10; $i++) {
            $labels[] = $product->find($dataset[$i]['id'])->getTitle();
            $data[] = $dataset[$i]['money'];
        }
        $chart = $chartBuilder->createChart(Chart::TYPE_DOUGHNUT);

        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'My First dataset',
                    'backgroundColor' => [
                        'rgb(127, 188, 210)',
                        'rgb(165, 241, 233)',
                        'rgb(225, 255, 238)',
                        'rgb(255, 238, 175)'
                    ],
                    'borderColor' => '#03cecb',
                    'data' => $data,
                ],
            ],
        ]);

        $chart->setOptions([
            'plugins' => [
                'title' => [
                    'display' => true,
                    'text' => 'Chiffre d \'affaire par produits'
                ],
            ]
        ]);
        return $chart;
    }
    public function clientsByBuys($orders, $em, $chartBuilder)
    {
        $stats = array();
        foreach ($orders as $key => $value) {
            $index = array_search(
                $value->getUser()->getId(),
                array_column($stats, 'ownerId')
            );

            if ($index > -1) {

                $stats[$index]['worth'] += $value->getTotal();
            } else {

                $stats[] = array(
                    "ownerId" => $value->getUser()->getId(),
                    'worth' => $value->getTotal()
                );
            }
        }
        usort(
            $stats,
            fn (array $a, array $b): int => $b['worth'] <=> $a['worth']
        );

        $labels = array();
        $data = array();
        $user = $em->getRepository("App\Entity\User");
        for ($i = 0; $i < 10; $i++) {
            $labels[] = $user->find($stats[$i]['ownerId'])->getFullName();
            $data[] = $stats[$i]['worth'];
        }
        // dd($labels);
        $chart = $chartBuilder->createChart(Chart::TYPE_BAR);

        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'client par achat',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => $data,
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 100,
                ],
            ],
        ]);
        //dd($stats);
        return $chart;
    }
}
