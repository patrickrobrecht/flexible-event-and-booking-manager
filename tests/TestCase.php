<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Traits\ActsAsUser;
use Tests\Traits\GeneratesTestData;

abstract class TestCase extends BaseTestCase
{
    use ActsAsUser;
    use GeneratesTestData;
    use RefreshDatabase;
    use WithFaker;
}
