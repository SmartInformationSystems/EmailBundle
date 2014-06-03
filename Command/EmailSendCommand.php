<?php

namespace SmartInformationSystems\EmailBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use SmartInformationSystems\EmailBundle\Spool\SmartInformationSystemsEmailSpool;

/**
 * Отправка тестового письма.
 *
 */
class EmailSendCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('sis_email:send')
            ->setDescription('Sending email from spool')
            ->addOption('limit', 100, InputOption::VALUE_OPTIONAL, 'Messages limit for sending')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mailer     = $this->getContainer()->get('mailer');
        $transport  = $mailer->getTransport();

        if ($transport instanceof \Swift_Transport_SpoolTransport) {
            $spool = $transport->getSpool();
            if ($spool instanceof SmartInformationSystemsEmailSpool) {
                if ($limit = $input->getOption('limit')) {
                    $spool->setMessageLimit($limit);
                }
            }

            $spool->flushQueue($this->getContainer()->get('swiftmailer.transport.real'));
        }
    }
}
