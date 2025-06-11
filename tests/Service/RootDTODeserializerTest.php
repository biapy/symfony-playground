<?php

/**
 * @copyright 2025 Biapy
 * @license MIT
 */

declare(strict_types=1);

use App\DTO\RootDTO;
use App\DTO\ChildDTO;
use PHPUnit\Framework\TestCase;
use App\Service\RootDTODeserializer;
use App\Service\RootDTODeserializerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RootDTODeserializerTest extends KernelTestCase
{
    private RootDTODeserializerInterface $deserializer;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $container = self::getContainer();
        $this->serializer = $container->get(SerializerInterface::class);
        $this->deserializer = new RootDTODeserializer($this->serializer);
    }


    public function testDeserializeWithoutChild()
    {
        $json = '{"name": "Test"}';

            $result = $this->deserializer->deserialize($json);

        $this->assertInstanceOf(RootDTO::class, $result);
        $this->assertEquals("Test", $result->name);
        $this->assertNull($result->child);
    }

    public function testDeserializeWithChild()
    {
        $json = '{"name": "Test", "child-name": "Child"}';

        $result = $this->deserializer->deserialize($json);

        $this->assertInstanceOf(RootDTO::class, $result);
        $this->assertEquals("Test", $result->name);
        $this->assertInstanceOf(ChildDTO::class,$result->child);
        $this->assertEquals("Child", $result->child->name);
    }
}
