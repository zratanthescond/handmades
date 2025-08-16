<?php

namespace App\Controller\Admin\Crud;

use App\Core\Security\Permission\UserRoles;
use App\Entity\Order;
use App\Entity\OrderSpecialDiscount;
use App\Entity\UserAddress;
use App\Form\OrderSpecialDiscountType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;

class OrderCrudController extends AbstractCrudController
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public static function getEntityFqcn(): string
    {
        return Order::class;
    }


    /****
     * filter by category for order
     */
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new("products"), "Produit")
            ->add(DateTimeFilter::new('createdAt'), 'Date')
            ->add(NumericFilter::new('id'), "Numero");
        // ->add("type")
        // ->add(NullFilter::new("price", "Prix")->setChoiceLabels("Sans Prix", "Avec prix"))
        // ->add(NullFilter::new("qty", "Quantité")->setChoiceLabels("Sans quantité", "Avec quantité"))
        // ->add(EmptyCollectionFilter::new("images", "Avec images"))
        // ->add(TextFilter::new("description"))
        // ->add(TextFilter::new("ref", "Référence"));
    }

    public function configureActions(Actions $actions): Actions
    {
        $exportCVS = Action::new('export CVS', 'export CVS', 'fa fa-file-invoice')
            ->linkToCrudAction('renderInvoice')->setCssClass('btn btn-primary action-foo')->createAsGlobalAction();

        $actions->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::NEW, Action::EDIT);

        $actions->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
            return $action->displayIf(function (Order $order) {;

                $payment = $order->getPayementTransaction();

                $shipment = $order->getAramexShipement();

                if ($shipment) {

                    return false;
                }

                if ($payment && !!$payment->getData() && count($payment->getData())) {

                    return false;
                }

                return true;
            });
        });

        return
            $actions->add(Crud::PAGE_INDEX, $exportCVS);
    }
    public function renderInvoice(EntityManagerInterface $em)
    {
        $parrainage = $em->getRepository("App\Entity\Order")->findBy(array(), array('createdAt' => 'DESC'));
        // dd($parrainage);
        // $data = $parrainage->iterate();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Parrainage exports');
        $columnsMap = [];
        $lineIndex = 2;
        foreach ($parrainage as $line) {
            //   dd($line);
            $paymentStatus = '';
            $payment = $line->getPayementTransaction();

            if ($payment) {

                $dt = $payment->getdata();

                if (count($dt) > 0) {

                    if ($dt["TransStatus"] == 00) {

                        $paymentStatus = 'Accordé';
                    } else {

                        $paymentStatus = 'Echoué';
                    }
                } else {

                    $paymentStatus = 'Echoué';
                }
            } else {

                $paymentStatus = "à la livraison";
            }
            $data['ID'] = (int) $line->getId();
            $data['Date'] = $line->getCreatedAt();
            $data['Client'] = $line->getUser()->getFullName();
            $data['Produits'] = count($line->getProducts());
            $data['Sous-Total'] = $line->getSubtotal();
            $data['Total'] = $line->getTotal();
            $data['Paiment'] = $paymentStatus;
            $data['Livraison'] = $line->getAramexShipement();
            // dd($data);
            foreach ($data as $columnName => $columnValue) {
                if (is_int($columnIndex = array_search($columnName, $columnsMap))) {
                    //   dd($columnName);
                    $columnIndex++;
                } else {

                    $columnsMap[] = $columnName;
                    $columnIndex = count($columnsMap);
                }
                // echo "<br>";
                // print_r($columnValue);
                // echo "<br>";
                $sheet->getCellByColumnAndRow($columnIndex, $lineIndex)->setValue($columnValue);
            }
            $lineIndex++;
        }
        foreach ($columnsMap as $columnMapId => $columnTitle) {
            $sheet->getCellByColumnAndRow($columnMapId + 1, 1)->setValue($columnTitle);
        }
        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $excelOutput = ob_get_clean();

        return new Response(
            $excelOutput,
            200,
            [
                'content-type'        =>  'text/x-csv; charset=windows-1251',
                'Content-Disposition' => 'attachment; filename="price.xlsx"'
            ]
        );
    }
    public function detail(AdminContext $context)
    {

        $order = $context->getEntity()->getInstance();

        $shippement = $order->getAramexShipement();

        $client = $order->getUser();

        $addresses = array_filter($client->getAddresses()->toArray(), function (UserAddress $address) {

            return $address->getIsDefault();
        });

        $defaultAddress = count($addresses) ? $addresses[0] : null;

        $specialDiscount = new OrderSpecialDiscount();

        $specialDiscountForm = $this->createForm(OrderSpecialDiscountType::class, $specialDiscount);

        $request = $context->getRequest();

        $specialDiscountForm->handleRequest($request);

        $prevTotal = $order->getTotal();

        if ($specialDiscountForm->isSubmitted() && $specialDiscountForm->isValid()) {

            $discountValue = $specialDiscount->getValue();

            if ($prevTotal > $discountValue) {

                $specialDiscount->setCreator($this->getUser());

                $newTotal = $prevTotal - $discountValue;

                $order->setTotal($newTotal)->addSpecialDiscount($specialDiscount);

                $this->em->persist($order);

                $this->em->flush();

                $this->addFlash("success", "Réduction appliquée");
            } else {

                $this->addFlash("danger", "La valeur de la commande est supérieure à la promotion");
            }

            return $this->redirect($request->getUri());
        }

        $specialDiscounts = $order->getSpecialDiscounts()->toArray();

        $specialDiscountsValue = 0;

        foreach ($specialDiscounts as $v) {
            $specialDiscountsValue += $v->getValue();
        }

        return $this->render("dashboard/order/order_details.html.twig", [

            "order" => $order,
            "specialDiscounts" => $specialDiscounts,
            "specialDiscountsValue" => $specialDiscountsValue,
            "shippement" => $shippement,
            "defaultAddress" => $defaultAddress,
            "specialDiscountForm" => $specialDiscountForm->createView(),
        ]);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInPlural("Commandes")
            ->setEntityLabelInSingular("Commande")
            ->setDefaultSort(["createdAt" => "DESC"])
            ->setEntityPermission(UserRoles::SUPER_ADMIN);
    }


    public function configureFields(string $pageName): iterable
    {
        return [

            IntegerField::new("id")->onlyOnIndex(),
            DateTimeField::new("createdAt", "Date"),
            AssociationField::new("user", "Client")->onlyOnIndex(),
            CollectionField::new("products", "Produits")->onlyOnIndex(),
            MoneyField::new("subtotal", "Sous-total")->setCurrency("TND")->setNumDecimals(3)->setStoredAsCents(false),
            MoneyField::new("total")->setCurrency("TND")->setNumDecimals(3)->setStoredAsCents(false),
            AssociationField::new("payementTransaction", "Paiement")->formatValue(function ($v, Order $order) {

                $payment = $order->getPayementTransaction();

                if ($payment) {

                    $data = $payment->getdata();

                    if (count($data) > 0) {

                        if ($data["TransStatus"] == 00) {

                            return '<span class="badge badge-success">Accordé</span>';
                        } else {

                            return '<span class="badge badge-danger">Echoué</span>';
                        }
                    } else {

                        return '<span class="badge badge-danger">Echoué</span>';
                    }
                } else {

                    return "<span>à la livraison</span>";
                }
            })->onlyOnIndex(),

            AssociationField::new("aramexShipement", "Livraison")->onlyOnIndex(),

        ];
    }
}
