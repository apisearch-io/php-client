<?php

/*
 * This file is part of the Apisearch PHP Client.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */

declare(strict_types=1);

namespace Apisearch\Tests\Repository;

use Apisearch\Model\Item;
use Apisearch\Query\Query;
use Apisearch\Repository\Repository;
use Apisearch\Repository\TransformableRepository;
use Apisearch\Result\Result;
use Apisearch\Transformer\Transformer;
use PHPUnit\Framework\TestCase;

/**
 * Class TransformableRepositoryTest.
 */
class TransformableRepositoryTest extends TestCase
{
    public function testResultTransformer()
    {
        $repository = $this->createMock(Repository::class);
        $result = new Result(
            Query::createMatchAll()->identifyWith('123'),
            1, 1
        );
        $result->addItem(Item::createFromArray(['uuid' => ['id' => '1', 'type' => 'a']]));
        $repository->method('query')->willReturn($result);

        $transformer = $this->createMock(Transformer::class);
        $transformer->method('fromItem')->willReturn(['a']);
        $transformer->method('fromItems')->willReturn([['a']]);

        $transformableRepository = new TransformableRepository($repository, $transformer);
        $result = $transformableRepository->query(Query::createMatchAll());

        $this->assertEquals(
            [['a']], $result->getItems()
        );
    }
}
