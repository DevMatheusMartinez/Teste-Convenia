<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\EmployeeStore;
use Tests\TestCase;

class EmployeeStoreTest extends TestCase
{
    public function testIfTheEmployeeStoreRulesAreTheSameAsExpected():void
    {
        $request = new EmployeeStore();

        $this->assertEquals(
            $request->rules(),
            [
                'file' => ['required','file','mimetypes:text/plain,text/csv']
            ]
        );
    }
}
