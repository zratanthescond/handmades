<?php

namespace App\Command;

use App\Entity\AramexTracking as EntityAramexTracking;
use App\Event\OrderIsDeliveredEvent;
use App\Repository\AramexTrackingRepository;
use App\Service\Aramex\AramexTracking;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use App\Service\Aramex\AramexTrackingUpdateCodeResolver;


class DeliveryAramexUpdateCommand extends Command
{
    private $repo;

    private $aramexTrackingApi;

    private $em;

    private $normalizer;

    private $dispatcher;

    public function __construct(
        AramexTrackingRepository $repo,
        AramexTracking $aramexTrackingApi,
        EntityManagerInterface $em,
        NormalizerInterface $normalizer,
        EventDispatcherInterface $dispatcher
    ) {
        $this->repo = $repo;

        $this->em = $em;

        $this->normalizer = $normalizer;

        $this->dispatcher = $dispatcher;

        $this->aramexTrackingApi = $aramexTrackingApi;

        parent::__construct();
    }

    protected static $defaultName = 'delivery:aramex:update';
    protected static $defaultDescription = 'Update Aramex Trackings';


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $trackings = $this->repo->findUndeliveredTrackings(8);

        $trackingIds = array_map(

            fn (EntityAramexTracking $tracking) => $tracking->getShippement()->getTrackingId(),
            $trackings
        );

        if (count($trackingIds)) {

            $trackingResults = $this->aramexTrackingApi->trackMultiple($trackingIds);

            foreach ($trackingResults as $tracking) {

                $waybillNumber = $tracking->getWaybillNumber();

                $updateCode = $tracking->getUpdateCode();

                $trackingEntity = $this->repo->findOneBy(["waybillNumber" => $waybillNumber]);

                if ($updateCode === AramexTrackingUpdateCodeResolver::DELIVERED_STATUS) {

                    $order = $trackingEntity->getShippement()->getClientOrder();

                    $event = new OrderIsDeliveredEvent($order);

                    $this->dispatcher->dispatch($event, OrderIsDeliveredEvent::EVENT_NAME);
                }

                $trackingData = $this->normalizer->normalize($tracking);

                $trackingEntity->setData($trackingData)->setUpdateCode($updateCode);

                $this->em->persist($trackingEntity);
            }

            $this->em->flush();
        }



        $nbr = count($trackingIds);

        $io->success(sprintf("%s tracking(s) updated", $nbr));

        return Command::SUCCESS;
    }
}
