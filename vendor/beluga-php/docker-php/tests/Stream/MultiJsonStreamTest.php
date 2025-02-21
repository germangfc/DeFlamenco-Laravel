<?php

declare(strict_types=1);

namespace Docker\Tests\Stream;

use Docker\API\Model\BuildInfo;
use Docker\Stream\MultiJsonStream;
use Docker\Tests\TestCase;
use Nyholm\Psr7\Stream;
use Symfony\Component\Serializer\SerializerInterface;

class MultiJsonStreamTest extends TestCase
{
    public function jsonStreamDataProvider()
    {
        return [
            [
                '{}{"abc":"def"}',
                ['{}', '{"abc":"def"}'],
            ],
            [
                '{"test": "abc\"\""}',
                ['{"test":"abc\"\""}'],
            ],
            [
                '{"test": "abc\"{{-}"}',
                ['{"test":"abc\"{{-}"}'],
            ],
        ];
    }

    /**
     * @dataProvider jsonStreamDataProvider
     */
    public function testReadJsonEscapedDoubleQuote(string $jsonStream, array $jsonParts): void
    {
        $stream = Stream::create($jsonStream);
        $stream->rewind();

        $serializer = $this->getMockBuilder(SerializerInterface::class)
            ->getMock();

        $serializer
            ->expects($this->exactly(\count($jsonParts)))
            ->method('deserialize')
                ->withConsecutive(...array_map(fn ($part) => [$part, BuildInfo::class, 'json', []], $jsonParts))
        ;

        $stub = $this->getMockForAbstractClass(MultiJsonStream::class, [$stream, $serializer]);
        $stub->expects($this->any())
            ->method('getDecodeClass')
            ->willReturn('BuildInfo');

        $stub->wait();
    }
}
