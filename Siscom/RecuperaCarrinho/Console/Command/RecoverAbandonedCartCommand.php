<?php

namespace Siscom\RecuperaCarrinho\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Siscom\RecuperaCarrinho\Model\RecoverAbandonedCart;

class RecoverAbandonedCartCommand extends Command
{
    /**
     * @var RecoverAbandonedCart
     */
    protected $recovery;

    public function __construct(RecoverAbandonedCart $recovery)
    {
        $this->recovery = $recovery;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('recover_abandoned_cart')
            ->setDescription('Recover abandoned carts');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $return = $this->recovery->execute();
        if (!$return) {
            $output->writeln('Modulo não Habilitado, verifique em: Admin -> Store -> Configuration -> Recupera Carrinho');
        }

        $output->writeln('Done');
    }

}

