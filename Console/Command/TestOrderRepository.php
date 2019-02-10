<?php
/**
 * @author Marcus Pettersen Irgens <marcus.irgens@visma.com>
 */

namespace Marcuspi\OrderRepositoryCli\Console\Command;

use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\OrderRepositoryInterfaceFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestOrderRepository extends Command
{
    /**
     * @var OrderRepositoryInterfaceFactory
     */
    private $orderRepositoryFactory;

    /**
     * @var State
     */
    private $appState;

    public function __construct(
        OrderRepositoryInterfaceFactory $orderRepositoryFactory,
        State $appState,
        $name = null
    ) {
        parent::__construct($name);
        $this->orderRepositoryFactory = $orderRepositoryFactory;
        $this->appState = $appState;
    }

    /**
     *
     */
    protected function configure()
    {
        $this->setName("test:order:repository")
            ->addArgument("entity_id", InputArgument::REQUIRED, "Order ID");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $orderId = (int) $input->getArgument("entity_id");

        // Emulate adminhtml area code
        $this->appState->emulateAreaCode(Area::AREA_ADMINHTML, function () use ($output, $orderId) {
            $orderRepository = $this->orderRepositoryFactory->create();
            try {
                $order = $orderRepository->get($orderId);
                $output->writeln("Found " . $order->getIncrementId());
            } catch (NoSuchEntityException $nse) {
                $output->writeln($nse->getMessage());
            } catch (LocalizedException $e) {
                if ($e->getMessage() == "Area code is not set") {
                    $output->writeln("<error>Area code is not set exception was thrown.</error>");
                } else {
                    throw $e;
                }
            }
        });
    }
}
