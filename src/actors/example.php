<?php

namespace Example\Actors;

use Dapr\Actor;
use Dapr\ActorState;
use Dapr\IActor;

class ExampleActor implements IActor
{
    use Actor;
    use ActorState;

    private $id;
    private $state;

    public function __construct($id, $state) {
        $this->id = $id;
        $this->state = $state;
    }

    function get_id()
    {
        return $this->id;
    }

    function remind(string $name, $data): void
    {
        // TODO: Implement remind() method.
    }

    function on_activation(): void
    {
        // TODO: Implement on_activation() method.
    }

    function on_deactivation(): void
    {
        // TODO: Implement on_deactivation() method.
    }
}
