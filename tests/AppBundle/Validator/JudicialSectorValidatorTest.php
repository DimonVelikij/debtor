<?php

namespace Tests\AppBundle\Validator;

use AppBundle\Validator\Constraints\JudicialSector;
use AppBundle\Validator\Constraints\JudicialSectorValidator;

class JudicialSectorValidatorTest extends ValidatorTestCase
{
    /**
     * все ок
     */
    public function testJudicialSectorValidate()
    {
        $judicialSectorConstraint = new JudicialSector([
            'types' => [
                \AppBundle\Entity\JudicialSector::DISTRICT
            ]
        ]);

        $context = $this->getExecutionContextOkMock();

        $judicialSectorValidator = new JudicialSectorValidator();
        $judicialSectorValidator->initialize($context);

        $judicialSectorDistrict = $this->getEntityManager()->getRepository('AppBundle:JudicialSector')
            ->findOneBy(['type' => \AppBundle\Entity\JudicialSector::DISTRICT]);
        $formData = $this->getPersistentCollectionMock(\AppBundle\Entity\JudicialSector::class, [$judicialSectorDistrict]);

        $judicialSectorValidator->validate($formData, $judicialSectorConstraint);
    }

    /**
     * при добавлении указали только Районный, а нужно Районный и Мировай
     */
    public function testJudicialSectorInvalidate()
    {
        $judicialSectorConstraint = new JudicialSector([
            'types' => [
                \AppBundle\Entity\JudicialSector::DISTRICT,
                \AppBundle\Entity\JudicialSector::MAGISTRATE
            ]
        ]);

        $context = $this->getExecutionContextErrorMock($judicialSectorConstraint->message, [
            '{{ count }}'   =>  '2',
            '{{ types }}'   =>  'Мировой'
        ]);

        $judicialSectorValidator = new JudicialSectorValidator();
        $judicialSectorValidator->initialize($context);

        $judicialSectorDistrict = $this->getEntityManager()->getRepository('AppBundle:JudicialSector')
            ->findOneBy(['type' => \AppBundle\Entity\JudicialSector::DISTRICT]);
        $formData = $this->getPersistentCollectionMock(\AppBundle\Entity\JudicialSector::class, [$judicialSectorDistrict]);

        $judicialSectorValidator->validate($formData, $judicialSectorConstraint);
    }
}
