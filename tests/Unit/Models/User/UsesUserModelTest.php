<?php

namespace Handtuchsystem\Test\Unit\Models\User;

use Handtuchsystem\Models\BaseModel;
use Handtuchsystem\Models\User\UsesUserModel;
use Handtuchsystem\Test\Unit\Models\ModelTest;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsesUserModelTest extends ModelTest
{
    /**
     * @covers \Handtuchsystem\Models\User\UsesUserModel::user
     */
    public function testHasOneRelations()
    {
        /** @var UsesUserModel $contact */
        $model = new class extends BaseModel
        {
            use UsesUserModel;
        };

        $this->assertInstanceOf(BelongsTo::class, $model->user());
    }
}
