<?php

namespace Core\Database;

use Core\Database\Concerns\HasAttributes;
use Core\Database\Concerns\HasBooting;
use Core\Database\Concerns\HasCRUD;
use Core\Database\Concerns\HasEvents;
use Core\Database\Concerns\HasGlobalScopes;
use Core\Database\Concerns\HasQueries;
use Core\Database\Concerns\HasRelationships;
use Core\Database\Concerns\HasObservers;
use Core\Database\Concerns\HasTimestamps;
use Core\Database\Concerns\GuardsAttributes;

abstract class Model {


    use HasAttributes;
    use HasBooting;
    use HasCRUD;
    use HasEvents;
    use HasGlobalScopes;
    use HasQueries;
    use HasRelationships;
    use HasObservers;
    use HasTimestamps;
    use GuardsAttributes;


    protected static string $table;
    protected static string $primaryKey = 'id';

}

