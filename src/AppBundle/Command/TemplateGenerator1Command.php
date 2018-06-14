<?php

namespace AppBundle\Command;

use AppBundle\Entity\Event;
use AppBundle\Entity\Flat;
use AppBundle\Entity\FlatEvent;
use AppBundle\Exception\DebtorException;
use AppBundle\Exception\SubscriberException;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TemplateGenerator1Command extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('template:generator1')
            ->setDescription('Генерация шаблонов. Прописа на кроне. Работает постоянно.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
//        /** @var array $processFlats */
//        $processFlats = $this->getTemplateGeneratorService()->getProcessFlats();
//
//        $currentDate = new \DateTime();
//
//        if (!count($processFlats)) {
//            $output->writeln("{$currentDate->format('d.m.Y H:i')}: Список обрабатываемых помещений пуст");
//
//            return;
//        }
//
//        /** @var Event|null $startEvent */
//        $startEvent = $this->getTemplateGeneratorService()->getStartEvent();
//
//        if (!$startEvent) {
//            $output->writeln("{$currentDate->format('d.m.Y H:i')}: Отсутствует стартовое событие");
//
//            return;
//        }
//
//        /** @var EntityManager $em */
//        $em = $this->getEntityManager();
//
//        /** @var Flat $processFlat */
//        foreach ($processFlats as $processFlat) {
//            //если еще небыло никаких событий генерация - устанавливаем первое событие
//            if (!count($processFlat->getFlatsEvents())) {
//                $flatEvent = new FlatEvent();
//                $flatEvent
//                    ->setFlat($processFlat)
//                    ->setEvent($startEvent)
//                    ->setDateGenerate(new \DateTime());
//
//                $processFlat->addFlatsEvent($flatEvent);
//
//                $em->persist($flatEvent);
//            } else {//иначе добаляем в список следующее событие
//                /** @var FlatEvent $currentFlatEvent */
//                $currentFlatEvent = $processFlat->getFlatsEvents()->last();
//
//                /** @var Event $currentEvent */
//                $currentEvent = $currentFlatEvent->getEvent();
//
//                //если не пришло время генерации документа
//                if ((new \DateTime())->diff($currentFlatEvent->getDateGenerate())->d < $currentEvent->getTimePerformAction()) {
//                    continue;
//                }
//
//                //если есть у текущего события родитель, добавляем его в список
//                if ($parentEvent = $currentEvent->getParent()) {
//                    //если текущее и следующее событие неюридическое, удаляем текущее событие
//                    if (!$currentEvent->getIsJudicial() && !$parentEvent->getIsJudicial()) {
//                        $processFlat->removeFlatsEvent($currentFlatEvent);
//                        $em->remove($currentFlatEvent);
//                    }
//
//                    //создаем объект родительского события
//                    $flatEvent = new FlatEvent();
//                    $flatEvent
//                        ->setFlat($processFlat)
//                        ->setEvent($parentEvent)
//                        ->setDateGenerate(new \DateTime());
//
//                    if ($parentEvent->getIsJudicial()) {
//                        $data = [
//                            'start_date'    =>  new \DateTime(),
//                            'end_date'      =>  new \DateTime()
//                        ];
//
//                        if (!$parentEvent->getTemplate()) {
//                            $data['confirm'] = false;
//                        }
//                        $flatEvent->setData($data);
//                    }
//
//                    $processFlat->addFlatsEvent($flatEvent);
//
//                    $em->persist($processFlat);
//                }
//            }
//
//            /** @var FlatEvent $flatEvent */
//            foreach ($processFlat->getFlatsEvents() as $flatEvent) {
//                try {
//                    $fileLinks = $this->getTemplateGeneratorService()->generateTemplate($processFlat, $flatEvent);
//                } catch (DebtorException $exception) {
//
//                } catch (SubscriberException $exception) {
//
//                } catch (\Exception $exception) {
//
//                }
//            }
//        }
//
//        return;
        //======
        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine.orm.doctrine_entity_manager');

        //список обрабатываемых помещений
        $flats = $em->getRepository('AppBundle:Flat')->findAll();

        /** @var Event|null $startEvent */
        $startEvent = $em->getRepository('AppBundle:Event')
            ->createQueryBuilder('event')
            ->where('event.isStart = :isStart')
            ->setParameter('isStart', true)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$startEvent) {
            throw new \Exception('Нет события');
        }

        /** @var Flat $flat */
        foreach ($flats as $flat) {
            //определение долга

            //если еще небыло никаких генерация - генерируем первое событие
            if (!count($flat->getFlatsEvents())) {
                //типо генерация
                $flatEvent = new FlatEvent();
                $flatEvent
                    ->setFlat($flat)
                    ->setEvent($startEvent)
                    ->setDateGenerate(new \DateTime());

                $em->persist($flatEvent);
            } else {
                /** @var FlatEvent $currentFlatEvent */
                $currentFlatEvent = $flat->getFlatsEvents()->last();
                /** @var Event $currentEvent */
                $currentEvent = $currentFlatEvent->getEvent();

                //если не пришло время генерации документа
                if ((new \DateTime())->diff($currentFlatEvent->getDateGenerate())->d < $currentEvent->getTimePerformAction()) {
                    continue;
                }

                //если есть у текущего события родитель, добавляем его в список
                if ($parentEvent = $currentEvent->getParent()) {
                    //если текущее и следующее событие неюридическое, удаляем текущее событие
                    if (!$currentEvent->getIsJudicial() && !$parentEvent->getIsJudicial()) {
                        $flat->removeFlatsEvent($currentFlatEvent);
                        $em->remove($currentFlatEvent);
                    }

                    $flatEvent = new FlatEvent();
                    $flatEvent
                        ->setFlat($flat)
                        ->setEvent($parentEvent)
                        ->setDateGenerate(new \DateTime());

                    if ($parentEvent->getIsJudicial()) {
                        $data = [
                            'start_date'    =>  new \DateTime(),
                            'end_date'      =>  new \DateTime()
                        ];

                        if (!$parentEvent->getTemplate()) {
                            $data['confirm'] = false;
                        }
                        $flatEvent->setData($data);
                    }

                    $flatEvent->addData('time_perform_action', $currentEvent->getTimePerformAction());

                    $flat->addFlatsEvent($flatEvent);

                    $em->persist($flat);
                }

                /** @var FlatEvent $flatEvent */
                foreach ($flat->getFlatsEvents() as $flatEvent) {
                    //генерация документов
                    $flatEvent->setDateGenerate(new \DateTime());

                    $em->persist($flatEvent);
                }
            }

            $em->flush();
        }

        $output->writeln('Готово');
    }

    /**
     * @return \AppBundle\Service\TemplateGenerator|object
     */
    private function getTemplateGeneratorService()
    {
        return $this->getContainer()->get('app.service.template_generator');
    }

    /**
     * @return EntityManager|object
     */
    private function getEntityManager()
    {
        return $this->getContainer()->get('doctrine.orm.doctrine_entity_manager');
    }

    /**
     * @return \AppBundle\Service\FlatLogger|object
     */
    private function getFlatLogger()
    {
        return $this->getContainer()->get('app.service.flat_logger');
    }
}