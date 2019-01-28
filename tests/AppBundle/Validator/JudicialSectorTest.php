<?php

namespace Tests\AppBundle\Validator;

use AppBundle\Validator\Constraints\JudicialSector;
use Symfony\Component\Form\Exception\InvalidArgumentException;

class JudicialSectorTest extends ValidatorTestCase
{
    public function testCreateJudicialSector()
    {
        try {
            new JudicialSector();
        } catch (InvalidArgumentException $exception) {
            $this->assertEquals("Undefined option 'types'", $exception->getMessage());
        }

        try {
            new JudicialSector(['types' => 'type']);
        } catch (InvalidArgumentException $exception) {
            $this->assertEquals("Option 'type' must be array", $exception->getMessage());
        }

        $judicialSector = new JudicialSector(['types' => ['type1', 'type2']]);
        $this->assertInstanceOf(JudicialSector::class, $judicialSector);
        $this->assertEquals("Количество судебных участков должно быть: {{ count }}. Необходимо еще добавить судебные участки следующих типов: {{ types }}", $judicialSector->message);
        $this->assertEquals(['type1', 'type2'], $judicialSector->types);
    }
}
