<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Traits\ActsAsUser;

abstract class TestCase extends BaseTestCase
{
    use ActsAsUser;
    use CreatesApplication;
    use RefreshDatabase;
    use WithFaker;
}
