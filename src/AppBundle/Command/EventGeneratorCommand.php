<?php

namespace AppBundle\Command;

use AppBundle\Entity\Flat;
use AppBundle\EventGenerator\Generator\GeneratorAggregate;
use AppBundle\Service\FlatLogger;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EventGeneratorCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('event:generator')
            ->setDescription('Генерация событий');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var GeneratorAggregate $generator */
        $generator = $this->getContainer()->get('app.generator.aggregate');

        /** @var array $processFlats */
        $processFlats = $this->getProcessFlats();

        $currentDate = new \DateTime();

        if (!count($processFlats)) {
            $output->writeln($currentDate->format('d.m.Y H:i') . ' Список обрабатываемых помещений пуст');

            return;
        }

        /** @var FlatLogger $flatLogger */
        $flatLogger = $this->getContainer()->get('app.service.flat_logger');

        $output->writeln($currentDate->format('d.m.Y H:i') . ' Количество помещения для обработки: ' . count($processFlats));

        /** @var Flat $processFlat */
        foreach ($processFlats as $processFlat) {
            $output->writeln('Обработка помещения #' . $processFlat->getId());
            try {
                $generator->processFlat($processFlat);
                $output->writeln('Успешная обработка помещения #' . $processFlat->getId());
            } catch (\Exception $e) {
                $output->writeln('Ошибка при обработке помещения #' . $processFlat->getId() . '. ' . $e->getMessage());
            }
        }

        $output->writeln($currentDate->format('d.m.Y H:i') . ' Обработка помещения завершена');
        $output->writeln($flatLogger->getDelimiter());
    }

    /**
     * получение помещений для обработки
     * @return array
     */
    private function getProcessFlats()
    {
        /** @var QueryBuilder $flatQueryBuilder */
        $flatQueryBuilder = $this->getContainer()->get('doctrine.orm.doctrine_entity_manager')
            ->getRepository('AppBundle:Flat')
            ->createQueryBuilder('flat');

        return $flatQueryBuilder
            ->where('flat.isGenerateErrors = :isGenerateErrors')
            ->andWhere('flat.archive = :isArchive')
            ->setParameters([
                'isGenerateErrors'  =>  false,
                'isArchive'         =>  false
            ])
            ->getQuery()
            ->getResult();
    }
}