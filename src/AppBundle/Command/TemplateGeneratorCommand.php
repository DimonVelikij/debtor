<?php

namespace AppBundle\Command;

use AppBundle\Entity\Flat;
use AppBundle\Service\FlatLogger;
use AppBundle\Service\TemplateGenerator;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TemplateGeneratorCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('template:generator')
            ->setDescription('Генерация шаблонов. Прописа на кроне. Работает постоянно.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var TemplateGenerator $templateGenerator */
        $templateGenerator = $this->getContainer()->get('app.service.template_generator');

        /** @var array $flats */
        $flats = $templateGenerator->getProcessFlats();

        $currentDate = new \DateTime();

        if (!count($flats)) {
            $output->writeln("{$currentDate->format('d.m.Y H:i')}: Список обрабатываемых помещений пуст");

            return;
        }

        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine.orm.doctrine_entity_manager');

        /** @var FlatLogger $flatLogger */
        $flatLogger = $this->getContainer()->get('app.service.flat_logger');

        /** @var Flat $flat */
        foreach ($flats as $flat) {
            //если разница в днях между текущим временем и последним обновлением записи помещения меньше, чем указано у шаблона
            if ($currentDate->diff($flat->getLastDateGenerate())->d < $flat->getTemplate()->getTimePerformAction()) {
                continue;
            }

            $delimiter = "==================================================\n";

            $output->writeln($flatLogger->getLogPrefix($flat));

            if (!$flat->getTemplate()->getIsJudicial() && !count($flat->getSubscribers())) {
                //пишем логи
                $log = 'Отсутствует список абонетов для генерации шаблона "' . $flat->getTemplate()->getName() . '", который является досудебным. Для генерации досудебного шаблона нужно наличие хотя бы одного абонента.';
                $output->writeln($log);
                $output->writeln($delimiter);
                $flatLogger->log($flat, $log);
                //проставляем ошибку генерации у помещения
                $flat->setIsGenerateErrors(true);
                $em->persist($flat);
                $em->flush();
                continue;
            }

            if ($flat->getTemplate()->getIsJudicial() && !count($flat->getDebtors())) {
                //пишем логи
                $log = 'Отсутствует список должников для генерации шаблона "' . $flat->getTemplate()->getName() . '", который является судебным. Для генерации судебного шаблона нужно наличие хотя бы одного должника.';
                $output->writeln($log);
                $output->writeln($delimiter);
                $flatLogger->log($flat, $log);
                //проставляем ошибку генерации у помещения
                $flat->setIsGenerateErrors(true);
                $em->persist($flat);
                $em->flush();
                continue;
            }

            $log = 'Генерация шаблона "' . $flat->getTemplate()->getName() . '"';
            $output->writeln($log);
            $flatLogger->log($flat, $log);

            try {
                $fileLink = $templateGenerator->generateTemplate($flat);
            } catch (\Exception $e) {
                //пишем логи
                $log = 'Во время генерации шаблона "' . $flat->getTemplate()->getName() . '" возникли ошибки. "' . $e->getMessage() . '". Требуется вмешательство разработчика.';
                $output->writeln($log);
                $output->writeln($delimiter);
                $flatLogger->log($flat, $log);
                //проставляем ошибку генерации у помещения
                $flat->setIsGenerateErrors(true);
                $em->persist($flat);
                $em->flush();
                continue;
            }

            $log = 'Генерация шаблона "' . $flat->getTemplate()->getName() . '" успешно завершена. <a href="' . $fileLink . '">ссылка на файл</a>';
            $output->writeln($log);
            $output->writeln($delimiter);
            $flatLogger->log($flat, $log);

            if ($flat->getTemplate()->getParent()) {
                //если у текущего шаблона есть родительский, подставляем его в помещение
                $flat->setTemplate($flat->getTemplate()->getParent());
            } else {
                //если у текущего шаблона нет родительского, ставим у помещения пометку archive - все шаблоны сгенерированы
                $flat->setArchive(true);
            }

            //устанавливаем дату последней генарции текущую
            $flat->setLastDateGenerate(new \DateTime());

            $em->persist($flat);
            $em->flush();
        }
    }
}