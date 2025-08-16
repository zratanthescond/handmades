<?php

namespace App\Controller\Admin\Crud;

use App\Entity\Parrainage;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

class ParrainageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Parrainage::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInPlural("Parrainages")
            ->setEntityLabelInSingular("Parrainage")
            ->setDefaultSort(["createdAt" => "DESC"]);
    }


    public function configureActions(Actions $actions): Actions
    {

        $exportCVS = Action::new('export CVS', 'export CVS', 'fa fa-file-invoice')
            ->linkToCrudAction('renderInvoice')->setCssClass('btn btn-primary action-foo')->createAsGlobalAction();
        return $actions->disable(Action::DELETE, Action::NEW)->add(Crud::PAGE_INDEX, $exportCVS);
    }

    public function configureFields(string $pageName): iterable
    {
        return [

            DateTimeField::new("createdAt", "Date")->setFormTypeOptions(["disabled" => true]),
            AssociationField::new("fromUser", "Client")->setFormTypeOptions(["disabled" => true]),
            TextField::new("beneficiaryFullName", "Bénéficiaire")->onlyOnIndex(),
            EmailField::new("beneficiaryEmail", "Email")->setFormTypeOptions(["disabled" => true]),
            BooleanField::new("isRewarded", "Récompensé")->renderAsSwitch(false)->setFormTypeOptions(["disabled" => true])


        ];
    }


    public function renderInvoice(EntityManagerInterface $em)
    {


        $paginator = array(
            array('name' => 'aly', 'type' => 'male', 'age' => 13),
            array('name' => 'aly', 'type' => 'male', 'age' => 13),
            array('name' => 'aly', 'type' => 'male', 'age' => 13),
            array('name' => 'aly', 'type' => 'male', 'age' => 13)
        );
        $parrainage = $em->getRepository("App\Entity\Parrainage")->findBy(array(), array('createdAt' => 'DESC'));
        //dd($parrainage);
        // $data = $parrainage->iterate();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Parrainage exports');
        $columnsMap = [];
        $lineIndex = 2;
        foreach ($parrainage as $line) {
            //  dd($line);
            $data['Date'] = $line->getCreatedAt();
            $data['Client'] = $line->getFromUser()->getFullName();
            $data['Bénéficiaire'] = $line->getBeneficiaryFullName();
            $data['Email'] = $line->getBeneficiaryEmail();
            $data['Récompensé'] = $line->getIsRewarded() === true ? 'Oui' : 'Non';
            foreach ($data as $columnName => $columnValue) {
                if (is_int($columnIndex = array_search($columnName, $columnsMap))) {
                    //   dd($columnName);
                    $columnIndex++;
                } else {

                    $columnsMap[] = $columnName;
                    $columnIndex = count($columnsMap);
                }
                //dd($columnName);
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
    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
