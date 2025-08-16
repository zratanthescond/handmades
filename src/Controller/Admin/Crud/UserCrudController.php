<?php

namespace App\Controller\Admin\Crud;

use App\Core\Security\Permission\UserRoles;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Response;

class UserCrudController extends AbstractCrudController
{

    private $adminUrlGenerator;

    public function __construct(AdminUrlGenerator $adminUrlGenerator)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInPlural("Clients")
            ->setEntityLabelInSingular("Client")
            ->setDefaultSort(["createdAt" => "DESC"]);
    }

    public function configureActions(Actions $actions): Actions
    {
        $exportCVS = Action::new('export CVS', 'export CVS', 'fa fa-file-invoice')
            ->linkToCrudAction('renderInvoice')->setCssClass('btn btn-primary action-foo')->createAsGlobalAction();
        return $actions->disable(Action::DELETE, Action::NEW)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)->add(Crud::PAGE_INDEX, $exportCVS);
    }


    public function renderInvoice(EntityManagerInterface $em)
    {
        $parrainage = $em->getRepository("App\Entity\User")->findBy(array(), array('createdAt' => 'DESC'));
        // dd($parrainage);
        // $data = $parrainage->iterate();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Parrainage exports');
        $columnsMap = [];
        $lineIndex = 2;
        foreach ($parrainage as $line) {
            //   dd($line);

            $data['ID'] = (int) $line->getId();
            $data["Date d'inscription"] = $line->getCreatedAt();
            $data['Email'] = $line->getEmail();
            $data['Nom et Prénom'] = $line->getFullName();
            $data['Numéro du téléphone'] = $line->getPhoneNumber();
            $data['Date de naissance'] = $line->getBirthDay();
            $data['Points de fidélité'] = $line->getRewardPoints();

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

        $client = $context->getEntity()->getInstance();

        $addNewAdressUrl = $this->adminUrlGenerator->setAll([
            "crudAction" => Action::NEW,
            "entityId" => null,
            "userId" => $client->getId(),
            "crudControllerFqcn" => UserAddressCrudController::class
        ])->generateUrl();

        return $this->render("dashboard/client/details.html.twig", [
            "client" => $client,
            "addNewAdressUrl" => $addNewAdressUrl
        ]);
    }

    public function configureFields(string $pageName): iterable
    {
        $user = $this->getUser();

        $disabled = in_array(UserRoles::SUPER_ADMIN, $user->getRoles()) ? false : true;

        return [

            IntegerField::new("id")->setFormTypeOptions(["disabled" => true])->setColumns(6),
            DateField::new("createdAt", "Date d'inscription")->setFormTypeOptions(["disabled" => true])->setColumns(6),
            EmailField::new("email")->setFormTypeOptions(["disabled" => true])->setColumns(4),
            TextField::new("fullName", "Nom et prénom")->onlyOnIndex(),
            TextField::new("firstName", "Nom")->onlyOnForms()->setColumns(4),
            TextField::new("lastName", "Prénom")->onlyOnForms()->setColumns(4),
            TextField::new("phoneNumber", "Numéro de téléphone")->setColumns(6),
            DateField::new("birthDay", "Date de naissance")->setColumns(6),
            IntegerField::new("rewardPoints", "Points de fidélité")
                ->setFormTypeOptions(["disabled" => $disabled])
                ->setColumns(6)

        ];
    }
}
